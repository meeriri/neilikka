<?php
    if (!session_id()) {session_start();}

    // Jos käyttäjä ei ole kirjautunut, uudelleenohjataan kirjautumissivulle 
    // ja viestitään saapumisreitti GET-parametrilla:
    if (!isset($_SESSION["loggedin"]) or !$_SESSION["loggedin"]) {
        if (isset($_GET["logout"])) {
            // Jos juuri kirjauduttiin ulos tältä sivulta:
            header("location: login.php?logout=1");
        } else {
            // Jos taas yritettiin tulla sivulle kirjautumatta, otetaan sivu talteen,
            // jotta tänne voidaan palata kirjautumisen jälkeen:
            $_SESSION["aiottu_sivu"] = htmlspecialchars($_SERVER["PHP_SELF"]);
            header("location: login.php?suojattu=1");
        }
        exit;
    }
?>