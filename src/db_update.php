<?php
require_once "database/db_connect.php";

$conn->query("UPDATE USER SET status = 'ACTIVE' WHERE status = 'active'");
$conn->query("UPDATE USER SET role = 'CUSTOMER' WHERE role = 'customer'");

$conn->query("ALTER TABLE USER ALTER COLUMN status SET DEFAULT 'ACTIVE'");
$conn->query("ALTER TABLE USER ALTER COLUMN role SET DEFAULT 'CUSTOMER'");

echo "✅ Päivitys valmis!";
?>