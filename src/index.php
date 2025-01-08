<?php
declare(strict_types=1);
// Lisätään virheiden raportointi kehitysympäristössä
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');
 
//header("Content-Security-Policy: default-src 'self'; style-src 'self';");

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
    <?php if(!$loggedIn): ?>
        <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
        <!-- Nappula, joka avaa modalin -->
        <button 
            hx-get="login_modal.php" 
            hx-target="#modal-container" 
            hx-trigger="click"
        >
            Kirjaudu
        </button>
    <?php else: ?>
        <p>Tervetuloa, <?php echo $_SESSION['username']; ?>!</p>
        <a href="logout.php">Kirjaudu ulos</a>
    <?php endif; ?>
    <button 
        hx-get="register.php"
        hx-target="#modal-container"
        hx-trigger="click"
        class="btn-register"
    >
        Uusi tili
    </button>
    <div id="modal-container">
        <!-- Modalin kontti -->
    </div>
<main>

</main>
<footer>
    <p>@ 2025 Mikko Lepistö - metarktis@gmail.com</p>
</footer>
</body>
</html>