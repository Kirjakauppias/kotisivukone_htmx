<?php
require_once "database/db_connect.php";

// Päivitetään vanhat arvot, jotta niissä ei ole ongelmallisia tietoja
$conn->query("UPDATE USER SET status = 'ACTIVE' WHERE status IS NULL OR status = 'active'");
$conn->query("UPDATE USER SET role = 'CUSTOMER' WHERE role IS NULL OR role = 'customer'");

// Muutetaan sarakkeiden tietotyyppi VARCHAR(50), jotta voidaan asettaa oletusarvot
$conn->query("ALTER TABLE USER MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'ACTIVE'");
$conn->query("ALTER TABLE USER MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'CUSTOMER'");

echo "✅ Päivitys valmis!";
?>