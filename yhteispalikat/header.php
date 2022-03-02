<header>
    <?php
        if (!session_id()) {session_start();}
        // Nykyinen sivu talteen mahdollista uloskirjautumista varten:
        $_SESSION["lahtosivu"] = htmlspecialchars($_SERVER["PHP_SELF"]);
        
        // Yläpalkin ja navin elementtien näkyvyys, jos käyttäjä on kirjautunut:
        if (isset($_SESSION["loggedin"]) and $_SESSION["loggedin"]) {
            echo "<style>.header_logged_out {display:none;}
                    .header_logged_in {display:flex;}</style>";
        }
        else { // Näkyvyys toisin päin, jos käyttäjä ei ole kirjautuneena:
            echo "<style>.header_logged_out {display:flex;}
                    .header_logged_in {display:none;}</style>";
        }
    ?>

    <div class="ilmoituspalkki pikkuteksti header_logged_out">
        <!-- Jos käyttäjä ei ole kirjautunut, näytetään tervetulotoivotus: -->
        <p>Tervetuloa uudistuneelle sivustollemme!</p>
    </div>
    <div class="ilmoituspalkki pikkuteksti header_logged_in">
        <!-- Jos käyttäjä on kirjautunut, näytetään käyttäjätunnus sekä uloskirjautumisnappi: -->
        <p><?php echo "Olet kirjautuneena käyttäjänä: ".($_SESSION["sposti"] ?? "");?></p>
        <form action="<?php echo $polku?>/rutiinit/logout.php" method="get">
            <input type="submit" id="logout_nappi" name="ulos" value="Kirjaudu ulos">
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
            <li class="header_logged_in"><a href="<?php echo $polku?>/kayttajahallinta/omasivu.php">Oma sivusi</a></li>
            <li><a href="<?php echo $polku?>/ota_yhteytta.php">Ota yhteyttä</a></li>
            <!-- menun loppuun tulee toinen label/kuvake edellä luodulle checkboxille;
            tällä kertaa kuvake on rasti Font Awesomesta (menun sulkemista varten) -->
            <label for="piilonappi" class="menun-piilotusnappi"><i class="fas fa-times"></i></label>
        </ul>
    </nav>
</header>