<?php
    // Haetaan lomakkeiden yhteinen koodi:
    if (file_exists(dirname(__DIR__,1)."/rutiinit/lomake.php")) {
        include(dirname(__DIR__,1)."/rutiinit/lomake.php");
    }
    
    // Sähköpostin vahvistamissivun (verify_email.php) pääfunktio, joka tarkistaa osoiteriviltä 
    // löytyvän poletin ja jos poletti on ok, asettaa käyttäjätilin aktivoiduksi:
    function vahvista_ja_aktivoi() {
        global $polku; // juurikansion polku näkyväksi

        // Luodaan virheviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $yhteysvirhe, $poletti_vanhentunut, $jo_vahvistettu;
        global $vahvistaminen_ok, $virhe_aktivoinnissa;

        // Haetaan poletti osoiteriviltä (jos ei löydy, poletti olkoon tyhjä merkkijono):
        $poletti = $_GET["token"] ?? "";
        
        // Jos käyttäjän selaimelta on joku kirjautuneena sisään, keskeytetään:
        if (isset($_SESSION["loggedin"]) and $_SESSION["loggedin"]) {
            $yhteysvirhe = virhetagit("Olet kirjautuneena käyttäjänä ".$_SESSION["sposti"].
                ". Tämä tili on jo aktivoitu. Jos haluat aktivoida toisen käyttäjätilin,
                kirjaudu ensin <a href='".$polku."/rutiinit/logout.php?ulos=1'>ulos</a>.");
            return;
        }
        // Jos polettia ei löytynyt osoiteriviltä:
        if (!$poletti) {
            if (!isset($_POST["tilaa_poletti"]) and !isset($_GET["uusi"])) { 
                // Jos sivulle on saavuttu pyytämättä uutta polettia:
                $virhe_aktivoinnissa = virhetagit("Käyttäjätili otetaan käyttöön (aktivoidaan)
                    vahvistamalla siihen liitetty sähköpostiosoite. Voit vahvistaa sähköpostiosoitteesi
                    klikkaamalla linkkiä, jonka lähetimme antamaasi osoitteeseen.<br><br>
                    Jos et löydä sähköpostia, tarkista roskapostikansiosi.<br>
                    Tarvittaessa voit tilata uuden linkin täyttämällä tietosi alle.");
            }
        } else { // Jos poletti löytyi osoiteriviltä, haetaan vastaava tietue tietokannasta:
            try {$polettitulos = hae_poletti($poletti, "activation");}
            catch(Exception $voivoi) { // Jos haku ei onnistunut:
                kasittele_poikkeus($voivoi);
                $yhteysvirhe = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. 
                    Yritä ladata sivu uudelleen.");
                return; // keskeytetään funktion suoritus tähän
            }
            if ($polettitulos->num_rows == 0) { // Jos haulla ei löytynyt tietuetta:
                $virhe_aktivoinnissa = virhetagit("Tilin aktivointi ei onnistunut. Varmista, että
                    käytät linkkiä, joka löytyy viimeisimmästä lähettämästämme vahvistussähköpostista
                    ja että kopioit koko linkin osoiteriville.<br><br>
                    Tarvittaessa voit tilata uuden vahvistuslinkin täyttämällä tietosi alle. 
                    Jos et löydä sähköpostiamme, tarkista roskapostikansiosi.");
            } else { // Jos tietue löytyi:
                $polettitietue = $polettitulos->fetch_assoc();
                $user_id = $polettitietue["user_id"];
                // Tarkistetaan, onko käyttäjätili jo aktivoitu
                // (siltä varalta, jos on tapahtunut yhteysvirhe ja sivu on ladattu uudestaan):
                try {$aktivointi_tehty = aktivoitu($user_id);} 
                catch(Exception $voivoi) { // Jos taas tuli yhteysvirhe:
                    kasittele_poikkeus($voivoi);
                    $yhteysvirhe = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. 
                        Yritä ladata sivu uudelleen.");
                    return; // keskeytetään funktion suoritus tähän
                }
                if ($aktivointi_tehty) {
                    $vahvistaminen_ok = ok_tagit("Käyttäjätilisi on aktivoitu.<br>Kirjaudu sisään
                        <a href='".$polku."/kayttajahallinta/login.php'>täältä</a>!");
                    try {// Yritetään poistaa poletti activation-taulusta:
                        poista_tietueet($user_id, 'activation');
                    } catch(Exception $voivoi) { // Jos poisto ei onnistunut:
                        kasittele_poikkeus($voivoi);
                    }
                // Jos käyttäjätiliä ei ole aktivoitu, tarkistetaan poletin voimassaolo:
                } elseif ($polettitietue["expires"] < date("Y-m-d H:i:s", time())) { // Jos ei voimassa:
                    $poletti_vanhentunut = virhetagit("Linkki on vanhentunut. Täytä tietosi alle, niin lähetämme
                        sinulle uuden.");
                } else { // Jos poletti on voimassa:
                    try { // Yritetään asettaa käyttäjätili aktivoiduksi:
                        aktivoi_tili($user_id);
                        $vahvistaminen_ok = ok_tagit("Vahvistaminen onnistui ja käyttäjätilisi on nyt aktivoitu.<br>
                            Kirjaudu sisään <a href='".$polku."/kayttajahallinta/login.php'>täältä</a>!");
                    } catch(Exception $voivoi) { // Jos muutos ei onnistunut:
                        kasittele_poikkeus($voivoi);
                        $virhe_aktivoinnissa = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. 
                            Yritä ladata sivu uudestaan.");
                        return; // keskeytetään funktion suoritus tähän
                    }
                    try { // Yritetään poistaa poletti activation-taulusta:
                        poista_tietueet($user_id, 'activation');
                    } catch(Exception $voivoi) { // Jos poisto ei onnistunut:
                        kasittele_poikkeus($voivoi);
                        // HUOM ajoittain voi olla tarpeen tehdä tietokantaan DELETE-käsky, jolla poistetaan
                        // jo aktivoiduilta käyttäjätileiltä virheellisesti jääneet aktivointipoletit.
                    }
                }
            }  
        }
    }

    // Lisäfunktio, joka tarkistaa uuden aktivointipoletin tilauslomakkeen
    // ja jos syötteet ovat ok, lähettää käyttäjän sähköpostiin uuden poletin:
    function tilaa_uusi_poletti() {
        global $polku; // juurikansion polku näkyväksi

        // Luodaan onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $tarkista_tilaus, $poletti_tilattu;
        
        // Luodaan muuttujat käyttäjän antamia syötteitä varten:
        global $poletti_sp;
        $salasana = "";

        // Loput funktiosta suoritetaan vain, jos on painettu Tilaa linkki -nappia:
        if (isset($_POST["tilaa_poletti"])) {
            $poletti_sp = strip_tags(trim($_POST["sposti"]));
            $salasana = strip_tags(trim($_POST["salasana"]));

            // Tarkistetaan, ovatko syötteet oikeanmuotoisia:
            if (!empty($poletti_sp) and !empty($salasana) 
            and filter_var($poletti_sp, FILTER_VALIDATE_EMAIL)) {
                try {$hakutulos = hae_kayttaja($poletti_sp, "user");} // Jos ovat, haetaan käyttäjätili
                catch(Exception $voivoi) { // Jos haussa tapahtui virhe:
                    kasittele_poikkeus($voivoi);
                    $tarkista_tilaus = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. Yritä uudelleen.");
                    return; // keskeytetään funktion suoritus tähän
                }
                if ($hakutulos->num_rows == 0) { // Jos haulla ei löytynyt käyttäjätiliä:
                    $tarkista_tilaus = virhetagit("Tällä sähköpostiosoitteella ei ole 
                        rekisteröity käyttäjätiliä. Linkkiä ei lähetetty.");
                } else { // Jos haulla löytyi käyttäjätili, tutkitaan tulokset:
                    $tili = $hakutulos->fetch_assoc();
                    if ($tili["activated"]=="1") { // Jos tili on aktivoitu:
                        $tarkista_tilaus = ok_tagit("Tämä sähköpostiosoite on jo vahvistettu ja
                            käyttäjätili aktivoitu. Kirjaudu tilillesi
                            <a href='".$polku."/kayttajahallinta/login.php'>täältä</a>.");
                    } elseif (!password_verify($salasana, $tili["passhash"])) {
                        // Jos annettu salasana on väärä:
                        $tarkista_tilaus = virhetagit("Tarkista sähköpostiosoite ja salasana. 
                            Linkkiä ei lähetetty.");
                    } else {// Jos tili on aktivoimatta ja salasana oikein:
                        // Poistetaan mahdolliset aiemmat poletit:
                        try {poista_tietueet($tili["user_id"], "activation");}
                        catch(Exception $voivoi) { // Jos poistossa tapahtui virhe:
                            kasittele_poikkeus($voivoi);
                            $tarkista_tilaus = virhetagit("Tietokantayhteydessä tapahtui virhe.
                                Yritä hetken kuluttua uudelleen.");
                        }
                        // Yritetään lisätä 30 minuutissa vanheneva poletti activation-tauluun:
                        if (empty($tarkista_tilaus)) {
                            $poletti = luo_poletti();
                            try {lisaa_poletti($tili["user_id"], $poletti, 1800, "activation");}
                            catch(Exception $voivoi) { // Jos lisäys ei onnistunut:
                                kasittele_poikkeus($voivoi);
                                $tarkista_tilaus = virhetagit("Tietokantayhteydessä tapahtui virhe.
                                    Yritä hetken kuluttua uudelleen.");
                            }
                        }
                        if (empty($tarkista_tilaus)) { // Jos poletin lisäys onnistui:
                            // Yritetään lähettää vahvistussähköposti:
                            $posti_lahetetty = laheta_vahvistusviesti($poletti_sp, $poletti);
                            if (!$posti_lahetetty) { // Jos lähetys ei onnistunut:
                                $tarkista_tilaus = virhetagit("Linkin lähettäminen ei valitettavasti
                                    onnistunut. Yritä hetken kuluttua uudelleen.");
                            } else { // Jos lähetys onnistui:
                                $poletti_tilattu = ok_tagit("Uusi linkki on lähetetty sähköpostiisi.
                                    Linkki vanhenee 30 minuutin päästä.");
                            }
                            // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                            $tilaus_sp = "";
                            $salasana = "";            
                        }
                    }
                }
            } else { // Jos kenttien syötteissä oli vikaa:
                $tarkista_tilaus = virhetagit("Tarkista sähköpostiosoite ja salasana. Linkkiä ei lähetetty.");
            }
        }
    }
?>