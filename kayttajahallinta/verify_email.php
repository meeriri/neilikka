<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="sähköpostin vahvistus">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Vahvista sähköpostiosoitteesi &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("../rutiinit/polku.php"))
            {include("../rutiinit/polku.php");}
        if (file_exists("../yhteispalikat/latauslinkit.php"))
            {include("../yhteispalikat/latauslinkit.php");}
        if (file_exists("../rutiinit/lomake_verify.php"))
            {include("../rutiinit/lomake_verify.php");}?>
</head>

<body>
    <?php if (file_exists("../yhteispalikat/header.php")) 
        {include("../yhteispalikat/header.php");}?>

    <main>
        <section>
            <h1>Sähköpostiosoitteen vahvistus</h1>
            <?php
                // Yritetään vahvistaa sposti ja aktivoida käyttäjätili
                // sekä tulostetaan mahdolliset virheilmoitukset:
                vahvista_ja_aktivoi();
                echo $yhteysvirhe;
                echo $poletti_vanhentunut;
                echo $jo_vahvistettu;
                echo $vahvistaminen_ok;
                echo $virhe_aktivoinnissa;
                if (isset($_GET["uusi"])) {echo "<p>Jos et saanut vahvistuslinkkiä sähköpostiisi,
                    tilaa alta uusi.</p>";}
            
                // Jos sähköpostivahvistuksen poletti oli vanhentunut, aktivointi ei muusta syystä
                // onnistunut tai jos sivulle on saavuttu rekisteröintisivun virheen kautta,
                // tulostetaan lomake, jolla käyttäjä voi tilata uuden poletin:
                if (!empty($poletti_vanhentunut) or !empty($virhe_aktivoinnissa)
                or isset($_GET["uusi"]) or isset($_POST["tilaa_poletti"])) {
                    tilaa_uusi_poletti(); // Yritetään tehdä polettitilaus ja muodostetaan ilmoitukset
                    echo "<br>";
                    echo '<form action="'.$polku.'/kayttajahallinta/verify_email.php"
                        method="post"><fieldset>'.
                        $tarkista_tilaus.$poletti_tilattu.
                        '<div><label for="sposti">Sähköpostiosoitteesi</label><br>
                        <input id="sposti" type="email" name="sposti"
                        value="'.$poletti_sp.'"></div>
                        <div><label for="salasana">Salasanasi sivustollemme</label><br>
                        <input id="salasana" type="password" name="salasana"><br></div>
                        <input type="submit" name="tilaa_poletti" value="Tilaa linkki">
                        </fieldset></form>';
                }
            ?>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>
<?php sulje_yhteys(); ?>
</html>