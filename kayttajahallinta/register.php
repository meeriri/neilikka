<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="rekisteröinti">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Rekisteröidy &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("../rutiinit/polku.php"))
            {include("../rutiinit/polku.php");}
        if (file_exists("../yhteispalikat/latauslinkit.php"))
            {include("../yhteispalikat/latauslinkit.php");}
        if (file_exists("../rutiinit/tietokantayhteys.php"))
            {include("../rutiinit/tietokantayhteys.php");}?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <h1>Rekisteröityminen</h1>
            <p class="peruskappale">
                Rekisteröitymällä voit tallentaa tuotteitamme toivelistallesi. 
            </p>
            <p class="peruskappale">
                Oletko jo rekisteröitynyt? Kirjaudu sisään 
                <a href="<?php echo $polku?>/kayttajahallinta/login.php">tästä</a>.
            </p>
        </section>

        <section><form>
            <fieldset>
                <label for="sposti">Sähköpostiosoitteesi</label><br>
                <input id="sposti" type="text" name="sposti"><br>
                <label for="salasana">Aseta salasanasi sivustollemme</label><br>
                <input id="salasana" type="password" name="salasana"><br>
                <label for="salasana2">Vahvista salasanasi</label><br>
                <input id="salasana2" type="password"><br>
                <?php if (file_exists("../rutiinit/uutiskirjekysymys.php")) 
                    {include("../rutiinit/uutiskirjekysymys.php");}?>
                <input type="submit" value="Rekisteröidy">
            </fieldset>
        </form></section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>