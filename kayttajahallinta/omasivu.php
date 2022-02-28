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
            {include("../yhteispalikat/latauslinkit.php");}?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <?php // Jos sivulle on tultu suoraan kirjautumissivulta
                // (jolloin sivun osoitteeseen on lisätty GET-parametri "tervetuloa"):
                if (isset($_GET["tervetuloa"])) {
                    echo "<p class='lomake_ok'>Kirjautuminen onnistui.</p><br>";
                }?>
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