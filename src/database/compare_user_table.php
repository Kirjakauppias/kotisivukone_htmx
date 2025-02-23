<?php
require_once 'db_connect.php';

// Hae USER-taulun saraketiedot
$query = "
    SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'USER' AND TABLE_SCHEMA = DATABASE();
";

$result = $conn->query($query);

if (!$result) {
    die("Kyselyn suoritus epäonnistui: " . $conn->error);
}

// Tulostetaan taulun rakenne selkeässä muodossa
echo "USER-taulun rakenne Railway.com-tietokannassa:\n";
echo str_repeat("=", 50) . "\n";
printf("%-20s %-20s %-10s %-15s %s\n", "Sarakkeen nimi", "Tietotyyppi", "NULL", "Oletusarvo", "Lisätiedot");
echo str_repeat("-", 50) . "\n";

while ($row = $result->fetch_assoc()) {
    printf(
        "%-20s %-20s %-10s %-15s %s\n",
        $row['COLUMN_NAME'],
        $row['COLUMN_TYPE'],
        $row['IS_NULLABLE'],
        $row['COLUMN_DEFAULT'] ?? 'NULL',
        $row['EXTRA']
    );
}

$conn->close();
?>