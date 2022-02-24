<?php
    // Haetaan tunnukset root-kansion yläkansiosta:
    if (file_exists(dirname(__DIR__,2)."/tunnukset.php")) {
        include_once(dirname(__DIR__,2)."/tunnukset.php");
    } else { // Jos ei onnistu, keskeytä:
        echo "<p class='lomakevirhe'>Tietokantaan ei saada yhteyttä. Yritä myöhemmin uudelleen.</p>";
        exit;
    }
    // Funktio, joka luo yhteyden tietokantaan:
    // (DEBUG-jutun voi jättää poiskin, kunhan toimii)
    function db_yhteys() {
        // Tallennetaan yhteys staattiseen muuttujaan, jolloin samaa yhteyttä
        // ei tarvitse asettaa kuin kerran:
        static $yhteys;
        if (!isset($yhteys) or empty($yhteys)) {
            try {
                @$yhteys = new mysqli(PALVELIN, DB_KAYTTAJA, DB_SALASANA, TIETOKANTA);
                $yhteys->set_charset("utf8"); // merkistökoodaus ääkkösille sopivaksi
            } catch(Exception $voih) { // Poikkeuksen käsittely:
                if (defined("DEBUG") and DEBUG) {
                    $viesti = "Poikkeus ".$voih->getCode().": ".$voih->getMessage().
                        " rivillä ".$voih->getLine().", tiedostossa ".$voih->getFile()."<br>";
                } else {$viesti = "Virhe tietokantayhteydessä. Yritä hetken päästä uudestaan.<br>";}
                echo "<p class='lomakevirhe'>$viesti</p>";
                return false;
            }
        } // Jos ei tullut yhteysvirhettä:
        return $yhteys;
    }
    
?>