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
                Meiltä löydät sekä sisä- että ulkokasvit ja kaiken tarvitsemasi kasvien hoitoon.<br>
                Inspiroivat myymälämme sijaitsevat Helsingissä ja Espoossa.<br>
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

            <article>
                <p class="pikkuteksti">14.12.2015</p>
                <p>Joulukukat edullisesti meiltä.<br>
                    Myymälöissämme myös kattava ja edullinen valikoima joulukuusia!
                </p>
            </article>

            <article>
                <p class="pikkuteksti">1.9.2015</p>
                <p>Nyt on hyvä aika aloittaa puutarhan valmistelu talven lepokautta varten.
                    <br>Meiltä löydät kaikki <a href="tyokalut.html">työkalut ja tarvikkeet</a>.
                </p>
            </article>
        </section>
    </main>

    <?php if (file_exists("../yhteispalikat/footer.php")) 
        {include("../yhteispalikat/footer.php");}?>

</body>

</html>