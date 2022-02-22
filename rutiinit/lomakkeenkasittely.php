<?php
    // Luodaan tietokantayhteys (viittauksessa ei saa olla http-alkua, joten $polku ei käy):
    if (file_exists(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php")) 
        {include(dirname(__DIR__,1)."/rutiinit/tietokantayhteys.php");}
    if (!isset($yhteys)) {$yhteys = db_yhteys();}

    // funktio, jonka avulla pudotusvalikossa saadaan näkymään jo tehdyt valinnat:
    function naytaValittu($valikko, $valinta) {
        if (isset($_POST[$valikko]) and $_POST[$valikko] == $valinta)
            {return "selected='selected'";}
    }
    // funktio, jonka avulla checkboxissa saadaan näkymään jo laitettu rasti:
    function naytaRasti($boksisetti, $boksin_value) {
        if (isset($_POST[$boksisetti])) {
            // Jos setissä on monta checkboxia, boksisetti on taulukko:
            if (is_array($_POST[$boksisetti]) and in_array($boksin_value,$_POST[$boksisetti]))
                {return "checked='checked'";}
            else // Jos setissä on vain yksi checkbox, ei tarvitse tutkia enempää: 
                {return "checked='checked'";}
        }
    }

    // funktio, jonka avulla tulostetaan huomautus pakolliseen kenttään, josta puuttuu arvo:
    function huomautaPuuttuvasta($kentta) {
        if (isset($_POST["lähetä"]) and empty($_POST[$kentta])) {
            if ($kentta == "uutiskirje") {
                return "<p class='lomakevirhe levennettava'>Valitse jompikumpi.</p>";
            }
            return "<p class='lomakevirhe'>Täytä tämä kenttä.</p>";
        }
    }

    // Funktio, joka tarkistaa rekisteröintilomakkeella annetut syötteet
    // ja jos ne ovat ok, rekisteröi käyttäjän:
    function tarkista_ja_rekisteroi() {
        global $yhteys; // tietokantayhteys näkyväksi

        // Luodaan virhe- ja onnistumisviestit (global, jotta näkyvät funktion ulkopuolelle):
        global $onnistuminen, $spostin_vahvistus_ok;
        global $sposti_kaytossa, $spostin_vahvistusvirhe;
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
                    $sposti_kaytossa = "<p class='lomakevirhe'>Sähköpostiosoitteella ".
                        $syotteet["sposti"]." on jo olemassa käyttäjätili.";
                        // Tyhjennetään syöte, jotta tämä osoite ei täyty enää lomakkeelle:
                        $syotteet["sposti"] = ""; 
                    if ($tulos->fetch_assoc()["activated"]==="0") { 
                        // Jos tili on aktivoimatta (eli rivin activated-kentässä on arvo 0):
                        $sposti_kaytossa .= "<br>Tili täytyy aktivoida, jotta voit käyttää sitä.
                            <br>Aktivointilinkki on lähetetty sähköpostiosoitteeseen.";
                    } else { // Jos tili on aktivoitu:
                        global $polku;
                        $sposti_kaytossa .= "<br>Siirry kirjautumiseen
                            <a href=$polku.'kayttajahallinta/login.php'>tästä</a>.";
                    }
                    $sposti_kaytossa .= "</p>";
                // Jos sähköpostiosoite on käytettävissä, tarkistetaan sen muoto:
                } elseif (!filter_var($syotteet["sposti"], FILTER_VALIDATE_EMAIL)) {
                    $voi_lahettaa = false;
                    $sposti_epakelpo = "<p class='lomakevirhe'>Tarkista sähköpostiosoite.</p>";
                }
            } else { // Jos sposti-kenttä on tyhjä:
                $voi_lahettaa = false;
                $sposti_puuttuu = "<p class='lomakevirhe'>Kirjoita tähän sähköpostiosoitteesi.</p>";
            }
            // Jos spostia ei ole annettu tai annettu sposti on käytettävissä, 
            // tarkistetaan muutkin syötteet:
            if (!empty($sposti_puuttuu) or empty($sposti_kaytossa)) {    
                if (empty($syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_puuttuu = "<p class='lomakevirhe'>Kirjoita tähän valitsemasi salasana.</p>";
                } elseif (!preg_match('/^(?=.*[0-9])(?=.*[a-zåäö])(?=.*[A-ZÅÄÖ])(?=.*[^0-9a-zåäöA-ZÅÄÖ\s])[^\s]{8,20}$/', 
                        $syotteet["salasana"])) {
                    $voi_lahettaa = false;
                    $salasana_epakelpo = "<p class='lomakevirhe'>Salasanan on oltava 8&#8211;20 merkkiä
                        pitkä ja mukana on oltava ainakin yksi numero, yksi pieni kirjain, yksi iso 
                        kirjain sekä yksi erikoismerkki.</p>";
                    $salasana2_epakelpo = "<p class='lomakevirhe'>Vahvista uusi salasanasi tähän.</p>";
                }
                if (empty($syotteet["salasana2"])) {
                    $voi_lahettaa = false;
                    $salasana2_puuttuu = "<p class='lomakevirhe'>Vahvista salasanasi kirjoittamalla
                        se uudestaan tähän.</p>";
                } elseif ($syotteet["salasana2"] !== $syotteet["salasana"]) {
                    $voi_lahettaa = false;
                    $salasana2_epakelpo = "<p class='lomakevirhe'>Salasanat eivät vastanneet toisiaan.
                        Kirjoita tähän sama salasana kuin edelliseen kohtaan.</p>";
                } 
                if (empty($syotteet["uutiskirje"])) {
                    $voi_lahettaa = false;
                    $uutiskirje_puuttuu = "<p class='lomakevirhe levennettava'>Valitse jompikumpi.</p>";
                } 
            }
        }
        // Jos syötteet ovat kunnossa:
        if ($voi_lahettaa) {
            // Luodaan satunnainen poletti (token) sähköpostin aktivointia varten:
            //$poletti = ;

            $onnistuminen = "<p class='lomake_ok'>Onnistui!</p>";
            // Tyhjennetään syötteet, jotta eivät enää näy lomakkeella:
            foreach ($syotteet as $kentta => $arvo) {$syotteet[$kentta] = "";}
        }
    }
?>