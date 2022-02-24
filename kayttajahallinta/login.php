<?php /* Kirjautumisen session-käsittely Slackista
..
if(password_verify($password, $hashed_password)){
// Password is correct, so start a new session
 if (!session_id()) session_start();
 // Store data in session variables
 $_SESSION["loggedin"] = true;
 // $_SESSION["id"] = $id;
 // $_SESSION["username"] = $username;                            
 // Redirect user to welcome page
 if (isset($_SESSION['next_page']){
   $next_page = $_SESSION['next_page'];
   unset($_SESSION['next_page']);
   header("location: $next_page");
   exit;
   }
 header("location: welcome.php"); */
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
        if (file_exists("../rutiinit/lomakkeenkasittely.php"))
            {include("../rutiinit/lomakkeenkasittely.php");}?>
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
                    // Tarkistetaan lomakkeen lähetys ja tulostetaan mahdolliset virheilmoitukset:
                    tarkista_ja_kirjaudu();
                    echo $tilia_ei_ole;
                    echo $tili_vahvistamatta;
                    echo $vaara_salasana;
                ?>
                <div>
                    <label for="sposti">Sähköpostiosoitteesi</label><br>
                    <input id="sposti" type="text" name="sposti"
                        <?php echo "value='".$syotteet["sposti"]."'";?>>
                    <?php echo $sposti_puuttuu; // Tulostetaan mahdollinen virheilmoitus ?>
                </div>
                <div>
                    <label for="salasana">Salasanasi sivustollemme</label><br>
                    <input id="salasana" type="password" name="salasana"
                        <?php echo "value='".$syotteet["salasana"]."'";?>><br>
                    <?php echo $salasana_puuttuu; ?>
                </div>
                <div>
                    <label for="muista" class="checkbox_sailio">
                        <input type="checkbox" id="muista" name="muista" value="kyllä" 
                            <?php if ($syotteet["muista"]=="kyllä") {echo "checked='checked'";}?>>
                        &emsp;&emsp;&ensp;<span class="oma_checkbox keskemmalle"></span>
                        Muista tiedot 30 päivän ajan.<br>
                        <span class="pikkuteksti">Jotta muistaminen olisi mahdollista, asennamme
                            koneellesi evästeen.<br>
                    </label>

                </div>
                <input type="submit" name="kirjaudu" value="Kirjaudu">
            </fieldset>
            </form>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>
