<?php
require_once "./database/db_connect.php"; // Yhdistetään tietokantaan

try {
    // SQL-kysely: Lisätään image_path-sarake, jos sitä ei ole
    $sql = "ALTER TABLE ARTICLE ADD COLUMN deleted_at IS NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Tietokannan päivitys onnistui: 'deleted_at' lisätty.";
    } else {
        echo "Virhe päivitettäessä tietokantaa: " . $conn->error;
    }
} catch (Exception $e) {
    echo "Virhe: " . $e->getMessage();
}

$conn->close();
?>