<?php
declare(strict_types=1);
// Lisätään virheiden raportointi kehitysympäristössä
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Asetetaan HTTP-otsikot tietoturvan parantamiseksi
// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');
//header("Content-Security-Policy: default-src 'self'; style-src 'self';");

// Konfiguroidaan istunnon tietoturva-asetukset
ini_set('session.cookie_secure', '1'); // Vain HTTPS-yhteyksillä
ini_set('session.cookie_httponly', '1'); // Estää JavaScriptin pääsyn kekseihin
ini_set('session.cookie_samesite', 'Strict'); // Ei lähetä keksejä kolmannen osapuolen pyynnöissä

// Käynnistetään sessio
session_start(); 
require 'funcs.php';
require_once './database/db_enquiry.php';

// Alustetaan muuttuja.
$loggedIn = isset($_SESSION['user_id']); 

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
    <title>ML-Blogi</title>
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
            <h1 style="font-size:50px">ML-Blogi</h1>
            <p>Showcase Demo - Beta 16.2.25</p>
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
                <!-- Näytetään jos käyttäjä on kirjautunut sisään -->
                <div class="logged-in">
                    <!-- Uloskirjautumis -painike -->
                    <button class="btn-logout"><a href="logout.php">Kirjaudu ulos</a></button>
                </div>
            <?php endif; ?>
        </div>
    </div> <!-- /Banneri-->

    <!-- Responsiivinen navigointipalkki -->
    <div class="topnav" id="myTopnav">
        <a href="#home" class="active">Etusivu</a>
        <?php if(!$loggedIn): ?>
            <a href="#contact">Yhteystiedot</a>
        <?php endif; ?>
        <!-- 23.1.25: Jos käyttäjä on kirjautunut sisään, näytetään hallinta-navigaatio -->
        <?php if($loggedIn): ?>

            <!-- 23.2. Tarkistetaan että onko käyttäjällä jo blogi. Jos blogi löytyy, piilotetaan "Luo blogi!" -->
            <?php $isAdmin = checkIfAdmin($conn, $_SESSION['user_id']); ?>
            <?php if($isAdmin) :?>
            <?php $blogExists = checkBlogExists($conn, $_SESSION['user_id']); ?>
            <?php if(!$blogExists) :?>
            <a href="" alt="omat tiedot"
                hx-get="modals/create-blog-modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Luo blogi!
            </a>
            <?php endif; ?>
            <?php if($blogExists) :?>
            <a href="" alt="omat tiedot"
                hx-get="modals/create-article-modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Uusi artikkeli
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <a href="" alt="omat tiedot"
                hx-get="user_edit_modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Omat tiedot
            </a>
            <a href="" alt="omat tiedot"
                hx-get="modals/password_modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Salasanan vaihto
            </a>
        <?php endif; ?>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div>
    <div id="modal-container">
        <!-- Modalin kontti -->
    </div>
<main>
    <!-- Jos käyttäjä ei ole kirjautunut sisään, näytetään esittelysivu -->
    <?php if(!$loggedIn): ?>
        <img src="images/stars_small.png" alt="5 stars" class="img-stars">
        <!-- Esittelyotsikko -->
        <div class="presentation">
            <p>Luo oma blogi itsellesi!</p>
            <p>Täällä voit rakentaa</p>
            <p class="orange">blogin itse helposti</p>
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
    <?php else: 
        include_once "./dashboard.php";
        endif;
    ?>
</main>
<footer id="contact">
    <div class="footer-container">
        <div class="footer-details">
            <h2>Tietoa</h2> 
            <a href="tietoturvaseloste.html" alt="Tietoturvaseloste">Tietoturvaseloste</a>
            <p>2025 Mikko Lepistö</p>
            <p>metarktis@gmail.com</p>
        </div>
        <div class="footer-contact">
            <a href="https://github.com/Kirjakauppias/kotisivukone_htmx" alt="github" target="_blank"> <img src="images/github_small.png" alt="Github-Icon"></a>
            <a href="https://www.linkedin.com/in/mikko-lepistö-38762966" alt="LinkedIn" target="_blank"><img src="images/linked_small.png" alt="LinkedIn-icon"></a>
        </div>
    </div>
</footer>
<script>
    // Funktio navigoinnin responsiivisuuteen
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