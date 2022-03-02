<?php
    // Haetaan lomakkeiden yhteinen koodi:
    if (file_exists(dirname(__DIR__,1)."/rutiinit/lomake.php")) {
        include(dirname(__DIR__,1)."/rutiinit/lomake.php");
    }

    // Kirjautumislomakkeen (login.php) pääfunktio, joka tarkistaa kirjautumislomakkeella annetut 
    // syötteet ja jos ne ovat ok, kirjaa käyttäjän sisään:
    function tarkista_ja_kirjaudu() {
        global $polku; // juurikansion polku näkyväksi

        // Luodaan virheviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $tilia_ei_ole, $sposti_vahvistamatta, $vaara_salasana;
        global $sposti_ei_kelpaa, $salasana_puuttuu, $yhteysvirhe;

        // Luodaan taulukko käyttäjän antamia syötteitä varten:
        global $syotteet;
        $syotteet = array("sposti"=>"","salasana"=>"","muista"=>"");
        
        // Loput funktiosta suoritetaan vain, jos on painettu Kirjaudu-nappia:
        if (isset($_POST["kirjaudu"])) {
            foreach ($syotteet as $kentta => $arvo) { // Siistitään syötteet:
                if (isset($_POST[$kentta])) {
                    $syotteet[$kentta] = strip_tags(trim($_POST[$kentta]));
                }
            } // Tarkistetaan, onko salasanakenttä täytetty:
            if (empty($syotteet["salasana"])) {
                $salasana_puuttuu = virhetagit("Kirjoita tähän Neilikka-tilisi salasana.");
            }
            // Sähköpostiosoitteeseen liittyvät tarkistukset:
            if (empty($syotteet["sposti"])) { // Jos sposti-kenttä on tyhjä:
                $voi_kirjautua = false;
                $sposti_ei_kelpaa = virhetagit("Kirjoita tähän sähköpostiosoitteesi.");                
            } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) { 
                // Jos sposti on syötetty, mutta ei ole kelvollinen sähköpostiosoite:
                    $voi_kirjautua = false;
                    $sposti_ei_kelpaa = virhetagit("Tarkista antamasi sähköpostiosoite.");
            } elseif (empty($salasana_puuttuu)) {
                // Jos sposti kelpaa ja salasana on annettu, etsitään käyttäjää tietokannasta:
                try {$hakutulos = hae_kayttaja($syotteet["sposti"], "user");}
                catch(Exception $voivoi) { // Jos haku ei onnistunut:
                    kasittele_poikkeus($voivoi);
                    $yhteysvirhe = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. Yritä uudelleen.");
                    return; // keskeytetään funktion suoritus tähän
                }
                if ($hakutulos->num_rows == 0) { // Jos haulla ei löytynyt käyttäjätiliä:
                    $tilia_ei_ole = virhetagit("Antamallasi sähköpostiosoitteella ".$syotteet["sposti"].
                        " ei löydy käyttäjätiliä.");
                    // Tyhjennetään syötteet, jotta samat tiedot eivät täyty lomakkeelle:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                } else { // Jos haulla löytyi käyttäjätili, tutkitaan tulokset:
                    $tili = $hakutulos->fetch_assoc();
                    if ($tili["activated"]=="0") { // Jos tili on aktivoimatta:
                        $sposti_vahvistamatta = virhetagit("Tilisi sähköpostiosoite on vahvistamatta.
                        Sähköpostiisi on lähetetty aktivointilinkki.<br><br>Jos et löydä sähköpostia
                        (tarkistathan myös roskapostikansiosi), voit pyytää uuden linkin
                        <a href='".$polku."/kayttajahallinta/verify_email.php?uusi=1'>täältä</a>.");
                    } elseif (!password_verify($syotteet["salasana"], $tili["passhash"])) {
                        // Jos annettu salasana on väärä:
                        $vaara_salasana = virhetagit("Sähköpostiosoite tai salasana on väärä. 
                            Yritä uudelleen.");
                        // HUOM PITÄISIKÖ LASKEA VÄÄRÄT YRITYKSET JA 5 KERRAN JÄLKEEN LUKITA TILI?
                        $syotteet["salasana"] = ""; // Tyhjennetään salasanakenttä
                    } else { // Jos tili on aktivoitu ja salasana on oikein:
                        if (!session_id()) {session_start();} // aloitetaan istunto, jos ei vielä ole aloitettu
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $tili["user_id"];
                        $_SESSION["sposti"] = $tili["email"];
                        if (isset($_SESSION["aiottu_sivu"])) {
                            $seuraava_sivu = $_SESSION["aiottu_sivu"]."?tervetuloa=1";
                                // GET-parametri "tervetuloa" opastaa seuraavaa sivua tulostamaan tervetulotekstin
                            unset($_SESSION["aiottu_sivu"]);
                            header("location: $seuraava_sivu");
                            exit;
                        }
                        header("location: $polku/kayttajahallinta/omasivu.php?tervetuloa=1");
                    }
                }
            }
        }
    }
?>