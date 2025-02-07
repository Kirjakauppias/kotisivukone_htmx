<?php
// db_connect.php
// kaikki SQL -virheet raportoidaan:
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Valitaan tietokanta paikallisesti tai Railwaysta
$host = $_ENV['DB_HOST'] ?? "";
$dbName = $_ENV['DB_NAME'] ?? "";
$user = $_ENV['DB_USER'] ?? "";
$pass = $_ENV['DB_PASS'] ?? "";
//$port = $_ENV['PORT'] ?? 3306;

// Ympäristömuuttujien tarkastus:
//var_dump($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['PORT']);


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