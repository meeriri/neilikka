<?php
    if (!session_id()) {session_start();}

    if (isset($_GET["ulos"])) { // Jos on klikattu uloskirjautumisnappia:
        $kohdesivu = $_SESSION["lahtosivu"]; // Haetaan sivu, jolla nappia klikattiin
        $_SESSION = array(); // Tuhotaan istuntomuuttujat
        session_destroy();
        header("location: $kohdesivu?logout=1"); // Uudelleenohjaus sivulle, jolla nappia klikattiin
    } else {header("location: login.php?");}
        // Jos logout-sivulle tultiin muuta kautta, uudelleenohjataan login-sivulle
?>