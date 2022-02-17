<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, Helsinki, Espoo, yhteydenotto">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Ota yhteyttä &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("./rutiinit/polku.php"))
            {include("./rutiinit/polku.php");}
        if (file_exists("./yhteispalikat/latauslinkit.php"))
            {include("./yhteispalikat/latauslinkit.php");}?>
</head>

<body>
    <?php if (file_exists("./yhteispalikat/header.php")) 
        {include("./yhteispalikat/header.php");}?>

    <main>
        <section>
            <h1>Ota yhteyttä!</h1>
            <p class="peruskappale">
                Kuulemme sinusta mielellämme.<br>
                Saat meidät kiinni monin tavoin:
                <ul>
                    <li>puhelimitse yksittäisistä 
                        <a href="myymalat.php">myymälöistä</a></li>
                    <li>sähköpostitse: 
                        <a href="mailto:meerin.testitili@gmail.com">
                            asiakaspalvelu@puutarhaliikeneilikka.fi</a></li>
                    <li>alla olevalla lomakkeella.</li>
                </ul>
            </p>
        </section>

        <section><form>
            <fieldset>
                <!--legend>Yhteydenottolomake</legend-->
                <label for="nimi">Nimesi</label><br>
                <input id="nimi" type="text" name="nimi"><br>
                <label for="sposti">Sähköpostiosoitteesi</label><br>
                <input id="sposti" type="text" name="sposti"><br>
                <label for="aihe">Viestin aihe</label><br>
                <select id="aihe" name="aihe">
                    <option value="tuotteet">Kysymys tuotteistamme</option>
                    <option value="tilaus">Tilaus</option>
                    <option value="yhteydenotto">Yhteydenottopyyntö</option>
                    <option value="muu">Muu</option>
                </select><br>
                <label for="viesti">Viestisi</label><br>
                <textarea id="viesti"></textarea><br>
                <?php if (file_exists("./rutiinit/uutiskirjekysymys.php")) 
                    {include("./rutiinit/uutiskirjekysymys.php");}?>
                <input type="submit" value="Lähetä">
            </fieldset>
        </form></section>
    </main>

    <?php if (file_exists("./yhteispalikat/footer.php")) 
        {include("./yhteispalikat/footer.php");}?>

</body>

</html>