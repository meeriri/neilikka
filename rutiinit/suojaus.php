<?php
    if (!session_id()) {session_start();}

    // Jos käyttäjä ei ole kirjautunut, uudelleenohjataan kirjautumissivulle.
    // Tällöin kirjautumissivulle välitetään GET-parametri "viesti=kirjaudu",
    // jonka perusteella kirjautumissivu huomioi, että sivulle on tultu tätä kautta.
    if (!isset($_SESSION["loggedin"]) or !$_SESSION["loggedin"]) {
        $_SESSION["aiottu_sivu"] = htmlspecialchars($_SERVER["PHP_SELF"]);
        header("location: login.php?viesti=kirjaudu");
        exit;
    }
?>