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

        <section>
            <form action="" method="post">
            <fieldset>
                <?php // Jos lomake on lähetetty, kerrotaan, onnistuiko:
                    echo $onnistuminen;
                    echo $sposti_kaytossa;
                    echo $spostin_vahvistusvirhe;
                    echo $spostin_vahvistus_ok;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="text" name="sposti"
                        <?php // Jos on yritetty lähettää lomake, mutta ei onnistuttu,
                            // täytetään kenttä aiemmalla syötteellä:
                            if (!empty($_POST["rekisteröidy"]) and empty($onnistuminen)) 
                                {echo naytaSyotetty("sposti");}
                        ?>>
                        <?php // Jos on yritetty lähettää lomake, mutta annettu sposti ei kelpaa:
                        echo huomautaPuuttuvasta("rekisteröidy","sposti");
                        echo $sposti_epakelpo; 
                        ?>
                </div>
                <div>
                    <label for="salasana">Aseta salasanasi sivustollemme</label><br>
                    <input id="salasana" type="password" name="salasana"><br>
                    <?php // Jos on yritetty lähettää lomake, mutta annettu salasana ei kelpaa:
                        echo huomautaPuuttuvasta("rekisteröidy","salasana");
                        echo $salasana_epakelpo; 
                    ?>
                </div>
                <div>
                    <label for="salasana2">Vahvista salasanasi</label><br>
                    <input id="salasana2" type="password" name="salasana2"><br>
                    <?php // Jos on yritetty lähettää lomake, mutta salasanan vahvistus ei kelpaa:
                        echo huomautaPuuttuvasta("rekisteröidy","salasana2");
                        echo $salasana2_epakelpo; 
                    ?>
                </div>
                <?php if (file_exists("../yhteispalikat/uutiskirjekysymys.php")) 
                    {include("../yhteispalikat/uutiskirjekysymys.php");}
                ?>
                <input type="submit" name="rekisteröidy" value="Rekisteröidy">
            </fieldset>
            </form>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>