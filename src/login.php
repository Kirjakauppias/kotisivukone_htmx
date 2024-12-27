<?php
session_start();
// Sivu jossa tarkistetaan kirjautuminen ja lähetetään.
// virhe-ilmoitukset index.php response -diviin.
// Otetaan yhteys tietokantaan.
require_once "database/db_enquiry.php";

// Tarkistetaan, onko lomake lähetetty index.php:sta.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Alustetaan muuttujat ja tarkistetaan CSRF-token.
    $csrf_token = $_POST['csrf_token'] ?? '';
    if ($csrf_token !== $_SESSION['csrf_token']) {
        header('HTTP/1.1 403 Forbidden');
        echo "<div id='response'><p class='error'>Virheellinen CSRF-token.</div>";
        exit();
    }

    // Suodatetaan käyttäjän syötteet.
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)) {
        echo "<div id='response'><p class='error'>Täytä kaikki kentät.</p></div>";
        exit();
    }
} else {
    // Jos ei ole POST-pyyntöä, palautetaan virheilmoitus.
    header('HTTP/1.1 405 Method Not Allowed');
    echo "<div id='response'><p class='error'>Vain POST-pyynnöt sallittu.</div>";
    exit();
}

?>