<?php if (!session_id()) {session_start();}?>

<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, kirjautuminen">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Kirjaudu &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("../rutiinit/polku.php"))
            {include("../rutiinit/polku.php");}
        if (file_exists("../yhteispalikat/latauslinkit.php"))
            {include("../yhteispalikat/latauslinkit.php");}
        if (file_exists("../rutiinit/lomakkeenkasittely.php"))
            {include("../rutiinit/lomakkeenkasittely.php");}
        // Tarkistetaan, onko lomake lähetetty, ja muodostetaan mahdolliset virheilmoitukset
        // (tehdään tämä ennen kuin sivulle ladataan mitään, jotta tarvittaessa voidaan uudelleenohjata): 
        tarkista_ja_kirjaudu();?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <h1>Kirjautuminen</h1>
            <p class="peruskappale">
                Jos et ole vielä rekisteröitynyt, pääset tekemään sen 
                <a href="<?php echo $polku?>/kayttajahallinta/register.php">tästä</a>.
            </p>
        </section>

        <section>
            <form action="" method="post">
            <fieldset>
                <?php
                    // Tulostetaan mahdolliset virheilmoitukset:
                    echo $tilia_ei_ole;
                    echo $sposti_vahvistamatta;
                    echo $vaara_salasana;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="text" name="sposti"
                        <?php echo "value='".$syotteet["sposti"]."'";?>>
                    <?php echo $sposti_ei_kelpaa; // Tulostetaan mahdollinen virheilmoitus ?>
                </div>
                <div>
                    <label for="salasana">Salasanasi sivustollemme</label><br>
                    <input id="salasana" type="password" name="salasana"
                        <?php echo "value='".$syotteet["salasana"]."'";?>><br>
                    <?php echo $salasana_puuttuu;?>
                </div>
                <div>
                    <label for="muista" class="checkbox_sailio">
                        <input type="checkbox" id="muista" name="muista" value="kyllä" 
                            <?php if ($syotteet["muista"]=="kyllä") {echo "checked='checked'";}?>>
                        &emsp;&emsp;&ensp;<span class="oma_checkbox keskemmalle"></span>
                        Muista tiedot 30 päivän ajan.<br>
                        <span class="pikkuteksti">Jotta muistaminen olisi mahdollista, asennamme
                            koneellesi evästeen.</span>
                    </label>
                </div>
                <input type="submit" name="kirjaudu" value="Kirjaudu">
                <a class="pikkuteksti" href="<?php echo $polku?>/kayttajahallinta/forgot_password.php">
                    Unohditko salasanasi?</a><br>
            </fieldset>
            </form>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>
