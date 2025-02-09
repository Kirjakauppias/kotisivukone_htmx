<?php
require_once "database/db_connect.php";

// Päivitetään olemassa olevat arvot
$conn->query("UPDATE USER SET status = 'ACTIVE' WHERE status = 'active'");
$conn->query("UPDATE USER SET role = 'CUSTOMER' WHERE role = 'customer'");

// Muutetaan sarakkeiden tietotyyppi VARCHAR(50), jotta voidaan asettaa oletusarvot
$conn->query("ALTER TABLE USER CHANGE COLUMN status status VARCHAR(50) NOT NULL DEFAULT 'ACTIVE'");
$conn->query("ALTER TABLE USER CHANGE COLUMN role role VARCHAR(50) NOT NULL DEFAULT 'CUSTOMER'");

echo "✅ Päivitys valmis!";
?>