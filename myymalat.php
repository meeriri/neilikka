<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, Helsinki, Espoo, myymalat">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">

    <title>Myymälät &#8211; Puutarhaliike Neilikka</title>

    <?php if (file_exists("./yhteispalikat/latauslinkit.html"))
        {include("./yhteispalikat/latauslinkit.html");}?>
</head>

<body>
    <?php if (file_exists("./yhteispalikat/header.php")) 
        {include("./yhteispalikat/header.php");}?>

    <main>
        <section>
            <h1>Myymälämme</h1>
            <p class="peruskappale">
                Puutarhaliike Neilikalla on kaksi myymälää pääkaupunkiseudulla.<br>
                Myymälämme ovat avaria ja väljästi sisustettuja, jotta asiointi olisi turvallista.<br>
                Myyjämme käyttävät maskia ja desinfioimme pintoja säännöllisesti.<br>
            </p>
        </section>

        <section>
            <article class="myymalakortti">
                <img src="./kuvat/pexels-daria-shevtsova-709824.jpg" 
                    alt="Amina Filkinsin tyttö puutarhamyymälässä">
                <address><h2>Puutarhaliike Neilikka, Helsinki</h2>
                    Fabianinkatu 42<br>
                    00100 Helsinki<br>
                    p. 09 123 4567<br>
                    sähköposti: helsinki@puutarhaliikeneilikka.fi
                    <p class="pikkuteksti"><br>Avoinna</p>
                    <p> ma&#8211;ke 9&#8211;17<br>la 12&#8211;18</p>
                </address>
            </article>
            <article class="myymalakortti">
                <img src="./kuvat/pexels-daria-shevtsova-709824.jpg" 
                    alt="Amina Filkinsin tyttö puutarhamyymälässä">
                <address><h2>Puutarhaliike Neilikka, Espoo</h2>
                    Kivenlahdentie 10<br>
                    01234 Espoo<br>
                    p. 09 123 4568<br>
                    sähköposti: espoo@puutarhaliikeneilikka.fi
                    <p class="pikkuteksti"><br>Avoinna</p>
                    <p> ma&#8211;ke 9&#8211;17<br>la 12&#8211;18</p>
                </address>
            </article>

        </section>
    </main>

    <?php if (file_exists("./yhteispalikat/footer.php")) 
        {include("./yhteispalikat/footer.php");}?>

</body>

</html>