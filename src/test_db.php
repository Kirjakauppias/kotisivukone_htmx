<?php
// NÄMÄ TOIMI
/*$host = 'mysql.railway.internal';
$user = 'root';
$pass = 'PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO';
$dbName = 'railway';
$port = 3306;*/

$host = $_ENV['DB_HOST'] ?? 'mysql.railway.internal';  // Railwayn tietokannan isäntänimi (oletus)
$dbName = $_ENV['DB_NAME'] ?? 'railway';  // Railwayn tietokannan nimi (oletus)
$user = $_ENV['DB_USER'] ?? 'root';  // Railwayn käyttäjätunnus (oletus)
$pass = $_ENV['DB_PASS'] ?? 'PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO';  // Railwayn salasana (oletus)
$port = $_ENV['PORT'] ?? 3306;  // Portti, oletusarvo 3306

// Tarkistetaan, että ympäristömuuttujat on asetettu oikein
if (!$host || !$dbName || !$user || !$pass) {
    die('Ympäristömuuttujia puuttuu! Tarkista ympäristön asetukset.');
}

// Luo yhteys
$conn = new mysqli($host, $user, $pass, $dbName, $port);

if ($conn->connect_error) {
    die("❌ Yhteys epäonnistui: " . $conn->connect_error);
} else {
    echo "✅ Yhteys MySQL:ään onnistui!";
}
