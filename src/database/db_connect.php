<?php
// db_connect.php
// kaikki SQL -virheet raportoidaan:
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ympäristömuuttujat tulevat suoraan Dockerista
$host = $_ENV['DB_HOST'] ?? "mysql--ktz.railway.internal";
$dbName = $_ENV['DB_NAME'] ?? "railway";
$user = $_ENV['DB_USER'] ?? "root";
$pass = $_ENV['DB_PASS'] ?? "PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO";

// Tarkistetaan, että pakolliset muuttujat ovat määritelty
if (!$host || !$dbName || !$user || !$pass) {
    die('Ympäristömuuttujia puuttuu! Tarkista Docker Compose -määritykset.');
}

// Luodaan yhteys tietokantaan
$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    error_log("Yhteys epäonnistui: " . $conn->connect_error);
    die("Yhteys epäonnistui: " . $conn->connect_error);
}
?>