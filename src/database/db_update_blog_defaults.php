<?php
require_once 'db_connect.php'; // Otetaan tietokantayhteys käyttöön

$updates = [
    // Poistetaan vanhat FOREIGN KEY -suhteet
    "ALTER TABLE `BLOG` DROP FOREIGN KEY `BLOG_ibfk_2`;",
    "ALTER TABLE `BLOG` DROP FOREIGN KEY `BLOG_ibfk_3`;",
    
    // Varmistetaan, että layout_id ja style_id sallivat NULL-arvon ja niiden oletusarvo on 1
    "ALTER TABLE `BLOG` MODIFY `layout_id` INT DEFAULT 1;",
    "ALTER TABLE `BLOG` MODIFY `style_id` INT DEFAULT 1;",
    
    // Lisätään takaisin FOREIGN KEY -suhteet (ON DELETE SET NULL)
    "ALTER TABLE `BLOG` ADD CONSTRAINT `BLOG_ibfk_2` FOREIGN KEY (`layout_id`) REFERENCES `LAYOUTS`(`layout_id`) ON DELETE SET NULL;",
    "ALTER TABLE `BLOG` ADD CONSTRAINT `BLOG_ibfk_3` FOREIGN KEY (`style_id`) REFERENCES `STYLES`(`style_id`) ON DELETE SET NULL;",
];

try {
    foreach ($updates as $sql) {
        $conn->query($sql);
    }
    echo "✅ BLOG-taulun layout_id ja style_id päivitetty: oletusarvo = 1, mutta ON DELETE SET NULL toimii.";
} catch (Exception $e) {
    error_log("❌ Virhe päivityksessä: " . $e->getMessage());
    die("❌ Virhe päivityksessä: " . $e->getMessage());
}

$conn->close();
?>