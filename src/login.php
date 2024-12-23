<?php
session_start();
// Sivu jossa tarkistetaan kirjautuminen ja lähetetään
// virhe-ilmoitukset index.php response -diviin
// Otetaan yhteys tietokantaan
require_once "database/db_enquiry.php";

// Tarkistetaan, onko lomake lähetetty index.php:sta
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Määritellään kirjautumislomakkeessa annetut tiedot
    $username = $_POST['username'];
    $password = $_POST['password'];
}

?>