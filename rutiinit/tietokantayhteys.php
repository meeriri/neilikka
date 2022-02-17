<?php
    // Haetaan tunnukset (jos ei onnistu, keskeytä):
    if (file_exists("../../tunnukset.php")) {include("../../tunnukset.php");}
    else {
        echo "<p>Tietokantaan ei saada yhteyttä. Yritä myöhemmin uudelleen.</p>";
        exit;
    }

    // Funktio, joka luo yhteyden tietokantaan:
    // (ajax- ja DEBUG-jutut funktion parametrissä ja poikkeuksenkäsittelyssä
    //  olivat opettajan snippetissä, en tiedä niiden merkitystä)
    function db_yhteys($ajax = false) {
        // Tallennetaan yhteys staattiseen muuttujaan, jolloin samaa yhteyttä
        // ei tarvitse asettaa kuin kerran:
        static $yhteys;
        if (!isset($yhteys) or empty($yhteys)) {
            try {
                @$yhteys = new mysqli(PALVELIN, DB_KAYTTAJA, DB_SALASANA, TIETOKANTA);
                // Jos yhteyden muodostaminen ei onnistunut, luodaan poikkeus:
                if ($yhteysvirhe = $yhteys->connect_error) {
                    throw new Exception("Virhe tietokantayhteydessä $palvelin.", $yhteysvirhe);
                }
            }
            catch (Exception $voih) { // Poikkeuksen käsittely:
                if (defined("DEBUG") and DEBUG) {
                    $viesti = "Poikkeus " .$voih->getCode(). ": " .$voih->getMessage().
                        " rivillä " .$voih->getLine(). ", tiedostossa " .$voih->getFile(). "<br>";
                }
                else {$viesti = "Virhe tietokantayhteydessä. Yritä hetken päästä uudestaan.<br>";}
                echo ($ajax) ? json_encode($viesti) : "<p>$viesti</p>";
                return false;
            }
        } // Jos ei tullut yhteysvirhettä:
        $yhteys->set_charset("utf8"); // merkistökoodaus ääkkösille sopivaksi
        return $yhteys;
    }

    // funktio, jonka avulla lomakkeen kentässä saadaan näkymään jo syötetty arvo:
    function naytaSyotetty($kentta) {
        return $_POST[$kentta] ? "value='".$_POST[$kentta]."'" : "";
    }
    // funktio, jonka avulla pudotusvalikossa saadaan näkymään jo tehdyt valinnat:
    function naytaValittu($valikko, $valinta) {
        if (isset($_POST[$valikko]) and $_POST[$valikko] == $valinta)
            {return "selected='selected'";}
    }
    // funktio, jonka avulla checkboxissa saadaan näkymään jo laitettu rasti:
    function naytaRasti($boksisetti, $boksin_value) {
        if (isset($_POST[$boksisetti]) and in_array($boksin_value,$_POST[$boksisetti]))
            {return "checked='checked'";}
    }
    // funktio, joka avulla radionapissa saadaan näkymään jo laitettu valinta:
    function naytaRadiovalinta($nappisetti, $napin_value) {
        if (isset($_POST[$nappisetti]) and $_POST[$nappisetti] == $napin_value)
            {return "checked='checked'";}
    }
    // funktio, jonka avulla tulostetaan huomautus pakolliseen kenttään, josta puuttuu arvo:
    function huomautaPuuttuvasta($submitin_nimi, $kentta) {
        if (isset($_POST[$submitin_nimi]) and empty($_POST[$kentta])) {
            if ($kentta == "uutiskirje") {
                return "<p class='lomakevirhe levennettava'>Valitse jompikumpi.</p>";
            }
            return "<p class='lomakevirhe'>Täytä tämä kenttä.</p>";
        }
    } 
    
?>