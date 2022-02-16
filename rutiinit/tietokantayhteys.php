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
?>