<?php
$host = 'mysql.railway.internal';
$user = 'root';
$pass = 'PNfRKwbpEQGdiVyTgALFlUKTbRtsZgsO';
$dbName = 'railway';
$port = 3306;

// Luo yhteys
$conn = new mysqli($host, $user, $pass, $dbName, $port);

if ($conn->connect_error) {
    die("❌ Yhteys epäonnistui: " . $conn->connect_error);
} else {
    echo "✅ Yhteys MySQL:ään onnistui!";
}
