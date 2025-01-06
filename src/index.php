<?php
declare(strict_types=1);
// Lisätään virheiden raportointi kehitysympäristössä
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');
// Lisätään Content-Security-Policy header
header("Content-Security-Policy: default-src 'self'; script-src 'self'");

ini_set('session.cookie_secure', '1'); // Vain HTTPS-yhteyksillä
ini_set('session.cookie_httponly', '1'); // Estää JavaScriptin pääsyn kekseihin
ini_set('session.cookie_samesite', 'Strict'); // Ei lähetä keksejä kolmannen osapuolen pyynnöissä
session_start(); // Aloitetaan sessio

$loggedIn = isset($_SESSION['user_id']); // Alustetaan muuttuja.

// Varmistetaan, että CSRF-token luodaan ja tallennetaan istunnossa.
// Luodaan tokenille aikaraja.
if (!isset($_SESSION['csrf_token']) || time() - ($_SESSION['csrf_token_time'] ?? 0) > 300) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Kirjautuminen</title>
    <link rel="stylesheet" href="./styles/style.css">
    <script src="htmx.js" defer></script>
    <script>
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>
</head>
<body>
    <div id="login_container">
    <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
        <?php if(!$loggedIn): ?>
            <form
                class="login_form" 
                hx-post="login.php"
                hx-target="#login_container" 
                hx-swap="innerHTML"
                autocomplete="off"
            >
                <label for="username">Käyttäjätunnus:</label>
                <input type="text" id="username" name="username" autocomplete="username" required>

                <label for="password">Salasana:</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>

                <!-- Lisätään lomakkeeseen piilotettu kenttä joka lisää CSRF-tokenin -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <input type="submit" value="Kirjaudu">
            </form>
        <?php else: ?>
            <p>Tervetuloa, <?php echo $_SESSION['username']; ?>!</p>
            <a href="logout.php">Kirjaudu ulos</a>
        <?php endif; ?>    
    </div><!-- /login_container -->
    <!-- Footer, joka ei vaikuta kirjautumisprosessiin -->
    <footer>
        <p>@ 2025 Mikko Lepistö</p>
    </footer>
</body>
</html>