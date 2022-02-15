<?php
    // Projektikansion nimi:
    $projekti = "neilikka";

    // Jos sivua kutsuva (remote) palvelin on tarjoava (local) palvelin:
    if (in_array($_SERVER["REMOTE_ADDR"], array("127.0.0.1","REMOTE_ADDR"=>"::1"))) {
        $polku = "http://localhost/koulu";
    }
    else { // Jos kutsu tulee muualta, huomioidaan yhteyden tyyppi (https vai http):
        $polku = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
        $polku .= $_SERVER["SERVER_NAME"]; // ja lisätään palvelimen osoite
            // (edellä SERVER_NAMEn tilalla voisi olla HTTP_HOST)
    }

    // Lisätään polun loppuun projektikansion nimi:
    $polku .= "/$projekti"; 
    echo $polku;
?>