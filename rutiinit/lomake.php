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
    function virhetagit($teksti, $lisaluokka = "") {
        return "<p class='lomakevirhe $lisaluokka'>$teksti</p>";
    }
    // Apufunktio, joka lisää tekstin ympärille lomake_ok-luokan p-tägit:
    function ok_tagit($teksti) {
        return "<p class='lomake_ok'>$teksti</p>";
    }

    // Apufunktio, joka käsittelee poikkeuksia (tällä hetkellä käytössä vain tietokantakomennoille):
    function kasittele_poikkeus($poikkeus){
        if (defined("DEBUG") and DEBUG) {
            echo "Poikkeus: ".$poikkeus->getCode().": ".$poikkeus->getMessage().
            "<br>rivi: ".$poikkeus->getLine().", tiedosto: ".$poikkeus->getFile()."<br><br>";
        }
    }
?>