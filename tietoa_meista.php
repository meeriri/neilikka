<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, Helsinki, Espoo, puutarhakauppa, puutarhatarvikkeet">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">
    <title>Tietoa meistä &#8211; Puutarhaliike Neilikka</title>
    <?php
        if (file_exists("./rutiinit/polku.php"))
            {include("./rutiinit/polku.php");}
        if (file_exists("./yhteispalikat/latauslinkit.php"))
            {include("./yhteispalikat/latauslinkit.php");}?>
</head>

<body>
    <?php if (file_exists("./yhteispalikat/header.php")) 
        {include("./yhteispalikat/header.php");}?>

    <main>
        <article>
            <h1>Tietoa meistä</h1>
            <p class="peruskappale">
                Puutarhaliike Neilikka on vuonna 1990 perustettu puutarhanhoitoon 
                ja huonekasveihin erikoistunut myymäläketju. Ensimmäinen myymälämme 
                perustettiin Helsingin Fabianinkadulle, ja toukokuussa 1997 perustimme 
                ketjun toisen myymälän Espooseen.
            </p>
            <p class="peruskappale">
                Meiltä löydät kattavan valikoiman sisä- ja ulkokasveja sekä 
                tietysti kaikki työkalut ja muut tarvikkeet niiden hoitoon. 
                Osaava ja puutarhanhoidosta innostunut henkilökuntamme on aina 
                valmiina auttamaan sinua valitsemaan juuri sinulle sopivimmat tuotteet.
            </p>
        </article>
    </main>

    <?php if (file_exists("./yhteispalikat/footer.php")) 
        {include("./yhteispalikat/footer.php");}?>

</body>

</html>