<?php
    // Luodaan tietokantayhteys (viittauksessa ei saa olla http-alkua, joten $polku ei käy):
    if (file_exists(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php")) {
        include(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php");
        if (!isset($yhteys)) {$yhteys = db_yhteys();}
    }
    // Haetaan PHPMaileriin perustuva posti.php:
    if (file_exists(dirname(__DIR__,1)."/rutiinit/posti.php")) {
        include(dirname(__DIR__,1)."/rutiinit/posti.php");
    }

    // Apufunktio, joka lisää tekstin ympärille lomakevirhe-luokan p-tägit:
    function lomaketagit($teksti, $lisaluokka = "") {
        return "<p class='lomakevirhe $lisaluokka'>$teksti</p>";
    }
    // Apufunktio, joka lisää tekstin ympärille lomake_ok-luokan p-tägit:
    function ok_tagit($teksti) {
        return "<p class='lomake_ok'>$teksti</p>";
    }

    // Apufunktio, joka etsii annettua sähköpostiosoitetta annetusta tietokannan taulusta
    // ja palauttaa hakutuloksen:
    function hae_kayttaja($sposti, $taulu) {
        global $yhteys; // yhteys näkyväksi
        $hakulauseke = $yhteys->prepare("SELECT * FROM ".$taulu." WHERE email = ?");
        $hakulauseke->bind_param("s",$sposti);
        $hakulauseke->execute();
        $tulos = $hakulauseke->get_result();
        $hakulauseke->close();
        return $tulos;
    }

    // Apufunktio, joka tarkistaa annettuun sähköpostiosoitteeseen liitetyn käyttäjätilin
    // statuksen: aktivoitu, aktivoimatta tai tilia_ei_ole. 
    function tilin_status($sposti) {
        $hakutulos = hae_kayttaja($sposti, "user");
        if ($hakutulos->num_rows > 0) { // Jos haulla löytyi käyttäjä, tutkitaan tulokset:
            if ($hakutulos->fetch_assoc()["activated"]=="0") { // Tili aktivoimatta
                return "aktivoimatta";
            } else {return "aktivoitu";} // Tili aktivoitu
        } else {return "tilia_ei_ole";} // Haku ei löytänyt käyttäjää
    }

    // Apufunktio, joka etsii annettua polettia annetusta tietokannan taulusta ja
    // palauttaa hakutuloksen:
    function hae_poletti($poletti, $taulu) {
        global $yhteys; // yhteys näkyväksi
        $hakulauseke = $yhteys->prepare("SELECT * FROM ".$taulu." WHERE token = ?");
        $hakulauseke->bind_param("s",$poletti);
        $hakulauseke->execute();
        $tulos = $hakulauseke->get_result();
        $hakulauseke->close();
        return $tulos;
    }

    // Apufunktio, joka generoi annettujen sekuntien päästä vanhenevan poletin:
    function luo_poletti() {
        return bin2hex(random_bytes(12));
    }

    // Apufunktio, joka lisää käyttäjälle (user_id) annetun poletin annettuun tauluun
    // siten, että poletti vanhenee annettujen sekuntien päästä.
    // Palauttaa true, jos lisäys tietokantaan onnistui:
    function lisaa_poletti($kayttajan_id, $poletti, $aika_vanhenemiseen, $taulu) {
        global $yhteys; // yhteys näkyväksi
        $vanhenee = date("Y-m-d H:i:s", time() + $aika_vanhenemiseen); // poletin vanhenemishetki
        // Valmistellaan ja suoritetaan rivin lisäys tauluun:
        $polettilauseke = $yhteys->prepare("INSERT INTO ".$taulu." (user_id, token,
            expires) VALUES (?, ?, ?)");
        $polettilauseke->bind_param("sss", $kayttajan_id, $poletti, $vanhenee);
        $polettilauseke->execute();
        $polettilauseke->close();
    }

    // Apufunktio, joka poistaa annettua käyttäjää koskevat tietueet annetusta taulusta
    // (taulussa on oltava user_id-kenttä):
    function poista_tietueet($user_id, $taulu) {
        global $yhteys; // yhteys näkyväksi
        // Valmistellaan ja suoritetaan rivin poisto:
        $poistolauseke = $yhteys->prepare("DELETE FROM ".$taulu." WHERE user_id = ?");
        $poistolauseke->bind_param("s", $user_id);
        $poistolauseke->execute();
        $poistolauseke->close();
    }
    
    // Apufunktio, joka lähettää sähköpostiviestin annetun osoitteen vahvistamista varten
    // ja palauttaa totuusarvon, onnistuiko lähetys:
    function laheta_vahvistusviesti($sposti, $poletti) {
        global $polku; // polku näkyväksi
        // Muodostetaan sähköpostin otsikko ja sisältö:
        $otsikko = "Neilikan toivelistasi on enää yhden klikkauksen päässä!";
        $viesti = "<p>Hei!</p>
            <p>Hienoa, että rekisteröidyit Puutarhaliike Neilikan sivustolle!<br>
            Pääset kokoamaan toivelistaasi upeiden tuotteidemme joukosta heti, kun
            vahvistat sähköpostiosoitteesi.</p>
            <button><a href='$polku/kayttajahallinta/verify_email.php?token=$poletti' 
                style='text-decoration:none; font-size:12pt; padding:3pt;'>
                Vahvista tästä!</a></button>
            <p>Jos linkki ei aukea painikkeesta, kopioi tämä selaimen osoiteriville:<br>
            $polku/kayttajahallinta/verify_email.php?token=$poletti</p>
            <p>Poletti on voimassa 30 minuuttia. Jos klikkaat linkkiä tämän jälkeen,
            sinua pyydetään syöttämään salasanasi ja tilaamaan uusi poletti.</p>
            <p>Tervetuloa Neilikkaan!<br>Puutarhaliike Neilikan tiimi</p>";
        return posti($sposti, $viesti, $otsikko);
    }


    // Apufunktio, joka lisää annetun sähköpostiosoitteen email_list-tauluun (sähköpostilistalle),
    // ellei osoite jo ole siellä. Palauttaa true, jos funtion suorituksen jälkeen osoite on
    // listalla; false, jos näin ei ole.
    function lisaa_listalle($sposti) {
        global $yhteys; // yhteys näkyväksi
        // Tarkistetaan, onko osoite jo listalla:
        $hakutulos = hae_kayttaja($sposti, "email_list");
        if ($hakutulos->num_rows == 0) { // Jos osoite ei ole listalla, yritetään lisäystä:
            $uutislauseke = $yhteys->prepare("INSERT INTO email_list (email) VALUES (?)");
            $uutislauseke->bind_param("s", $sposti);
            try {$uutislauseke->execute();}
            catch(Exception $voivoi) { // Jos listalle lisäys ei onnistunut:
                if (defined("DEBUG") and DEBUG) {
                    echo "Poikkeus: ".$voivoi->getCode().": ".$voivoi->getMessage().
                    "<br>rivi: ".$voivoi->getLine().", tiedosto: ".$voivoi->getFile()."<br>";
                }
                return false; 
            }
            finally {$uutislauseke->close();}
        }
        return true;
    }
    
    // REKISTERÖINTILOMAKKEEN pääfunktio, joka tarkistaa rekisteröintilomakkeella annetut syötteet
    // ja jos ne ovat ok, rekisteröi käyttäjän:
    function tarkista_ja_rekisteroi() {
        global $yhteys, $polku; // tietokantayhteys ja juurikansion polku näkyväksi

        // Luodaan virhe- ja onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $onnistuminen, $sposti_kaytossa;
        global $vahvistus_lahettamatta, $vahvistus_lahetetty, $postituslistavirhe;
        global $sposti_puuttuu, $salasana_puuttuu, $salasana2_puuttuu, $uutiskirje_puuttuu;
        global $sposti_epakelpo, $salasana_epakelpo, $salasana2_epakelpo;

        // Luodaan taulukko käyttäjän antamia syötteitä varten:
        global $syotteet;
        $syotteet = array("sposti"=>"","salasana"=>"","salasana2"=>"","uutiskirje"=>"");
        
        // Luodaan muuttuja, jolla seurataan, voiko syötteet lisätä tietokantaan:
        $voi_lahettaa = true;

        // Jos ei ole painettu Rekisteröidy-nappia, syötteitä ei lisätä tietokantaan:
        if (!isset($_POST["rekisteröidy"])) {
            $voi_lahettaa = false;
        } else { // Jos Rekisteröidy-nappia on painettu:
            foreach ($syotteet as $kentta => $arvo) { // Siistitään syötteet:
                if (isset($_POST[$kentta])) {
                    $syotteet[$kentta] = strip_tags(trim($_POST[$kentta]));
                }
            } // Sähköpostiosoitteeseen liittyvät tarkistukset:
            if (!empty($syotteet["sposti"])) { // Jos sposti-kenttä on täytetty:
                $tila = tilin_status($syotteet["sposti"]);
                if ($tila != "tilia_ei_ole") { // Jos osoitteella on olemassa tili:
                    $voi_lahettaa = false;                    
                    $sposti_kaytossa = "Sähköpostiosoitteella ".$syotteet["sposti"].
                        " on jo olemassa käyttäjätili.";
                    // Tyhjennetään syötteet, jotta samat tiedot eivät täyty lomakkeelle:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                    if ($tila == "aktivoimatta") { // Jos tili on aktivoimatta:
                        $sposti_kaytossa .= "<br>Tili täytyy aktivoida, jotta voit käyttää sitä.
                            <br>Aktivointilinkki on lähetetty sähköpostiisi.";
                        // HUOM MITÄ JOS KÄYTTÄJÄ EI LÖYDÄ AKTIVOINTISÄHKÖPOSTIA
                    } else { // Jos tili on aktivoitu:
                        $sposti_kaytossa .= "<br>Siirry kirjautumiseen
                            <a href=$polku.'kayttajahallinta/login.php'>tästä</a>.";
                    }
                    $sposti_kaytossa = lomaketagit($sposti_kaytossa);
                // Jos osoitteella ei löydy käyttäjätiliä, tarkistetaan syötteen muoto:
                } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) {
                    $voi_lahettaa = false;
                    $sposti_epakelpo = lomaketagit("Tarkista antamasi sähköpostiosoite.");
                }
            } else { // Jos sposti-kenttä on tyhjä:
                $voi_lahettaa = false;
                $sposti_puuttuu = lomaketagit("Kirjoita tähän sähköpostiosoitteesi.");
            }
            // Jos spostia ei ole annettu tai annettu sposti on käytettävissä, 
            // tarkistetaan muutkin syötteet:
            if (!empty($sposti_puuttuu) or empty($sposti_kaytossa)) {    
                if (empty($syotteet["salasana2"])) {
                    $voi_lahettaa = false;
                    $salasana2_puuttuu = lomaketagit("Vahvista salasanasi kirjoittamalla
                        se uudestaan tähän.");
                } elseif ($syotteet["salasana2"] !== $syotteet["salasana"]) {
                    $voi_lahettaa = false;
                    $salasana2_epakelpo = lomaketagit("Salasanat eivät vastanneet toisiaan.
                        Kirjoita tähän sama salasana kuin edelliseen kohtaan.");
                    $syotteet["salasana2"] = "";
                } 
                if (empty($syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_puuttuu = lomaketagit("Kirjoita tähän valitsemasi salasana.");
                } elseif (!preg_match('/^(?=.*[0-9])(?=.*[a-zåäö])(?=.*[A-ZÅÄÖ])(?=.*[^0-9a-zåäöA-ZÅÄÖ\s])[^\s]{8,20}$/', 
                        $syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_epakelpo = lomaketagit("Salasanan on oltava 8&#8211;20 merkkiä
                        pitkä ja mukana on oltava ainakin yksi numero, yksi pieni kirjain,
                        yksi iso kirjain sekä yksi erikoismerkki.");
                    $syotteet["salasana"] = "";
                    $syotteet["salasana2"] = "";
                    if (empty($salasana2_epakelpo) and empty($salasana2_puuttuu)) {
                        $salasana2_puuttuu = lomaketagit("Vahvista salasanasi 
                            kirjoittamalla se uudestaan tähän.");
                    }
                }
                if (empty($syotteet["uutiskirje"])) {
                    $voi_lahettaa = false;
                    $uutiskirje_puuttuu = lomaketagit("Valitse jompikumpi.","levennettava");
                } 
            }
        }
        // Jos syötteet ovat kunnossa:
        if ($voi_lahettaa) {
            // Sekoitetaan salasana:
            $salasana_hash = password_hash($syotteet["salasana"], PASSWORD_BCRYPT);
            
            // Valmistellaan käyttäjän lisäys tietokannan user-tauluun:
            $lisayslauseke = $yhteys->prepare("INSERT INTO user (email, passhash, activated)
                VALUES (?, ?, '0')");
            $lisayslauseke->bind_param("ss", $syotteet["sposti"], $salasana_hash);
            try { // Yritetään lisäystä:
                $lisayslauseke->execute();
                $uusi_id = $lisayslauseke->insert_id;
            } catch(Exception $voivoi) { // Jos käyttäjän lisäys ei onnistunut:
                $vahvistus_lahettamatta = lomaketagit("Valitettavasti tietokantayhteydessä
                    tapahtui virhe. Yritä hetken kuluttua uudelleen.");
                if (defined("DEBUG") and DEBUG) {
                    echo "Poikkeus: ".$voivoi->getCode().": ".$voivoi->getMessage().
                    "<br>rivi: ".$voivoi->getLine().", tiedosto: ".$voivoi->getFile()."<br>";
                }
            }
            $lisayslauseke->close();
        }
        // Jos käyttäjän lisäys onnistui:
        if (isset($uusi_id)) {
            // Lisätään sähköpostiosoite sähköpostilistalle, jos valinta on tehty:
            if ($syotteet["uutiskirje"] == "kyllä") {
                $lisays_sujui = lisaa_listalle($syotteet["sposti"]);
                if (!$lisays_sujui) { // Jos listalle lisäys ei onnistunut:
                    $postituslistavirhe = "Valitettavasti liittyminen
                        sähköpostilistalle ei onnistunut. Yritä listalle liittymistä
                        uudelleen <a href='$polku/ota_yhteytta.php'>tätä kautta</a>.";
                    ///// HUOM TÄHÄN MYÖS MAHDOLLISUUS SUORAAN YRITTÄÄ UUDESTAAN
                    ///// HUOM UUTISTILAUKSEN VAHVISTUSSÄHKÖPOSTI MYÖS LÄHETETTÄVÄ
                    $postituslistavirhe = lomaketagit($postituslistavirhe);
                }
            }
            // Poistetaan mahdolliset aiemmat poletit:
            try {poista_tietueet($uusi_id, "activation");}
            catch(Exception $plaah) { // Jos poistossa tapahtui virhe:
                $vahvistus_lahettamatta = "Tietokantayhteydessä tapahtui virhe.
                    Jätä meille yhteydenottopyyntö 
                    <a href='$polku/ota_yhteytta.php'>täältä</a>.";
                $vahvistus_lahettamatta = lomaketagit($vahvistus_lahettamatta);
                // HUOM VOIKO KÄYTTÄJÄ YRITTÄÄ UUDESTAAN EDELLISTÄ?
                if (defined("DEBUG") and DEBUG) {
                    echo "Poikkeus: ".$plaah->getCode().": ".$plaah->getMessage().
                    "<br>rivi: ".$plaah->getLine().", tiedosto: ".$plaah->getFile()."<br>";
                }
            }
            if (empty($vahvistus_lahettamatta)) { // Jos poisto onnistui:
                // Lisätään 30 minuutissa vanheneva poletti activation-tauluun:
                $poletti = luo_poletti();
                try {lisaa_poletti($uusi_id, $poletti, 1800, "activation");}
                catch(Exception $voivoi) { // Jos lisäys ei onnistunut:
                    $vahvistus_lahettamatta = "Sinulle on nyt luotu
                        käyttäjätili. Valitettavasti vahvistussähköpostin lähettäminen ei
                        onnistunut &#8211; jotta voit käyttää tiliäsi, jätä meille 
                        <a href='$polku/ota_yhteytta.php'>yhteydenottopyyntö</a>.";
                        // paina <a href=''>tästä</a>, niin yritämme uudestaan!</p>";
                        // HUOM JOS TÄHÄN TEKIS NÄKYMÄTTÖMÄN FORMIN JA POSTILLA SEN LÄHETTÄIS
                        // --> KOODIIN RIVILLE 137: OR IF ISSET POST[VAHVISTA]
                    $vahvistus_lahettamatta = lomaketagit($vahvistus_lahettamatta);
                    if (defined("DEBUG") and DEBUG) {
                        echo "Poikkeus: ".$voivoi->getCode().": ".$voivoi->getMessage().
                        "<br>rivi: ".$voivoi->getLine().", tiedosto: ".$voivoi->getFile()."<br>";
                    }
                    // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                }
            }
            /* HUOM JOS EDELLISEEN TEKEE TON FORM-JUTUN JA HALUAA VASTAAVAN TÄHÄN KOHTAAN
            (VIRHEILMOITUKSEEN: SÄHKÖPOSTIN LÄHETYS EI ONNISTU), TÄMÄ IF-LAUSE PYKÄLÄÄ ULOMMAS JA
            JOTAIN EHTOJA, JOILLA SUORITUS SIIRTYY TÄHÄN KOHTAAN SILLOIN, JOS EDELLINEN VAIHE 
            ON ONNISTUNUT TAI TULLAAN SÄHKÖPOSTIN-LÄHETYS-EI-ONNISTUNUT-LINKISTÄ: */
            // Jos aktivointipoletin lisäys onnistui:
            if (empty($vahvistus_lahettamatta)) {
                // Yritetään lähettää vahvistussähköposti:
                $posti_lahetetty = laheta_vahvistusviesti($syotteet["sposti"], $poletti);
                if (!$posti_lahetetty) { // Jos lähetys ei onnistunut:
                    $vahvistus_lahettamatta = "Sinulle on nyt luotu käyttäjätili. 
                        Valitettavasti vahvistussähköpostin lähettäminen ei onnistunut.
                        Jotta pääset käyttämään tiliäsi, jätä meille
                        <a href='$polku/ota_yhteytta.php'>yhteydenottopyyntö</a>, niin hoidamme asian.";
                    $vahvistus_lahettamatta = lomaketagit($vahvistus_lahettamatta);
                } else { // Jos lähetys onnistui:
                    $onnistuminen = ok_tagit("Hienoa, melkein valmista!<br>
                        Aktivoi vielä tilisi klikkaamalla linkkiä, jonka lähetimme juuri
                        sähköpostiisi. Linkki vanhenee 30 minuutin päästä.");
                    // HUOM LISÄKSI ETKÖ SAANUT -LINKKI, JOSTA AKTIVOINTISÄHKÖPOSTIN VOI 
                    // LÄHETTÄÄ UUDESTAAN!
                }
                // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
            }
        }
    }

    // KIRJAUTUMISLOMAKKEEN pääfunktio, joka tarkistaa kirjautumislomakkeella annetut syötteet
    // ja jos ne ovat ok, kirjaa käyttäjän sisään:
    function tarkista_ja_kirjaudu() {
        global $yhteys, $polku; // tietokantayhteys ja juurikansion polku näkyväksi

        // Luodaan virheviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $tilia_ei_ole, $sposti_vahvistamatta, $vaara_salasana;
        global $sposti_ei_kelpaa, $salasana_puuttuu;

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
                $salasana_puuttuu = lomaketagit("Kirjoita tähän Neilikka-tilisi salasana.");
            }
            // Sähköpostiosoitteeseen liittyvät tarkistukset:
            if (empty($syotteet["sposti"])) { // Jos sposti-kenttä on tyhjä:
                $voi_kirjautua = false;
                $sposti_ei_kelpaa = lomaketagit("Kirjoita tähän sähköpostiosoitteesi.");                
            } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) { 
                // Jos sposti on syötetty, mutta ei ole kelvollinen sähköpostiosoite:
                    $voi_kirjautua = false;
                    $sposti_ei_kelpaa = lomaketagit("Tarkista antamasi sähköpostiosoite.");
            } elseif (empty($salasana_puuttuu)) {
                // Jos sposti kelpaa ja salasana on annettu, etsitään käyttäjää tietokannasta:
                $hakutulos = hae_kayttaja($syotteet["sposti"], "user");
                if ($hakutulos->num_rows == 0) { // Jos haulla ei löytynyt käyttäjätiliä:
                    $tilia_ei_ole = "Antamallasi sähköpostiosoitteella ".$syotteet["sposti"].
                        " ei löydy käyttäjätiliä.";
                    $tilia_ei_ole = lomaketagit($tilia_ei_ole);
                    // Tyhjennetään syötteet, jotta samat tiedot eivät täyty lomakkeelle:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                } else { // Jos haulla löytyi käyttäjätili, tutkitaan tulokset:
                    $tili = $hakutulos->fetch_assoc();
                    if ($tili["activated"]=="0") { // Jos tili on aktivoimatta:
                        $sposti_vahvistamatta = lomaketagit("Tilisi sähköpostiosoite on vahvistamatta.
                        Sähköpostiisi on lähetetty aktivointilinkki.");
                        // HUOM MITÄ JOS KÄYTTÄJÄ EI LÖYDÄ AKTIVOINTISÄHKÖPOSTIA?
                    } elseif (!password_verify($syotteet["salasana"], $tili["passhash"])) {
                        // Jos annettu salasana on väärä:
                        $vaara_salasana = lomaketagit("Sähköpostiosoite tai salasana on väärä. 
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

    function vahvista_ja_aktivoi() {
        global $yhteys, $polku; // tietokantayhteys ja juurikansion polku näkyväksi

        // Luodaan virheviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $poletti_vanhentunut, $jo_vahvistettu, $vahvistaminen_ok;
        global $virhe_aktivoinnissa, $aktivointivirhe_lisaohje;

        // Haetaan poletti osoiteriviltä (jos ei löydy, poletti olkoon tyhjä merkkijono):
        $poletti = $_GET["token"] ?? "";
        
        // Jos polettia ei löytynyt osoiteriviltä:
        if (!$poletti) {
            $virhe_aktivoinnissa = lomaketagit("Käyttäjätili otetaan käyttöön (aktivoidaan)
                vahvistamalla siihen liitetty sähköpostiosoite. Voit vahvistaa sähköpostiosoitteesi
                klikkaamalla linkkiä, jonka lähetimme antamaasi osoitteeseen.<br><br>
                Jos et löydä sähköpostia, tarkista roskapostikansiosi.<br>
                Tarvittaessa voit tilata uuden linkin täyttämällä tietosi alle.");
        } else { // Jos poletti löytyi osoiteriviltä, haetaan vastaava tietue tietokannasta:
            $polettitulos = hae_poletti($poletti, "activation");
            if ($polettitulos->num_rows == 0) { // Jos haulla ei löytynyt tietuetta:
                $virhe_aktivoinnissa = lomaketagit("Aktivointi ei onnistunut.<br>
                    Varmista, että kopioit vahvistussähköpostissa saamasi linkin osoiteriville
                    kokonaisuudessaan.<br>
                    Tarvittaessa voit tilata uuden linkin täyttämällä tietosi alle.");
                $aktivointivirhe_lisaohje = "Huom. Virheilmoitus voi johtua myös siitä, että
                    olet jo vahvistanut sähköpostiosoitteesi. Jos näin on, pääset kirjautumaan
                    tilillesi <a href='$polku/kayttajahallinta/login.php'>täältä</a>.";
                $aktivointivirhe_lisaohje = ok_tagit($aktivointivirhe_lisaohje);
            } else { // Jos tietue löytyi, tarkistetaan poletin voimassaolo:
                $polettitietue = $polettitulos->fetch_assoc();
                if ($polettitietue["expires"] < date("Y-m-d H:i:s", time())) { // Jos ei voimassa:
                    $poletti_vanhentunut = lomaketagit("Linkki on vanhentunut. Täytä tietosi alle, niin lähetämme
                        sinulle uuden.");
                } // TÄÄLLÄ MENOSSA! MUISTA POLETIN POISTO 
            }  
        }
    }

    // Funktio, jonka avulla käyttäjä voi tilata uuden aktivointipoletin:
    function tilaa_poletti() {
        global $yhteys, $polku; // yhteys ja polku näkyväksi

        // Luodaan onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $tarkista_tilaus, $poletti_tilattu;
        
        // Luodaan muuttujat käyttäjän antamia syötteitä varten:
        global $poletti_sp;
        $salasana = "";

        // Loput funktiosta suoritetaan vain, jos on painettu Tilaa poletti -nappia:
        if (isset($_POST["tilaa_poletti"])) {
            $poletti_sp = strip_tags(trim($_POST["sposti"]));
            $salasana = strip_tags(trim($_POST["salasana"]));

            // Tarkistetaan, ovatko syötteet oikeanmuotoisia:
            if (!empty($poletti_sp) and !empty($salasana) 
            and filter_var($poletti_sp, FILTER_VALIDATE_EMAIL)) {
                $hakutulos = hae_kayttaja($poletti_sp, "user"); // Jos ovat, haetaan käyttäjätili
                if ($hakutulos->num_rows == 0) { // Jos haulla ei löytynyt käyttäjätiliä:
                    $tarkista_tilaus = lomaketagit("Tällä sähköpostiosoitteella ei ole 
                        rekisteröity käyttäjätiliä.");
                } else { // Jos haulla löytyi käyttäjätili, tutkitaan tulokset:
                    $tili = $hakutulos->fetch_assoc();
                    if ($tili["activated"]=="1") { // Jos tili on aktivoitu:
                        $jo_vahvistettu = "Tämä sähköpostiosoite on jo vahvistettu ja
                            käyttäjätili aktivoitu. Kirjaudu tilillesi
                            <a href='$polku/kayttajahallinta/login.php'>täältä</a>.";
                        $jo_vahvistettu = ok_tagit($jo_vahvistettu);
                    } elseif (!password_verify($salasana, $tili["passhash"])) {
                        // Jos annettu salasana on väärä:
                        $tarkista_tilaus = lomaketagit("Tarkista sähköpostiosoite ja salasana.");
                    } else {// Jos tili on aktivoimatta ja salasana oikein:
                        // Poistetaan mahdolliset aiemmat poletit:
                        try {poista_tietueet($tili["user_id"], "activation");}
                        catch(Exception $plaah) { // Jos poistossa tapahtui virhe:
                            $tarkista_tilaus = lomaketagit("Tietokantayhteydessä tapahtui virhe.
                                Yritä hetken kuluttua uudelleen.");
                            if (defined("DEBUG") and DEBUG) {
                                echo "Poikkeus: ".$plaah->getCode().": ".$plaah->getMessage().
                                "<br>rivi: ".$plaah->getLine().", tiedosto: ".$plaah->getFile()."<br>";
                            }
                        }
                        // Yritetään lisätä 30 minuutissa vanheneva poletti activation-tauluun:
                        if (empty($tarkista_tilaus)) {
                            $poletti = luo_poletti();
                            try {lisaa_poletti($tili["user_id"], $poletti, 1800, "activation");}
                            catch(Exception $oijoi) { // Jos lisäys ei onnistunut:
                                $tarkista_tilaus = lomaketagit("Linkin lähettäminen ei valitettavasti
                                    onnistunut. Yritä hetken kuluttua uudelleen.");
                                if (defined("DEBUG") and DEBUG) {
                                    echo "Poikkeus: ".$oijoi->getCode().": ".$oijoi->getMessage().
                                    "<br>rivi: ".$oijoi->getLine().", tiedosto: ".$oijoi->getFile()."<br>";
                                }
                            }
                        }
                        if (empty($tarkista_tilaus)) { // Jos poletin lisäys onnistui:
                            // Yritetään lähettää vahvistussähköposti:
                            $posti_lahetetty = laheta_vahvistusviesti($poletti_sp, $poletti);
                            if (!$posti_lahetetty) { // Jos lähetys ei onnistunut:
                                $tarkista_tilaus = "Vahvistamiseen tarvittava linkki on luotu, mutta
                                    valitettavasti vahvistussähköpostin lähettäminen ei onnistunut.
                                    Jotta pääset käyttämään tiliäsi, jätä meille
                                    <a href='$polku/ota_yhteytta.php'>yhteydenottopyyntö</a>.";
                                $tarkista_tilaus = lomaketagit($tarkista_tilaus);
                                // HUOM TÄHÄN MAHDOLLISUUS YRITTÄÄ UUDESTAAN?
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
            } else { // Kenttien syötteissä oli vikaa:
                $tarkista_tilaus = lomaketagit("Tarkista sähköpostiosoite ja salasana.");
            }
        }
    }



            
            
    
?>