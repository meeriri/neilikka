<!DOCTYPE html>
<html lang=fi>

<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8"> <!-- ääkköset ym. -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsiivisuus -->
    <meta name="keywords" content="kuvitteellinen, puutarhaliike, Helsinki, Espoo, puutarhakauppa, puutarhatarvikkeet">
    <meta name="description" content="Koulutehtävänä tehdyt kuvitteellisen puutarhaliike Neilikan kotisivut">
    <meta name="author" content="Meeri Rivakka">

    <title>Puutarhaliike Neilikka</title>

    <!-- ladataan Googlen fontit Quicksand ja Great Vibes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">

    <!-- ladataan Font Awesomen kuvakkeet navia ja some-kuvakkeita varten -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.10.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- ladataan oma tyylitiedosto -->
    <link rel="stylesheet" type="text/css" href="neilikkatyyli.css"> <!-- linkki tyylitiedostoon -->
</head>

<body>
    <header>
        <div class="ilmoituspalkki pikkuteksti">
            <p>Tervetuloa uudistuneelle sivustollemme!</p>
        </div>

        <div class="kuva_tekstilla">
            <p class="kuvan_textbox">
                <span class="pikkuteksti">Epidemiatilanne</span><br><br>
                Myymälämme ovat avoinna normaalisti.<br><br>
                ma&#8211;ke 9&#8211;17 <br>la 12&#8211;18
            </p>
        </div>
    
        <a id="etusivulinkki" href="etusivu.html">Puutarhaliike Neilikka</a>
    
        <!-- lisätään kuvakkeena näkyvä checkbox, jonka avulla voidaan avata menu.
            Kuvake löytyy luokkien fas ja fa-bars avulla Font Awesomesta. -->
        <input type="checkbox" id="piilonappi"> 
        <label for="piilonappi" class="menun-nayttonappi"><i class="fas fa-bars"></i></label>
        
        <nav>
            <ul class="paavalikko">
                <li class="pudotusvalikko"><span class="ylaotsikko">Tuotteet</span>
                    <ul class="alavalikko">
                        <li><a href="sisakasvit.html">Sisäkasvit</a></li>
                        <li><a href="ulkokasvit.html">Ulkokasvit</a></li>
                        <li><a href="tyokalut.html">Työkalut</a></li>
                        <li><a href="kasvien_hoito.html">Kasvien hoito</a></li>
                    </ul>
                </li>
                <li><a href="myymalat.html">Myymälät</a></li>
                <li><a href="tietoa_meista.html">Tietoa meistä</a></li>
                <li><a href="ota_yhteytta.html">Ota yhteyttä</a></li>
                <!-- menun loppuun tulee edellä luotu checkbox, jonka kuvake on nyt sulkemiskuvake -->
                <label for="piilonappi" class="menun-piilotusnappi"><i class="fas fa-times"></i></label>
            </ul>
        </nav>    
    </header>

    <main>
        <section>
            <h1>Tervetuloa</h1>
            <p class="peruskappale">
                Puutarhaliike Neilikan kotisivuille!<br>
                Meiltä löydät sekä sisä- että ulkokasvit ja kaiken tarvitsemasi kasvien hoitoon.<br>
                Avarat myymälämme sijaitsevat Helsingissä ja Espoossa.
            </p>
        </section>

        <section>
            <h1>Uutisia</h1>
            <article class="uutinen">
                <p class="pikkuteksti">2.1.2016</p>
                <p>Hyvää uutta vuotta!<br>
                   Uudenvuoden kunniaksi myymälöissämme upeita tarjouksia.
                </p>
            </article>

            <article class="uutinen">
                <p class="pikkuteksti">14.12.2015</p>
                <p>Joulukukat edullisesti meiltä.<br>
                    Myymälöissämme myös kattava ja edullinen valikoima joulukuusia!
                </p>
            </article>

            <article class="uutinen">
                <p class="pikkuteksti">1.9.2015</p>
                <p>Nyt on hyvä aika aloittaa puutarhan valmistelu talven lepokautta varten.
                    <br>Meiltä löydät kaikki <a href="tyokalut.html">työkalut ja tarvikkeet</a>.
                </p>
            </article>
        </section>
    </main>

    <footer class="pikkuteksti">

        <section>Seuraa meitä!<br>
            <a href="#" class="fa fa-facebook"></a>
            <a href="#" class="fa fa-instagram"></a>
        </section>
        <section><address>Puutarhaliike Neilikka Oy<br>
            p. 01 234 5678<br>
            asiakaspalvelu@puutarhaliikeneilikka.fi<br>
            Y-tunnus 12-3456789<br></address></section>        
        <section>Site design<br>© Meeri Rivakka 2022</section>
    </footer>

</body>

</html>