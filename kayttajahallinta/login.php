<?php
    if (!session_id()) {session_start();}

    // Jos kirjautuminen on jo voimassa, uudelleenohjataan omalle sivulle
    // lähettäen sinne GET-parametri "kirjauduttu"
    // (jolloin omasivu huomioi, että sinne saavuttiin tätä kautta):
    if (isset($_SESSION["loggedin"]) and $_SESSION["loggedin"]) {
        header("location: omasivu.php?kirjauduttu=1");
        exit;
    }
?>

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
        if (file_exists("../rutiinit/lomake_login.php"))
            {include("../rutiinit/lomake_login.php");}
        // Tarkistetaan, onko lomake lähetetty, ja muodostetaan mahdolliset virheilmoitukset
        // (tehdään tämä ennen kuin sivulle ladataan mitään, jotta tarvittaessa voidaan uudelleenohjata): 
        tarkista_ja_kirjaudu();?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <?php // Tulostetaan ohjeistus, jos on yritetty mennä kirjautumatta suojatulle sivulle
                // (tällöin suojaus.php on ohjannut login-sivulle GET-parametrilla "suojattu"):
                if (isset($_GET["suojattu"])) {
                    echo virhetagit("Kirjaudu ensin sisään.")."<br>";
                }
                // Tulostetaan ohjeistus, jos sivulle on tultu uloskirjautumisen jälkeen suojatulta sivulta:
                if (isset($_GET["logout"])) {
                    echo ok_tagit("Olet kirjautunut ulos.")."<br>";
                }
                ?>
            <h1>Kirjautuminen</h1>
            <p class="lomake_ok">
                Jos et ole vielä rekisteröitynyt, pääset tekemään sen
                <a href="<?php echo $polku?>/kayttajahallinta/register.php">tästä</a>.
            </p>
        </section>

        <section>
            <form action="" method="post">
            <fieldset>
                <?php
                    // Tulostetaan mahdolliset virheilmoitukset:
                    echo $yhteysvirhe;
                    echo $tilia_ei_ole;
                    echo $sposti_vahvistamatta;
                    echo $vaara_salasana;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="email" name="sposti"
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
                        <span class="oma_checkbox"></span>
                        &emsp;&emsp;&ensp;Muista tiedot.<br>
                        <span class="pikkuteksti">Jos rastit tämän, asetamme selaimellesi evästeen,
                            jonka ansiosta selain muistaa kirjautumisesi 30 päivän ajan. Evästettä
                            ei käytetä muuhun tarkoitukseen.</span>
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
