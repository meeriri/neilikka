<header>
    <?php
        // Yläpalkin ja navin elementtien näkyvyys, jos käyttäjä ei ole kirjautuneena:
        if (!isset($_SESSION['ktunnus'])) {
            echo "<style>.header_logged_out {display:block;}
                    .header_logged_in {display:none;}</style>";
        }
        else { // Näkyvyys toisin päin, jos käyttäjä on kirjautunut:
            echo "<style>.header_logged_out {display:none;}
                    #.header_logged_in {display:block;}</style>";
        }
    ?>

    <div class="ilmoituspalkki pikkuteksti">
        <!-- Jos käyttäjä ei ole kirjautunut, näytetään tervetulotoivotus: -->
        <p class="header_logged_out">Tervetuloa uudistuneelle sivustollemme!</p>
        <!-- Jos käyttäjä on kirjautunut, näytetään käyttäjätunnus sekä uloskirjautumisnappi.
            Nappi ohjaa kirjautumissivulle (joka puolestaan osaa huomioida, 
            jos käyttäjä on tullut sivulle uloskirjautumisen vuoksi). -->
        <form class="header_logged_in" action="<?php echo $polku?>/kayttajahallinta/login.php" method="post">
            <?php echo "Olet kirjautuneena käyttäjänä: ".($_SESSION["ktunnus"] ?? "");?>
            <input type="submit" name="ulos" value="Kirjaudu ulos">
        </form>
    </div>

    <div class="kuva_tekstilla">
        <p class="kuvan_textbox">
            <span class="pikkuteksti">Epidemiatilanne</span><br><br>
            Myymälämme ovat avoinna normaalisti.<br><br>
            ma&#8211;ke 9&#8211;17 <br>la 12&#8211;18
        </p>
    </div>
    
    <a id="etusivulinkki" href="<?php echo $polku?>/index.php">Puutarhaliike Neilikka</a>
   
    <!-- Lisätään piilotettava checkbox, jonka avulla voidaan avata menu.
        Checkbox rastitaan labeliin laitetun hampurilaiskuvakkeen kautta, joka
        puolestaan haetaan luokkien fas ja fa-bars avulla Font Awesomesta. -->
    <input type="checkbox" id="piilonappi"> 
    <label for="piilonappi" class="menun-nayttonappi"><i class="fas fa-bars"></i></label>

    <nav>
        <ul class="paavalikko">
            <li class="pudotusvalikko"><span class="ylaotsikko">Tuotteet</span>
                <ul class="alavalikko">
                    <li><a href="<?php echo $polku?>/tuoteryhmat/sisakasvit.php">Sisäkasvit</a></li>
                    <li><a href="<?php echo $polku?>/tuoteryhmat/ulkokasvit.php">Ulkokasvit</a></li>
                    <li><a href="<?php echo $polku?>/tuoteryhmat/tyokalut.php">Työkalut</a></li>
                    <li><a href="<?php echo $polku?>/tuoteryhmat/kasvien_hoito.php">Kasvien hoito</a></li>
                </ul>
            </li>
            <li><a href="<?php echo $polku?>/myymalat.php">Myymälät</a></li>
            <li><a href="<?php echo $polku?>/tietoa_meista.php">Tietoa meistä</a></li>
            <li class="header_logged_out"><a href="<?php echo $polku?>/kayttajahallinta/login.php">Kirjaudu</a></li>
            <li class="header_logged_in"><a href="<?php echo $polku?>/kayttajahallinta/profiili.php">Profiili</a></li>
            <li><a href="ota_yhteytta.php">Ota yhteyttä</a></li>
            <!-- menun loppuun tulee toinen label/kuvake edellä luodulle checkboxille;
            tällä kertaa kuvake on rasti Font Awesomesta (menun sulkemista varten) -->
            <label for="piilonappi" class="menun-piilotusnappi"><i class="fas fa-times"></i></label>
        </ul>
    </nav>
</header>