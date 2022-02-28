<?php
    if (!session_id()) {session_start();}

    // Jos käyttäjä ei ole kirjautunut, uudelleenohjataan kirjautumissivulle.
    // Tällöin kirjautumissivulle välitetään GET-parametri "suojattu",
    // jonka perusteella kirjautumissivu huomioi, että sinne saavuttiin tätä kautta.
    if (!isset($_SESSION["loggedin"]) or !$_SESSION["loggedin"]) {
        $_SESSION["aiottu_sivu"] = htmlspecialchars($_SERVER["PHP_SELF"]);
        header("location: login.php?suojattu=1");
        exit;
    }
?>