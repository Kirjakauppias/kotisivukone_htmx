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
require 'funcs.php';

$loggedIn = isset($_SESSION['user_id']); // Alustetaan muuttuja.

// Varmistetaan, että CSRF-token luodaan ja tallennetaan istunnossa.
// Luodaan tokenille aikaraja.
if (!isset($_SESSION['csrf_token']) || time() - ($_SESSION['csrf_token_time'] ?? 0) > 300) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

$isResponsive = isset($_GET['responsive']) && $_GET['responsive'] == 'true';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Kirjautuminen</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="htmx.js" defer></script>
    <script>
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>
</head>
<body>
    <!-- Banneri -->
    <div class="banner" id="home">
        <div class="banner-text">
            <h1 style="font-size:50px">KOTISIVUKONE</h1>
            <p>Testauskäyttöön ainoastaan</p>
            <!-- Näytetään rekisteri -painike jos käyttäjä ei ole kirjautunu sisään. -->
            <?php if(!$loggedIn): ?>
                <button 
                hx-get="register.php"
                hx-target="#modal-container"
                hx-trigger="click"
                class="btn-register"
                >
                Aloita tästä!
            </button>
          
                <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
                <!-- Nappula, joka avaa modalin -->
                <button 
                    hx-get="login_modal.php" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                    class="btn-login"
                >
                    Kirjaudu
                </button>
            <?php else: ?>
                <div class="logged-in">
                    <p>Tervetuloa, <?php echo $_SESSION['username']; ?>!</p>
                    <!-- Uloskirjautumis -painike -->
                    <button class="btn-logout"><a href="logout.php">Kirjaudu ulos</a></button>
                </div>
            <?php endif; ?>
        </div>
    </div> <!-- /Banneri-->
    <!-- Responsiivinen navigointipalkki -->
    <div class="topnav" id="myTopnav">
        <a href="#home" class="active">Etusivu</a>
        <a href="#contact">Yhteystiedot</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div>
    <div id="modal-container">
        <!-- Modalin kontti -->
    </div>
<main>
    <img src="images/stars_small.png" alt="5 stars" class="img-stars">
    <!-- Esittelyotsikko -->
    <div class="presentation">
        <h1>Luo omat kotisivut itsellesi tai yrityksellesi</h1>
        <h1>Kotisivukoneella rakennat</h1>
        <h1 class="orange">sivut itse helposti</h1>
    </div>
    <!-- Esittelylaatikot -->
    <div class="row">
      <div class="column">
        <img src="images/webicon_small.png">
        <h1>Helppokäyttöinen</h1>
        <p>Luo ammattimaiset kotisivut helposti ilman koodaamista.</p>
      </div>
      <div class="column">
        <img src="images/responsiveicon_small.png">
        <h1>Mobiiliystävällinen</h1>
        <p>Responsiivinen ulkoasu näyttää hyvältä kaikilla laitteilla.</p>
      </div>
      <div class="column">
        <img src="images/safeicon_small.png">
        <h1>Turvallinen</h1>
        <p>Huolehdimme että tietoturva on parasta mahdollista. Jatkuvasti.</p>
      </div>
      <div class="column">
        <img src="images/effecticon_small.png">
        <h1>Kustannustehokas</h1>
        <p>Kotisivukone-palvelulla tehdyt kotisivut ovat kustannustehokas tapa luoda näkyvyyttä ja lisätä yrityksen myyntiä.</p>
      </div>
    </div><!-- /row-->
</main>
<footer id="contact">
    <div class="footer-container">
        <div class="footer-details">
            <h2>Opinnäytetyön muokattu versio</h2> 
            <p>2025 Mikko Lepistö</p>
            <p>metarktis@gmail.com</p>
        </div>
        <div class="footer-contact">
            <a href=""><img src="images/github_small.png"></a>
            <a href=""><img src="images/linked_small.png"></a>
        </div>
    </div>
</footer>
<script>
    function myFunction() {
      var x = document.getElementById("myTopnav");
      if (x.className === "topnav") {
        x.className += " responsive";
      } else {
        x.className = "topnav";
      }
    }
</script>
</body>
</html>