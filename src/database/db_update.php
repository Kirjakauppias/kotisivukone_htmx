<?php
// Päivittää USER-taulun deleted_at-sarakkeen oletusarvoksi NULL ja asettaa NULL kaikille olemassa oleville riveille

declare(strict_types=1);
include_once 'db_connect.php'; // Tietokantayhteys

try {
    // Muutetaan deleted_at oletuksena NULL:ksi
    $alterQuery = "ALTER TABLE `USER` MODIFY `deleted_at` TIMESTAMP NULL DEFAULT NULL";
    $conn->query($alterQuery);
    
    // Asetetaan deleted_at NULL kaikille olemassa oleville riveille
    $updateQuery = "UPDATE `USER` SET `deleted_at` = NULL WHERE `deleted_at` IS NOT NULL";
    $conn->query($updateQuery);
    
    echo "Taulun USER päivittäminen onnistui!";
} catch (mysqli_sql_exception $e) {
    error_log("SQL-virhe: " . $e->getMessage());
    die("Tietokantapäivitys epäonnistui: " . $e->getMessage());
}

$conn->close();
?>
