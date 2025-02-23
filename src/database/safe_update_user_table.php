<?php
require 'db_connect.php'; // Yhdistetään Railway.com tietokantaan

try {
    // 🔹 1. OTETAAN VARMUUSKOPIO USER-TAULUSTA
    $backupTable = "CREATE TABLE USER_BACKUP AS SELECT * FROM USER";
    $conn->query($backupTable);
    echo "Varmuuskopio luotu USER_BACKUP-tauluun.<br>";

    // 🔹 2. MUOKATAAN TAULUN RAKENNETTA
    $queries = [
        // Muutetaan user_id int-muotoon ilman unsignedia
        "ALTER TABLE USER MODIFY user_id INT NOT NULL AUTO_INCREMENT",

        // Muutetaan tekstikentät varchar-muotoon ja pakolliseksi
        "ALTER TABLE USER MODIFY firstname VARCHAR(255) NOT NULL",
        "ALTER TABLE USER MODIFY lastname VARCHAR(255) NOT NULL",
        "ALTER TABLE USER MODIFY username VARCHAR(255) NOT NULL",
        "ALTER TABLE USER MODIFY email VARCHAR(255) NOT NULL",
        "ALTER TABLE USER MODIFY password VARCHAR(255) NOT NULL",

        // Muutetaan status ja role enum-tyyppiseksi
        "ALTER TABLE USER MODIFY status ENUM('ACTIVE','INACTIVE','BANNED') NOT NULL DEFAULT 'ACTIVE'",
        "ALTER TABLE USER MODIFY role ENUM('CUSTOMER','ADMIN') NOT NULL DEFAULT 'CUSTOMER'",

        // Muutetaan last_login datetime-muotoon
        "ALTER TABLE USER MODIFY last_login DATETIME DEFAULT NULL",

        // Muutetaan created_at ja updated_at datetime-muotoon
        "ALTER TABLE USER MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE USER MODIFY updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];

    // Suoritetaan kaikki SQL-komennot
    foreach ($queries as $query) {
        $conn->query($query);
    }

    echo "USER-taulu päivitetty onnistuneesti!<br>";

    // 🔹 3. TARKISTETAAN, ETTÄ KAIKKI KÄYTTÄJÄTIEDOT SÄILYIVÄT
    $originalCount = $conn->query("SELECT COUNT(*) AS count FROM USER_BACKUP")->fetch_assoc()['count'];
    $newCount = $conn->query("SELECT COUNT(*) AS count FROM USER")->fetch_assoc()['count'];

    if ($originalCount === $newCount) {
        echo "Kaikki käyttäjätiedot säilyivät! ($newCount käyttäjää tallessa).<br>";
    } else {
        echo "⚠️ VAROITUS: Käyttäjien määrä ei täsmää! ($originalCount → $newCount). Tarkista tietokanta!<br>";
    }

} catch (Exception $e) {
    echo "⚠️ Virhe päivityksessä: " . $e->getMessage();
}

$conn->close();
?>