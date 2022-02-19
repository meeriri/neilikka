<?php
    // funktio, jonka avulla lomakkeen kentässä saadaan näkymään jo syötetty arvo:
    function naytaSyotetty($kentta) {
        return $_POST[$kentta] ? "value='".$_POST[$kentta]."'" : "";
    }

    // funktio, jonka avulla pudotusvalikossa saadaan näkymään jo tehdyt valinnat:
    function naytaValittu($valikko, $valinta) {
        if (isset($_POST[$valikko]) and $_POST[$valikko] == $valinta)
            {return "selected='selected'";}
    }

    // funktio, jonka avulla checkboxissa saadaan näkymään jo laitettu rasti:
    function naytaRasti($boksisetti, $boksin_value) {
        if (isset($_POST[$boksisetti])) {
            // Jos setissä on monta checkboxia, boksisetti on taulukko:
            if (is_array($_POST[$boksisetti]) and in_array($boksin_value,$_POST[$boksisetti]))
                {return "checked='checked'";}
            else // Jos setissä on vain yksi checkbox, ei tarvitse tutkia enempää: 
                {return "checked='checked'";}
        }
    }

    // funktio, joka avulla radionapissa saadaan näkymään jo laitettu valinta:
    function naytaRadiovalinta($nappisetti, $napin_value) {
        if (isset($_POST[$nappisetti]) and $_POST[$nappisetti] == $napin_value)
            {return "checked='checked'";}
    }

    // funktio, jonka avulla tulostetaan huomautus pakolliseen kenttään, josta puuttuu arvo:
    function huomautaPuuttuvasta($submitin_nimi, $kentta) {
        if (isset($_POST[$submitin_nimi]) and empty($_POST[$kentta])) {
            if ($kentta == "uutiskirje") {
                return "<p class='lomakevirhe levennettava'>Valitse jompikumpi.</p>";
            }
            return "<p class='lomakevirhe'>Täytä tämä kenttä.</p>";
        }
    }
?>