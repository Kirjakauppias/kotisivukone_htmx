<?php
declare(strict_types=1);
// Lisätään virheiden raportointi kehitysympäristössä
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');
// Lisätään Content-Security-Policy header
// Kaikki JavaScriptit ovat itse kirjoitettuja ja ladattu suoraan HTML-sivulle
// joten voin käyttää 'unsafe-inline' -direktiiviä skriptin sallimimeen. 
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; object-src 'none';");

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

        // JavaScript funktio modaalin avaamiseksi ja sulkemiseksi
        function openModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <?php if(!$loggedIn): ?>
        <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
        <button onclick="openModal()">Kirjaudu</button>
    <?php else: ?>
        <p>Tervetuloa, <?php echo $_SESSION['username']; ?>!</p>
        <a href="logout.php">Kirjaudu ulos</a>
    <?php endif; ?>
    
    <div id="loginModal" style="display:none;">
        <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
            <div id="login_container">
                <form 
                    hx-post="login.php"
                    hx-target="#errors" 
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
                <div id="errors">
                    <!-- Tähän tulostetaan virheilmoitukset -->
                </div>
            </div><!-- /login_container -->
        </div><!-- /modal-content -->
    </div><!-- /loginModal-->
    <!-- Footer, joka ei vaikuta kirjautumisprosessiin -->
    <footer>
        <p>@ 2025 Mikko Lepistö</p>
    </footer>
</body>
</html>