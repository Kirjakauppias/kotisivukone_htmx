<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja

// Lisätään virheiden raportointi kehitysympäristössä
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Asetetaan HTTP-otsikot tietoturvan parantamiseksi
// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');
//header("Content-Security-Policy: default-src 'self'; style-src 'self';"); // CSP:n käyttöönotto voi parantaa tietoturvaa

// Konfiguroidaan istunnon tietoturva-asetukset
ini_set('session.cookie_secure', '1'); // Evästeitä lähetetään vain HTTPS-yhteyksillä
ini_set('session.cookie_httponly', '1'); // Estää JavaScriptin pääsyn istuntoevästeisiin
ini_set('session.cookie_samesite', 'Strict'); // Ei lähetä keksejä kolmannen osapuolen pyynnöissä

session_start(); // Käynnistetään istunto 
require 'funcs.php'; // Apufunktioiden lataaminen
require_once './database/db_enquiry.php'; // Tietokantakyselyt

// Tarkistetaan, onko käyttäjä kirjautunut sisään
$loggedIn = isset($_SESSION['user_id']); 

// Varmistetaan, että CSRF-token luodaan ja tallennetaan istunnossa.
// Luodaan tokenille aikaraja.
if (!isset($_SESSION['csrf_token']) || time() - ($_SESSION['csrf_token_time'] ?? 0) > 300) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Luodaan satunnainen token
    $_SESSION['csrf_token_time'] = time(); // Aikaleima tokenin vanhentumisen seurantaan
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>Tarinan paikka</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="htmx.js" defer></script>
    <script>
        // Lisätään CSFR-token kaikkiin HTMX:n tekemisiin pyyntöihin
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>
</head>
<body>
    <!-- Banneri -->
    <div class="banner" id="home">
        <div class="banner-text">
            <h1 style="font-size:50px">TARINAN PAIKKA</h1>
            <p>Showcase Demo - Beta 24.2.25</p>
            <!-- Näytetään rekisteri -painike jos käyttäjä ei ole kirjautunu sisään. -->
            <!-- Näytä kirjautumislomake vain, jos käyttäjä ei ole kirjautunut -->
            <?php if(!$loggedIn): ?>
                <!-- Nappula, joka avaa modalin ja hakee register.php -tiedoston sisällön #modal-containeriin -->
                <button 
                    hx-get="modals/user-register-modal.php"
                    hx-target="#modal-container"
                    hx-trigger="click"
                    class="btn-register"
                >
                    Aloita tästä!
                </button>
                <!-- Nappula, joka avaa modalin ja hakee login_modal.php -tiedoston sisällön #modal-containeriin -->
                <button 
                    hx-get="modals/login-modal.php" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                    class="btn-login"
                >
                    Oma Blogi
                </button>
            <?php else: ?>
                <!-- Näytetään jos käyttäjä on kirjautunut sisään -->
                <div class="logged-in">
                    <!-- Uloskirjautumis -painike josta ohjataan logout.php -tiedostoon ja sitä kautta takaisin index.php -tiedostoon -->
                    <button class="btn-logout"><a href="logout.php">Kirjaudu ulos</a></button>
                </div>
            <?php endif; ?>
        </div>
    </div> <!-- /Banneri-->

    <!-- Responsiivinen navigointipalkki -->
    <div class="topnav" id="myTopnav">
        <a href="#home" class="active">Etusivu</a>
        <!-- Jos käyttäjä ei ole kirjautunut sisään, näytetään esittelysivun navigaatio -->
        <?php if(!$loggedIn): ?>
            <a href="#contact">Yhteystiedot</a>
        <?php endif; ?>
        <!-- 23.1.25: Jos käyttäjä on kirjautunut sisään, näytetään hallinta-navigaatio -->
        <?php if($loggedIn): ?>

            <!-- 23.2. Tarkistetaan että onko käyttäjällä jo blogi. Jos blogi löytyy, piilotetaan "Luo blogi!" -->
            <!-- 23.2. Haetaan muuttuja true / false arvo jolla määritellään, onko käyttäjällä ADMIN -arvo -->
            <?php $isAdmin = checkIfAdmin($conn, $_SESSION['user_id']); ?>
            <!-- 23.2. Jos käyttäjä on ADMIN, pääsee hän luomaan blogin (poistetaan myöhemmin) -->
            <?php if($isAdmin) :?>
                <!-- 23.3. Tarkistetaan, onko käyttäjällä jo blogi -->
                <?php $blogExists = checkBlogExists($conn, $_SESSION['user_id']); ?>
                <!-- 23.2. Jos käyttäjällä ei ole vielä blogia (FALSE) näytetään blogin luonti -linkki -->
                <?php if(!$blogExists) :?>
                    <!-- 23.2. linkki joka avaa modalin jonne tulostetaan modals/create-blog-modal.php -tiedoston sisältö -->
                    <a href="" alt="omat tiedot"
                        hx-get="modals/create-blog-modal.php" 
                        hx-target="#modal-container" 
                        hx-trigger="click"
                    >
                        Luo blogi!
                    </a>
                <?php endif; ?>
                <!-- 23.2. Jos käyttäjällä on jo blogi (TRUE), näytetään linkit jossa voi luoda uuden postauksen ja linkki omalle blogi-sivulle -->
                <?php if($blogExists) :?>
                    <!-- 23.2. Linkki joka avaa modalin jonne tulostetaan modals/create-article-modal.php -tiedoston sisältö -->
                    <a href="" alt="omat tiedot"
                        hx-get="modals/create-article-modal.php" 
                        hx-target="#modal-container" 
                        hx-trigger="click"
                    >
                        Uusi artikkeli
                    </a>
                    <?php
                    // 23.2 Haetaan käyttäjän blogin nimestä luotu slug ja määritellään muuttuja
                    $slug = getSlug($conn, $_SESSION['user_id']);
                    // 23.2. Tulostetaan linkki jossa on osoitteena käyttäjän blogin slug
                    echo "<a href='blogit/$slug' target='_blank'>Blogisivusi</a>"; ?>
                <?php endif; ?>
            <?php endif; ?>
            <!-- Linkki, joka avaa modalin ja hakee user_edit_modal.php -tiedoston sisällön #modal-containeriin -->
            <a href="" alt="omat tiedot"
                hx-get="modals/user-edit-modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Omat tiedot
            </a>
            <!-- Linkki, joka avaa modalin ja hakee modals/password_modal.php -tiedoston sisällön #modal-containeriin -->
            <a href="" alt="omat tiedot"
                hx-get="modals/password_modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Salasanan vaihto
            </a>
        <?php endif; ?>
        <!-- 
            Tämä on responsiivisen navigaatiopalkin painike, 
            joka näkyy mobiililaitteilla. Kun käyttäjä klikkaa 
            painiketta, kutsutaan JavaScript-funktiota `myFunction()`, 
            joka vaihtaa navigaatiopalkin näkyvyyttä.
        -->
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i> <!-- Font Awesome -ikonina hampurilaisvalikko -->
        </a>
    </div>

    <div id="modal-container">
        <!-- Modalin kontti, tänne tulostuu kaikki ikkunat joita käyttäjä käyttää -->
    </div>
