<?php
    if (!session_id()) session_start(); 

    // Jos käyttäjä ei ole kirjautunut, uudelleenohjataan kirjautumissivulle.
    // Tällöin kirjautumissivulle välitetään GET-parametri "viesti=kirjaudu",
    // jonka perusteella kirjautumissivu huomioi, että sivulle on tultu tätä kautta.
    if (!isset($_SESSION['ktunnus'])) header("Location: ../login.php?viesti=kirjaudu"); 
?>