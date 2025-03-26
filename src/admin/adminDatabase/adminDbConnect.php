<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja

// kaikki SQL -virheet raportoidaan:
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ladataan tietokantayhteyden tiedot ympäristömuuttujista
$host = $_ENV['DB_HOST'] ?? "";
$dbName = $_ENV['DB_NAME'] ?? "";
$user = $_ENV['DB_USER'] ?? "";
$pass = $_ENV['DB_PASS'] ?? "";
//$port = $_ENV['PORT'] ?? 3306;

// Tarkistetaan, että pakolliset muuttujat ovat määritelty
if (!$host || !$dbName || !$user || !$pass) {
    die('Ympäristömuuttujia puuttuu! Tarkista asetukset (DB_HOST, DB_NAME, DB_USER, DB_PASS)');
}

// Luodaan yhteys tietokantaan
$conn = new mysqli($host, $user, $pass, $dbName);

// Jos yhteys epäonnistuu, lokitetaan virhe ja lopetetaan suoritus
if ($conn->connect_error) {
    error_log("Yhteys epäonnistui: " . $conn->connect_error);
    die("Tietokantayhteys epäonnistui, yritä myöhemmin uudelleen.");
}

// 25.2. Asetetaan merkistökoodaus, estää erikoismerkkiongelmat
$conn->set_charset("utf8mb4");

/*
    db_connect.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
*/
?>