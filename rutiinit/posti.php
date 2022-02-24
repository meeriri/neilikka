<?php
    // Haetaan tunnukset root-kansion yläkansiosta:
    if (file_exists(dirname(__DIR__,2)."/tunnukset.php")) {
        include_once(dirname(__DIR__,2)."/tunnukset.php");
    } else { // Jos ei onnistu, keskeytä:
        echo "<p class='lomakevirhe'>Neilikan sähköpostijärjestelmään ei juuri nyt saada yhteyttä. 
            Yritä myöhemmin uudelleen.</p>";
        exit;
    }
    // Haetaan PHPMailerin luokat:
    if (file_exists(dirname(__DIR__,1)."/PHPMailer/Exception.php"))
        {include(dirname(__DIR__,1)."/PHPMailer/Exception.php");}
    if (file_exists(dirname(__DIR__,1)."/PHPMailer/PHPMailer.php"))
        {include(dirname(__DIR__,1)."/PHPMailer/PHPMailer.php");}
    if (file_exists(dirname(__DIR__,1)."/PHPMailer/SMTP.php"))
        {include(dirname(__DIR__,1)."/PHPMailer/SMTP.php");}

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    posti("memma82@hotmail.com","Katos vaan!","Testiviesti Neilikasta");

    function posti($emailTo, $viesti, $otsikko) {
        $emailFrom = "meerin.testitili@gmail.com";
        $emailFromName = "Meerin Testitili";
        $emailToName = "";

        $mail = new PHPMailer;
        $mail->isSMTP(); 
        $mail->SMTPKeepAlive = true; // Jottei Gmail blokkaa, kun palvelin on jatkuvasti sinne yhteydessä?
        $mail->Mailer = "smtp"; // Tarvitaanko? Opettajan joku lisäys liittyen edelliseen riviin?
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 3; // 0 = off (kun tuotannossa/julkaistu), 1: client messages, 2: +server messages, 3: +connection messages 
        $mail->Debugoutput = "html";

        $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); if your network doesn't support SMTP over IPv6
        $mail->Port = 587; // TLS only
        $mail->SMTPSecure = 'tls'; // ssl is depracated
        $mail->SMTPAuth = true;
        $mail->Username = SP_TILI;
        $mail->Password = SP_SALASANA; // Jos Gmail-osoitteellasi on 2-vaiheinen vahvistus päällä, aseta "App Password" ja käytä tätä salasanaa
        /* Gmailin asetukset: mahdollisesti "vähemmän turvallisten sovellusten (less secure apps)
        blokkaaminen täytyy ottaa pois päältä: https://www.google.com/settings/u/1/security/lesssecureapps
        ( ohje: https://support.google.com/accounts/answer/6010255#zippy=%2Cjos-v%C3%A4hemm%C3%A4n-turvallisten-sovellusten-k%C3%A4ytt%C3%B6oikeus-valinta-on-k%C3%A4yt%C3%B6ss%C3%A4-tilill%C3%A4si )
        + ehkä myös clear Captcha:  https://accounts.google.com/b/0/DisplayUnlockCaptcha */
        
        $mail->setFrom($emailFrom, $emailFromName);
        // $mail->addReplyTo($replyTo, $replyToName);
        $mail->addAddress($emailTo, $emailToName);
        $mail->Subject = $otsikko;
        $mail->msgHTML($viesti); // $mail->msgHTML(file_get_contents('contents.html'), __DIR__); reads HTML msg body from an external file, converts referenced images to embedded, converts HTML into a basic plain-text alternative body
        // $mail->AltBody = "teksti ilman html:ää"; // tämän rivin on oltava msgHTML-rivin jälkeen, jos on käytetty edellisen rivin kommentissa olevaa tiedostohakua
        // $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
        
        // Gmailiin ehdotettuja asetuksia Erikiltä/Stackoverflow'sta; vaarallisia jos jäävät voimaan, kun julkaistaan!
        /*$mail->SMTPOptions = array("tls"=>array(
            "verify_peer"=>false;
            "verify_peer_name"=>false;
            "allow_self_signed"=>true;)); */

        if(!@$mail->send()) { // Yritetään lähetää
            $tulos = false;
            //if (defined("DEBUG") and DEBUG) {
                echo "Virhe: ".$mail->ErrorInfo;
            //}
        } else {$tulos = true; echo "Onnistui!";}

        $mail->ClearAddresses(); // Koska on valittu SMTPKeepAlive, tyhjennetään edelliset vastaanottajat
        // $mail->ClearAttachments(); // Kuten yllä, mutta liitteille, jos niitä on
        $mail->SmtpClose(); // Suljetaan yhteys, koska edellä on valittu SMTPKeepAlive
        return $tulos;
    }

    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}


//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}
?>