<main>
    <!-- Jos käyttäjä ei ole kirjautunut sisään, näytetään esittelysivu -->
    <?php if(!$loggedIn): ?>
        <img src="images/stars_small.png" alt="5 stars" class="img-stars">
        <!-- Esittelyotsikko -->
        <div class="presentation">
            <p>Luo ilmainen blogi!</p>
            <p>Täällä voit kertoa tarinasi </p>
            <p class="orange">tai ideasi helposti.</p>
        </div>
        <!-- Esittelylaatikot -->
        <div class="row">
          <div class="column">
            <img src="images/webicon_small.png">
            <h1>Helppokäyttöinen</h1>
            <p>Rekisteröidy, luo blogi ja ala kirjoittamaan. Kaikki ilman koodaamista.</p>
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
            <h1>Oman näköinen blogi</h1>
            <p>Voit muokata blogin ulkoasua mieleiseksi (tulossa myöhemmin).</p>
          </div>
        </div><!-- /row-->
    <?php else:
        // Jos käyttäjä on kirjautunut sisään, näytetään hänen oma henkilökohtainen toimintosivu 
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
        // Haetaan navigaatiopalkki sen ID:n perusteella
        var x = document.getElementById("myTopnav");

        // Tarkistetaan, onko navigaatiopalkin nykyinen luokka "topnav"
        if (x.className === "topnav") {
            // Jos on, lisätään "responsive"-luokka, joka näyttää valikon
            x.className += " responsive";
        } else {
            // Jos "responsive" -luokka on jo lisätty, poistetaan se
            x.className = "topnav";
        }
    }
</script>
</body>
</html>