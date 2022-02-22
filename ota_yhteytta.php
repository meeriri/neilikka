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
            {include("./yhteispalikat/latauslinkit.php");}
        if (file_exists("./rutiinit/lomakkeenkasittely.php"))
            {include("./rutiinit/lomakkeenkasittely.php");}?>
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
                <div>
                    <label for="nimi">Nimesi</label><br>
                    <input id="nimi" type="text" name="nimi">
                </div>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="text" name="sposti">
                </div>
                <div>
                    <label for="aihe">Viestin aihe</label><br>
                    <select id="aihe" name="aihe">
                        <option value="tuotteet">Kysymys tuotteistamme</option>
                        <option value="tilaus">Tilaus</option>
                        <option value="yhteydenotto">Yhteydenottopyyntö</option>
                        <option value="muu">Muu</option>
                    </select>
                </div>
                <div>
                    <label for="viesti">Viestisi</label><br>
                    <textarea id="viesti"></textarea>
                </div>
                <div>
                    <label for="tilaus" class="checkbox_sailio">
                        <input type="checkbox" id="tilaus" name="tilaus" value="kyllä" 
                            <?php echo naytaRasti("tilaus","kyllä")?>>
                        <span class="oma_checkbox"></span>
                        Tilaan samalla Neilikan uutiskirjeen.<br>
                        Kirjeessä kerrotaan uutuuksista ja ajankohtaisista tarjouksista.
                        Tilauksen voi perua milloin tahansa.
                    </label>
                </div>
                <input type="submit" value="Lähetä">
            </fieldset>
        </form></section>
    </main>

    <?php if (file_exists("./yhteispalikat/footer.php")) 
        {include("./yhteispalikat/footer.php");}?>

</body>

</html>