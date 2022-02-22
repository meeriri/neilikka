<?php
    if (!session_id()) session_start(); 

    // Jos käyttäjä ei ole kirjautunut, uudelleenohjataan kirjautumissivulle.
    // Tällöin kirjautumissivulle välitetään GET-parametri "viesti=kirjaudu",
    // jonka perusteella kirjautumissivu huomioi, että sivulle on tultu tätä kautta.
    if (!isset($_SESSION['ktunnus'])) header("Location: login.php?viesti=kirjaudu"); 

// yhdistä edelliseen slackista:

if (!session_id()) session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
   $_SESSION['next_page'] = $_SERVER['PHP_SELF'];
   header("location: login.php");
   exit;
}
?>