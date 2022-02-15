<?php
    include ("../tunnukset.php");
    $yhteys = new mysqli($palvelin, $db_kayttaja, $db_salasana, $tietokanta); // luodaan yhteys

    // jos yhteyden muodostaminen ei onnistunut, keskeytä
    if ($yhteys->connect_error) {
        die("Yhteyden muodostaminen epäonnistui: " . $yhteys->connect_error);
    }
    $yhteys->set_charset("utf8"); // merkistökoodaus (muuten ääkköset sekoavat)
?>