<?php
// index.php
session_start(); // Aloitetaan sessio
$loggedIn = isset($_SESSION['user_id']); // Alustetaan muuttuja.
// Varmistetaan, että CSRF-token luodaan ja tallennetaan istunnossa.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirjautuminen</title>
    <link rel="stylesheet" href="./styles/style.css">
    <script src="htmx.js" defer></script>
</head>
<body>
    <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
    <?php if(!$loggedIn): ?>
        <!-- Kirjautumislomake -->
        <!-- hx-post: lomake lähetetään login.php-sivulle 
             hx-target: HTMX:n vastaus liitetään #response-diviin 
             hx-swap:="outerHTNL" koko #response-div päivitetään vastauksella -->
        <form 
            hx-post="login.php"
            hx-target="#response"
            hx-swap="outerHTML"
        >
            <label for="username">Käyttäjätunnus:</label>
            <input type="text" id="username" name="username" autocomplete="off" required>

            <label for="password">Salasana:</label>
            <input type="password" id="password" name="password" autocomplete="off" required>

            <!-- Lisätään lomakkeeseen piilotettu kenttä joka lisää CSRF-tokenin -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input type="submit" value="Kirjaudu">
        </form>
        <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset -->
        </div>
    <?php else: ?>
        <div class="welcome">
            <p>Tervetuloa, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
    <?php endif; ?>
</body>
</html>