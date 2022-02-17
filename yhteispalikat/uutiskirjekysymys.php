<div class="radionappikysymys">
    <label>Saammeko kertoa sinulle 
    ajankohtaisista tarjouksista uutiskirjeellämme?</label><br>
    <p class="radiosetti">
        <input id="kylla" type="radio" name="uutiskirje" value="kyllä"
            <?php echo naytaRadiovalinta("uutiskirje","kyllä")?>>
        <label for="kylla" class="radio-label">Mielellään!</label>
    </p>
    <p class="radiosetti">    
        <input id="ei" type="radio" name="uutiskirje" value="ei"
            <?php echo naytaRadiovalinta("uutiskirje","ei")?>>
        <label for="ei" class="radio-label">Ei kiitos tällä kertaa.</label><br>
    </p>
    <?php // Jos on yritetty lähettää lomake ilman uutiskirjevalintaa:
        echo huomautaPuuttuvasta("rekisteröidy","uutiskirje");?>
    <label id="peruutus-label">Voit muuttaa valintaasi milloin tahansa.</label>
</div>
