<?php
    // Haetaan lomakkeiden yhteinen koodi:
    if (file_exists(dirname(__DIR__,1)."/rutiinit/lomake.php")) {
        include(dirname(__DIR__,1)."/rutiinit/lomake.php");
    }

    // Rekisteröintilomakkeen (register.php) pääfunktio, joka tarkistaa lomakkeella annetut 
    // syötteet ja jos ne ovat ok, rekisteröi käyttäjän:
    function tarkista_ja_rekisteroi() {
        global $polku; // juurikansion polku näkyväksi

        // Luodaan virhe- ja onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $onnistuminen, $yhteysvirhe, $sposti_kaytossa;
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
                try {$tila = tilin_status($syotteet["sposti"]);} // Tutkitaan tilin tilanne
                catch(Exception $voivoi) { // Jos haku ei onnistunut:
                    kasittele_poikkeus($voivoi);
                    $yhteysvirhe = virhetagit("Ups! Tietokantayhteydessä tapahtui virhe. 
                        Paina rekisteröitymisnappia uudelleen.");
                    return; // keskeytetään funktion suoritus tähän
                }
                if ($tila != "tilia_ei_ole") { // Jos osoitteella on olemassa tili:
                    $voi_lahettaa = false;                    
                    $sposti_kaytossa = "Sähköpostiosoitteella ".$syotteet["sposti"].
                        " on jo olemassa käyttäjätili.";
                    // Tyhjennetään syötteet, jotta samat tiedot eivät täyty lomakkeelle:
                    foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
                    if ($tila == "aktivoimatta") { // Jos tili on aktivoimatta:
                        $sposti_kaytossa .= "<br>Tili täytyy aktivoida, jotta voit käyttää sitä.
                            <br>Aktivointilinkki on lähetetty sähköpostiisi (tarkistathan myös
                            roskapostikansiosi).<br>Tarvittaessa voit pyytää uuden linkin
                            <a href='".$polku."/kayttajahallinta/verify_email.php?uusi=1'>täältä</a>.";
                    } else { // Jos tili on aktivoitu:
                        $sposti_kaytossa .= "<br>Siirry kirjautumiseen
                            <a href='$polku.kayttajahallinta/login.php'>tästä</a>.";
                    }
                    $sposti_kaytossa = virhetagit($sposti_kaytossa);
                // Jos osoitteella ei löydy käyttäjätiliä, tarkistetaan syötteen muoto:
                } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) {
                    $voi_lahettaa = false;
                    $sposti_epakelpo = virhetagit("Tarkista antamasi sähköpostiosoite.");
                }
            } else { // Jos sposti-kenttä on tyhjä:
                $voi_lahettaa = false;
                $sposti_puuttuu = virhetagit("Kirjoita tähän sähköpostiosoitteesi.");
            }
            // Jos spostia ei ole annettu tai annettu sposti on käytettävissä, 
            // tarkistetaan muutkin syötteet:
            if (!empty($sposti_puuttuu) or empty($sposti_kaytossa)) {    
                if (empty($syotteet["salasana2"])) {
                    $voi_lahettaa = false;
                    $salasana2_puuttuu = virhetagit("Vahvista salasanasi kirjoittamalla
                        se uudestaan tähän.");
                } elseif ($syotteet["salasana2"] !== $syotteet["salasana"]) {
                    $voi_lahettaa = false;
                    $salasana2_epakelpo = virhetagit("Salasanat eivät vastanneet toisiaan.
                        Kirjoita tähän sama salasana kuin edelliseen kohtaan.");
                    $syotteet["salasana2"] = "";
                } 
                if (empty($syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_puuttuu = virhetagit("Kirjoita tähän valitsemasi salasana.");
                } elseif (!preg_match('/^(?=.*[0-9])(?=.*[a-zåäö])(?=.*[A-ZÅÄÖ])(?=.*[^0-9a-zåäöA-ZÅÄÖ\s])[^\s]{8,20}$/', 
                        $syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_epakelpo = virhetagit("Salasanan on oltava 8&#8211;20 merkkiä
                        pitkä ja mukana on oltava ainakin yksi numero, yksi pieni kirjain,
                        yksi iso kirjain sekä yksi erikoismerkki.");
                    $syotteet["salasana"] = "";
                    $syotteet["salasana2"] = "";
                    if (empty($salasana2_epakelpo) and empty($salasana2_puuttuu)) {
                        $salasana2_puuttuu = virhetagit("Vahvista salasanasi 
                            kirjoittamalla se uudestaan tähän.");
                    }
                }
                if (empty($syotteet["uutiskirje"])) {
                    $voi_lahettaa = false;
                    $uutiskirje_puuttuu = virhetagit("Valitse jompikumpi.","levennettava");
                } 
            }
        }
        // Jos syötteet ovat kunnossa, yritetään lisätä käyttäjä tietokantaan:
        if ($voi_lahettaa) {
            try {$uusi_id = lisaa_kayttaja($syotteet["sposti"], $syotteet["salasana"]);}
            catch(Exception $voivoi) { // Jos lisäys ei onnistunut:
                kasittele_poikkeus($voivoi);
                $vahvistus_lahettamatta = virhetagit("Ups! Virhe tietokantayhteydessä.
                    Yritä hetken kuluttua uudelleen.");
            }
        }
        // Jos käyttäjän lisäys onnistui:
        if (isset($uusi_id)) {
            // Lisätään sähköpostiosoite sähköpostilistalle, jos valinta on tehty:
            if ($syotteet["uutiskirje"] == "kyllä") {
                try {lisaa_listalle($syotteet["sposti"]);}
                catch(Exception $voivoi) { // Jos lisäys ei onnistunut:
                    kasittele_poikkeus($voivoi);
                    $postituslistavirhe = virhetagit("Valitettavasti liittyminen
                    sähköpostilistalle ei onnistunut. Yritä listalle liittymistä
                    uudelleen <a href='".$polku."/ota_yhteytta.php'>tätä kautta</a>.");
                    // HUOM TÄHÄN MYÖS MAHDOLLISUUS SUORAAN YRITTÄÄ UUDESTAAN
                    // HUOM UUTISTILAUKSEN VAHVISTUSSÄHKÖPOSTI LÄHETETTÄVÄ ERIKSEEN
                }
            }
            // Lisätään 30 minuutissa vanheneva poletti activation-tauluun:
            $poletti = luo_poletti();
            try {lisaa_poletti($uusi_id, $poletti, 1800, "activation");}
            catch(Exception $voivoi) { // Jos lisäys ei onnistunut:
                kasittele_poikkeus($voivoi);
                $vahvistus_lahettamatta = virhetagit("Sinulle on nyt luotu käyttäjätili. Valitettavasti
                    vahvistussähköpostin lähettäminen ei kuitenkaan onnistunut.<br>Pyydä uusi vahvistus
                    <a href='".$polku."/kayttajahallinta/verify_email.php?uusi=1'>täältä</a>,
                    niin pääset käyttämään tiliäsi!");
                // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
            }
            // Jos aktivointipoletin lisäys onnistui:
            if (empty($vahvistus_lahettamatta)) {
                // Yritetään lähettää vahvistussähköposti:
                $posti_lahetetty = laheta_vahvistusviesti($syotteet["sposti"], $poletti);
                if (!$posti_lahetetty) { // Jos lähetys ei onnistunut:
                    $vahvistus_lahettamatta = virhetagit("Sinulle on nyt luotu käyttäjätili. 
                        Valitettavasti vahvistussähköpostin lähettäminen ei kuitenkaan onnistunut.
                        <br>Pyydä uusi vahvistus
                        <a href='".$polku."/kayttajahallinta/verify_email.php?uusi=1'>täältä</a>,
                        niin pääset käyttämään tiliäsi!");
                } else { // Jos lähetys onnistui:
                    $onnistuminen = ok_tagit("Hienoa, melkein valmista!<br>
                        Aktivoi vielä tilisi klikkaamalla linkkiä, jonka lähetimme juuri
                        sähköpostiisi. Linkki vanhenee 30 minuutin päästä.<br><br>
                        Jos et löydä sähköpostia (tarkistathan myös roskapostikansiosi), 
                        voit pyytää uuden linkin
                        <a href='".$polku."/kayttajahallinta/verify_email.php?uusi=1'>täältä</a>.");
                }
                // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
                foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
            }
        }
    }
?>