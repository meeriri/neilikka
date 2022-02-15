<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, Helsinki, Espoo, puutarhakauppa, puutarhatarvikkeet">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">

    <title>Puutarhaliike Neilikka</title>

    <?php if (file_exists("../yhteispalikat/polku.php"))
        {include("../yhteispalikat/polku.php");}?>

    <?php if (file_exists("../yhteispalikat/latauslinkit.php"))
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

        <section>
            <h1>Uutisia</h1>
            <article>
                <p class="pikkuteksti">2.1.2016</p>
                <p>Hyvää uutta vuotta!<br>
                   Uudenvuoden kunniaksi myymälöissämme upeita tarjouksia.
                </p>
            </article>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>