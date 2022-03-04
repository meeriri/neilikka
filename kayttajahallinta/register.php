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
        if (file_exists("../rutiinit/lomake_register.php"))
            {include("../rutiinit/lomake_register.php");}?>
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
            <?php 
                // Jos käyttäjän selaimelta on joku kirjautuneena sisään:
                if (isset($_SESSION["loggedin"]) and $_SESSION["loggedin"]) {
                    echo virhetagit("Olet kirjautuneena käyttäjänä ".$_SESSION["sposti"].
                        ". Jos haluat rekisteröidä toisen käyttäjätilin, kirjaudu ensin
                        <a href='".$polku."/rutiinit/logout.php?ulos=1'>ulos</a>.");
                } elseif (!isset($_POST["rekisteröidy"])) { 
                    // Jos ei olla kirjautuneena eikä lomaketta ole yritetty lähettää:
                    echo ok_tagit("Oletko jo rekisteröitynyt? Kirjaudu sisään 
                        <a href='".$polku."/kayttajahallinta/login.php'>tästä</a>.");
                }
            ?>            
        </section>

        <section>
            <form action="" method="post">
            <fieldset>
                <?php
                    // Tarkistetaan lomakkeen lähetys ja tulostetaan mahdolliset ilmoitukset:
                    tarkista_ja_rekisteroi();
                    echo $onnistuminen;
                    echo $yhteysvirhe;
                    echo $sposti_kaytossa;
                    echo $vahvistus_lahettamatta;
                    echo $vahvistus_lahetetty;
                    echo $postituslistavirhe;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="email" name="sposti"
                        <?php echo "value='".$syotteet["sposti"]."'";?>>
                    <?php // Tulostetaan mahdolliset virheilmoitukset:
                        echo $sposti_puuttuu;
                        echo $sposti_epakelpo; 
                    ?>
                </div>
                <div>
                    <label for="salasana">Aseta salasanasi sivustollemme</label><br>
                    <input id="salasana" type="password" name="salasana"
                        <?php echo "value='".$syotteet["salasana"]."'";?>><br>
                    <?php
                        echo $salasana_puuttuu;
                        echo $salasana_epakelpo; 
                    ?>
                </div>
                <div>
                    <label for="salasana2">Vahvista salasanasi</label><br>
                    <input id="salasana2" type="password" name="salasana2"
                        <?php echo "value='".$syotteet["salasana2"]."'";?>><br>
                    <?php
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
                    <?php echo $uutiskirje_puuttuu;?>
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
<?php sulje_yhteys(); ?>
</html>