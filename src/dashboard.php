<?php
session_start();

// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Tervetuloa, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <a href="logout.php">Kirjaudu ulos</a>
</body>
</html>