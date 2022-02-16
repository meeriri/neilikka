/* Kirjautumisen session-käsittely Slackista*/
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
 header("location: welcome.php");

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
            {include("../yhteispalikat/latauslinkit.php");}?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section class="tervetuloteksti">
            <h1>Tervetuloa</h1>
            <p class="peruskappale">
                Puutarhaliike Neilikan kotisivuille!<br>
            </p>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>
