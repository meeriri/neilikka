<?php
    // Tarkistetaan kirjautuminen ja perustetaan istunto:
    include("../rutiinit/suojaus.php");
?>

<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="profiilisivu">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Oma sivusi &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("../rutiinit/polku.php"))
            {include("../rutiinit/polku.php");}
        if (file_exists("../yhteispalikat/latauslinkit.php"))
            {include("../yhteispalikat/latauslinkit.php");}
        if (file_exists("../rutiinit/lomake.php"))
            {include("../rutiinit/lomake.php");}?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <?php // Jos sivulle on tultu kirjautumisen onnistuttua
                // (jolloin sivun osoitteeseen on lisätty GET-parametri "tervetuloa"):
                if (isset($_GET["tervetuloa"])) {
                    echo ok_tagit("Kirjautuminen onnistui.")."<br>";
                }
                // Jos sivulle on tultu siksi, että on avattu login-sivu jo kirjautuneena:
                if (isset($_GET["kirjauduttu"])) {
                    echo ok_tagit("Olet kirjautunut käyttäjänä ".$_SESSION["sposti"].".")."<br>";
                }
            ?>
            <h1>Oma sivusi</h1>
            <p class="peruskappale">
                Täältä näet ja päivität omat tietosi Neilikan käyttäjärekisterissä.<br>
                Omalta sivultasi löytyy myös toivelistasi.
            </p>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>