<?php
require_once "database/db_connect.php";

// Päivitetään kaikki NULL-arvot nykyiseen aikaleimaan
$conn->query("UPDATE USER SET created_at = NOW() WHERE created_at IS NULL");

// Muutetaan sarake niin, että oletusarvona on CURRENT_TIMESTAMP
$conn->query("ALTER TABLE USER MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");

echo "✅ Päivitys valmis!";
?>