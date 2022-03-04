<?php
    // Haetaan tunnukset root-kansion yläkansiosta:
    if (file_exists(dirname(__DIR__,2)."/tunnukset.php")) {
        include_once(dirname(__DIR__,2)."/tunnukset.php");
    } else { // Jos ei onnistu, keskeytä:
        echo "<p class='lomakevirhe'>Tietokantaan ei saada yhteyttä. Yritä myöhemmin uudelleen.</p>";
        exit;
    }
    // Funktio, joka luo yhteyden tietokantaan:
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

    function sulje_yhteys() {
        global $yhteys;
        $yhteys->close();
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
    // statuksen - aktivoitu, aktivoimatta tai tilia_ei_ole:
    function tilin_status($sposti) {
        $hakutulos = hae_kayttaja($sposti, "user");
        if ($hakutulos->num_rows > 0) { // Jos haulla löytyi käyttäjä, tutkitaan tulokset:
            if ($hakutulos->fetch_assoc()["activated"]=="0") { // Tili aktivoimatta
                return "aktivoimatta";
            } else {return "aktivoitu";} // Tili aktivoitu
        } else {return "tilia_ei_ole";} // Haku ei löytänyt käyttäjää
    }

    // Apufunktio, joka tarkistaa, onko annettu käyttäjätili (user_id) aktivoitu vai ei:
    function aktivoitu($user_id) {
        global $yhteys; // yhteys näkyväksi
        $hakulauseke = $yhteys->prepare("SELECT activated FROM user WHERE user_id = ?");
        $hakulauseke->bind_param("i", $user_id);
        $hakulauseke->execute();
        $tulos = $hakulauseke->get_result();
        $hakulauseke->close();
        if ($tulos->fetch_row()[0] == 0) {return false;}
        else {return true;}
    }

    // Apufunktio, jolla asetetaan annettu käyttäjätili aktivoiduksi:
    function aktivoi_tili($user_id) {
        global $yhteys; // yhteys näkyväksi
        $muutoslauseke = $yhteys->prepare("UPDATE user SET activated='1' WHERE user_id = ?");
        $muutoslauseke->bind_param("i", $user_id);
        $muutoslauseke->execute();
        $muutoslauseke->close();
    }

    // Apufunktio, joka lisää käyttäjän user-tauluun ja palauttaa tämän saaman user_id-numeron:
    function lisaa_kayttaja($sposti, $salasana) {
        global $yhteys; // yhteys näkyväksi
        $salasana_hash = password_hash($salasana, PASSWORD_BCRYPT); // Sekoitetaan salasana
        $lisayslauseke = $yhteys->prepare("INSERT INTO user (email, passhash, activated)
            VALUES (?, ?, '0')");
        $lisayslauseke->bind_param("ss", $sposti, $salasana_hash);
        $lisayslauseke->execute();
        $uusi_id = $lisayslauseke->insert_id;
        $lisayslauseke->close();
        return $uusi_id;
    }

    // Apufunktio, joka etsii annettua polettia annetusta tietokannan taulusta ja
    // palauttaa hakutuloksen:
    function hae_poletti($poletti, $taulu) {
        global $yhteys; // yhteys näkyväksi
        $hakulauseke = $yhteys->prepare("SELECT * FROM ".$taulu." WHERE token = ?");
        $hakulauseke->bind_param("s", $poletti);
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
        $polettilauseke->bind_param("iss", $kayttajan_id, $poletti, $vanhenee);
        $polettilauseke->execute();
        $polettilauseke->close();
    }

    // Apufunktio, joka poistaa annettua käyttäjää koskevat tietueet annetusta taulusta
    // (taulussa on oltava user_id-kenttä):
    function poista_tietueet($user_id, $taulu) {
        global $yhteys; // yhteys näkyväksi
        // Valmistellaan ja suoritetaan rivin poisto:
        $poistolauseke = $yhteys->prepare("DELETE FROM ".$taulu." WHERE user_id = ?");
        $poistolauseke->bind_param("i", $user_id);
        $poistolauseke->execute();
        $poistolauseke->close();
    }
    
    // Apufunktio, joka lisää annetun sähköpostiosoitteen email_list-tauluun (sähköpostilistalle),
    // ellei osoite jo ole siellä:
    function lisaa_listalle($sposti) {
        global $yhteys; // yhteys näkyväksi
        $hakutulos = hae_kayttaja($sposti, "email_list"); // Tarkistetaan, onko jo listalla
        if ($hakutulos->num_rows == 0) { // Jos ei ole vielä listalla, yritetään lisäystä:
            $uutislauseke = $yhteys->prepare("INSERT INTO email_list (email) VALUES (?)");
            $uutislauseke->bind_param("s", $sposti);
            $uutislauseke->execute();
            $uutislauseke->close();
        }
    }
?>