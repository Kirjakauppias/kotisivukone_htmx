<?php
// NÄMÄ TOIMI
/*$host = 'mysql.railway.internal';
$user = 'root';
$pass = 'PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO';
$dbName = 'railway';
$port = 3306;*/

// NÄMÄ TOIMI
/*$host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'mysql.railway.internal';  // Railwayn tietokannan isäntänimi (oletus)
$dbName = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'railway';  // Railwayn tietokannan nimi (oletus)
$user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'root';  // Railwayn käyttäjätunnus (oletus)
$pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? 'PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO';  // Railwayn salasana (oletus)
$port = $_ENV['PORT'] ?? $_SERVER['PORT'] ?? 3306;  // Portti, oletusarvo 3306
*/

$host = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$port = $_ENV['PORT'];

var_dump($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['PORT']);
// Tarkistetaan, että ympäristömuuttujat on asetettu oikein
if (!$host || !$dbName || !$user || !$pass) {
    die('Ympäristömuuttujia puuttuu! Tarkista ympäristön asetukset.');
}


// Luo yhteys
$conn = new mysqli($host, $user, $pass, $dbName, $port);

if ($conn->connect_error) {
    error_log("Yhteys epäonnistui: " . $conn->connect_error);  // Lokitetaan virhe
    die("❌ Yhteys epäonnistui: " . $conn->connect_error);
} else {
    echo "✅ Yhteys MySQL:ään onnistui!";
}

// Funktio joka hakee käyttäjän tiedot usernamella
function getUserByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id, username, firstname, password, status FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
