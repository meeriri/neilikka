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
        if (file_exists("../rutiinit/lomakkeenkasittely.php"))
            {include("../rutiinit/lomakkeenkasittely.php");}?>
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

        <section>
            <form action="" method="post">
            <fieldset>
                <?php
                    // Tarkistetaan lomakkeen lähetys ja tulostetaan mahdolliset ilmoitukset:
                    tarkista_ja_rekisteroi();
                    echo $onnistuminen;
                    echo $sposti_kaytossa;
                    echo $spostin_vahvistusvirhe;
                    echo $spostin_vahvistus_ok;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="text" name="sposti"
                        <?php echo "value='".$syotteet["sposti"]."'";?>>
                    <?php // Jos on yritetty lähettää lomake ilman kelvollista sähköpostiosoitetta:
                        echo $sposti_puuttuu;
                        echo $sposti_epakelpo; 
                    ?>
                </div>
                <div>
                    <label for="salasana">Aseta salasanasi sivustollemme</label><br>
                    <input id="salasana" type="password" name="salasana"
                        <?php echo "value='".$syotteet["salasana"]."'";?>><br>
                    <?php // Jos on yritetty lähettää lomake ilman kelvollista salasanaa:
                        echo $salasana_puuttuu;
                        echo $salasana_epakelpo; 
                    ?>
                </div>
                <div>
                    <label for="salasana2">Vahvista salasanasi</label><br>
                    <input id="salasana2" type="password" name="salasana2"><br>
                    <?php // Jos on yritetty lähettää lomake, mutta salasanan vahvistus ei onnistu:
                        echo $salasana2_puuttuu;
                        echo $salasana2_epakelpo; 
                    ?>
                </div>
                <div class="radionappikysymys">
                    <label>Saammeko kertoa sinulle uutuuksista ja ajankohtaisista 
                        tarjouksista uutiskirjeellämme?</label><br>
                    <label for="kylla" class="radionappisailio">
                        <input id="kylla" type="radio" name="uutiskirje" value="kyllä"
                            <?php if ($syotteet["uutiskirje"]=="kyllä") {echo "checked='checked'";}?>>
                        <span class="oma_radionappi"></span>
                        Kuulostaa hyvältä!
                    </label>
                    <label for="ei" class="radionappisailio">    
                        <input id="ei" type="radio" name="uutiskirje" value="ei"
                            <?php if ($syotteet["uutiskirje"]=="ei") {echo "checked='checked'";}?>>
                        <span class="oma_radionappi"></span>
                        &nbsp; Ei kiitos tällä kertaa.
                    </label>
                    <?php // Jos on yritetty lähettää lomake ilman uutiskirjevalintaa:
                        echo $uutiskirje_puuttuu;?>
                    <label>Voit muuttaa valintaasi milloin tahansa.</label>
                </div>
                <input type="submit" name="rekisteröidy" value="Rekisteröidy">
            </fieldset>
            </form>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>