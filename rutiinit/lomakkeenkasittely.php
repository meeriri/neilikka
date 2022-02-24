<?php
    // Luodaan tietokantayhteys (viittauksessa ei saa olla http-alkua, joten $polku ei käy):
    if (file_exists(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php")) {
        include(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php");
        if (!isset($yhteys)) {$yhteys = db_yhteys();}
    }
    // Haetaan PHPMaileriin perustuva posti.php:
    if (file_exists(dirname(__DIR__,1)."/rutiinit/posti.php"))
        {include(dirname(__DIR__,1)."/rutiinit/posti.php");}

    // Funktio, joka lisää (virhe)tekstin ympärille <p class='...'>-tägit:
    function lomaketagit($teksti, $lisaluokka = "") {
        return "<p class='lomakevirhe $lisaluokka'>$teksti</p>";
    }

    // Funktio, joka tarkistaa rekisteröintilomakkeella annetut syötteet
    // ja jos ne ovat ok, rekisteröi käyttäjän:
    function tarkista_ja_rekisteroi() {
        global $yhteys, $polku; // tietokantayhteys ja juurikansion polku näkyväksi

        // Luodaan virhe- ja onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $onnistuminen, $sposti_kaytossa, $vahvistus_lahettamatta, $vahvistus_lahetetty;
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
                    $uusiarvo = $yhteys->real_escape_string(strip_tags(trim($_POST[$kentta])));
                    $syotteet[$kentta] = $uusiarvo;
                }
            } // Sähköpostiosoitteeseen liittyvät tarkistukset:
            if (!empty($syotteet["sposti"])) {
                // Jos sposti ei ole tyhjä, tarkistetaan, onko sillä jo rekisteröity tili:
                $tarkistuslauseke = $yhteys->prepare("SELECT * FROM user WHERE email = ?");
                $tarkistuslauseke->bind_param("s",$syotteet["sposti"]);
                $tarkistuslauseke->execute();
                $tulos = $tarkistuslauseke->get_result();
                if ($tulos->num_rows > 0) { // Jos osoitteella löytyi käyttäjätili:
                    $voi_lahettaa = false;
                    $sposti_kaytossa = "Sähköpostiosoitteella ".$syotteet["sposti"].
                        " on jo olemassa käyttäjätili.";
                    // Tyhjennetään syötteet, jotta samat tiedot eivät täyty lomakkeelle:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                    if ($tulos->fetch_assoc()["activated"]==="0") { 
                        // Jos tili on aktivoimatta (eli rivin activated-kentässä on arvo 0):
                        $sposti_kaytossa .= "<br>Tili täytyy aktivoida, jotta voit käyttää sitä.
                            <br>Aktivointilinkki on lähetetty sähköpostiosoitteeseen.";
                    } else { // Jos tili on aktivoitu:
                        $sposti_kaytossa .= "<br>Siirry kirjautumiseen
                            <a href=$polku.'kayttajahallinta/login.php'>tästä</a>.";
                    }
                    $sposti_kaytossa = lomaketagit($sposti_kaytossa);
                // Jos sähköpostiosoite on käytettävissä, tarkistetaan sen muoto:
                } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) {
                    $voi_lahettaa = false;
                    $sposti_epakelpo = lomaketagit("Tarkista sähköpostiosoite.");
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
        }
        // Jos käyttäjän lisäys onnistui:
        if (isset($uusi_id)) {
            // Lisätään sähköpostiosoite sähköpostilistalle, jos valinta on tehty:
            if ($syotteet["uutiskirje"] == "kyllä") {
                // Tarkistetaan ensin, onko osoite jo listalla:
                $tarkistuslauseke = $yhteys->prepare("SELECT * FROM email_list WHERE email = ?");
                $tarkistuslauseke->bind_param("s",$syotteet["sposti"]);
                $tarkistuslauseke->execute();
                $tulos = $tarkistuslauseke->get_result();
                if ($tulos->num_rows == 0) { // Jos osoite ei ole listalla:
                    $uutislauseke = $yhteys->prepare("INSERT INTO email_list (email) VALUES (?)");
                    $uutislauseke->bind_param("s", $syotteet["sposti"]);
                    try {$uutislauseke->execute();} // Yritetään lisäystä
                    catch(Exception $voivoi) { // Jos listalle lisäys ei onnistunut:
                        $vahvistus_lahettamatta = "Valitettavasti liittyminen
                            sähköpostilistalle ei onnistunut. Yritä listalle liittymistä
                            uudelleen <a href='$polku/ota_yhteytta.php'>tätä kautta</a>.";
                        ///// HUOM TÄHÄN MYÖS MAHDOLLISUUS SUORAAN YRITTÄÄ UUDESTAAN
                        ///// HUOM UUTISTILAUKSEN VAHVISTUSSÄHKÖPOSTI MYÖS LÄHETETTÄVÄ
                        $vahvistus_lahettamatta = lomaketagit($vahvistus_lahettamatta);
                        if (defined("DEBUG") and DEBUG) {
                            echo "Poikkeus: ".$voivoi->getCode().": ".$voivoi->getMessage().
                            "<br>rivi: ".$voivoi->getLine().", tiedosto: ".$voivoi->getFile()."<br>";
                        }
                    }
                }
            }
            // Luodaan satunnainen poletti (token) aktivointia varten:
            $poletti = bin2hex(random_bytes(12));
            // Lasketaan poletin vanhenemishetki (900 sek = vartti aikaa aktivoida):
            $vanhenee = date("Y-m-d H:i:s", time()+900);
    
            // Valmistellaan rivin lisäys activation_token-tauluun:
            $polettilauseke = $yhteys->prepare("INSERT INTO activation_token (user_id, token,
                expires) VALUES (?, ?, ?)");
            $polettilauseke->bind_param("sss", $uusi_id, $poletti, $vanhenee);
            try {$polettilauseke->execute();} // Yritetään lisäystä
            catch(Exception $oijoi) { // Jos lisäys ei onnistunut:
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
            /* HUOM JOS EDELLISEEN TEKEE TON FORM-JUTUN JA HALUAA VASTAAVAN TÄHÄN KOHTAAN
            (VIRHEILMOITUKSEEN: SÄHKÖPOSTIN LÄHETYS EI ONNISTU), TÄMÄ IF-LAUSE PYKÄLÄÄ ULOMMAS JA
            JOTAIN EHTOJA, JOILLA SUORITUS SIIRTYY TÄHÄN KOHTAAN SILLOIN, JOS EDELLINEN VAIHE 
            ON ONNISTUNUT TAI TULLAAN SÄHKÖPOSTIN-LÄHETYS-EI-ONNISTUNUT-LINKISTÄ: */
            // Jos aktivointipoletin lisäys onnistui:
            if (empty($vahvistus_lahettamatta)) {
                // Muodostetaan sähköposti, jolla käyttäjä voi aktivoida tilinsä:
                $viesti = "<p>Hei!</p>
                    <p>Hienoa, että rekisteröidyit Puutarhaliike Neilikan sivustolle!<br>
                    Pääset kokoamaan toivelistaasi upeiden tuotteidemme joukosta heti, kun
                    vahvistat sähköpostiosoitteesi.</p>
                    <button><a href='$polku/kayttajahallinta/verify_email.php?token=$poletti' 
                        style='text-decoration:none; font-size:12pt; padding:3pt;'>
                        Vahvista tästä!</a></button>
                    <p>Tervetuloa Neilikkaan!<br>Puutarhaliike Neilikan tiimi</p>";
                $otsikko = "Neilikan toivelistasi on enää yhden klikkauksen päässä!";
                $posti_onnistui = posti($syotteet["sposti"], $viesti, $otsikko);
                if (!$posti_onnistui) {
                    $vahvistus_lahettamatta = "Sinulle on nyt luotu käyttäjätili. 
                        Valitettavasti vahvistussähköpostin lähettäminen ei onnistunut.
                        Jotta pääset käyttämään tiliäsi, jätä meille
                        <a href='$polku/ota_yhteytta.php'>yhteydenottopyyntö</a>, niin hoidamme asian.";
                    $vahvistus_lahettamatta = lomaketagit($vahvistus_lahettamatta);
                } else {
                    $onnistuminen = "<p class='lomake_ok'>Hienoa, melkein valmista!<br>
                        Aktivoi vielä tilisi klikkaamalla linkkiä, jonka lähetimme juuri
                        sähköpostiisi. Linkki vanhenee 15 minuutin päästä.</p>";
                    //////// HUOM LISÄKSI ETKÖ SAANUT -LINKKI, JOSTA AKTIVOINTISÄHKÖPOSTIN VOI 
                    // LÄHETTÄÄ UUDESTAAN!!
                }
                // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
            }
        }
    }

    // Funktio, joka tarkistaa kirjautumislomakkeella annetut syötteet
    // ja jos ne ovat ok, kirjaa käyttäjän sisään:
    function tarkista_ja_kirjaudu() {
        global $yhteys, $polku; // tietokantayhteys ja juurikansion polku näkyväksi

        // Luodaan virheviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $tilia_ei_ole, $tili_vahvistamatta, $vaara_salasana;
        global $sposti_puuttuu, $salasana_puuttuu;

        // Luodaan taulukko käyttäjän antamia syötteitä varten:
        global $syotteet;
        $syotteet = array("sposti"=>"","salasana"=>"","muista"=>"");
        
        // Luodaan muuttuja, jolla seurataan, voiko kirjautumisen suorittaa:
        $voi_kirjautua = true;

        // Jos ei ole painettu Kirjaudu-nappia, ei voi kirjautua:
        if (!isset($_POST["kirjaudu"])) {
            $voi_kirjautua = false;
        } else { // Jos Kirjaudu-nappia on painettu:
 
        }
    }

?>