<?php
// tämä koodi kopioituna seuraaviin tiedostoihin:
// header, latauslinkit
    $projekti = "koulu/neilikka";
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on")
        {$polku = "https://";}
    else
        {$polku = "http://";}
    $polku .= $_SERVER["SERVER_NAME"] . "/" . $projekti;
        // edellä SERVER_NAMEn tilalla voisi olla HTTP_HOST
?>