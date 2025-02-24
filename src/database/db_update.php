<?php
require_once 'db_connect.php'; // Otetaan tietokantayhteys käyttöön

$updates = [
    "ALTER TABLE `LAYOUTS` MODIFY `layout_id` INT NOT NULL AUTO_INCREMENT;",
    "ALTER TABLE `STYLES` MODIFY `style_id` INT NOT NULL AUTO_INCREMENT;"
];

try {
    foreach ($updates as $sql) {
        $conn->query($sql);
    }
    echo "✅ LAYOUTS ja STYLES -taulut päivitetty onnistuneesti!";
} catch (Exception $e) {
    error_log("❌ Virhe taulujen päivityksessä: " . $e->getMessage());
    die("❌ Virhe taulujen päivityksessä: " . $e->getMessage());
}

$conn->close();
?>
