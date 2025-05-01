<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja

// Asetetaan HTTP-otsikot tietoturvan parantamiseksi
// Lisätään X-Frame-Options header estämään clickjacking
header('X-Frame-Options: DENY');

// Konfiguroidaan istunnon tietoturva-asetukset
ini_set('session.cookie_secure', '1'); // Evästeitä lähetetään vain HTTPS-yhteyksillä
ini_set('session.cookie_httponly', '1'); // Estää JavaScriptin pääsyn istuntoevästeisiin
ini_set('session.cookie_samesite', 'Strict'); // Ei lähetä keksejä kolmannen osapuolen pyynnöissä

require_once 'config.php'; // Virheiden käsittely

session_start(); // Käynnistetään istunto 

require 'funcs.php'; // Apufunktioiden lataaminen
require_once './database/db_enquiry.php'; // Tietokantakyselyt

// Laitetaan muuttujaan tieto siitä että onko käyttäjä kirjautunut sisään.
$loggedIn = loggedIn($conn);

// Tarkistetaan, onko sessiossa jo validi "modal_key"
if (empty($_SESSION['modal_key'])) {
    // Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon
    $_SESSION['modal_key'] = bin2hex(random_bytes(32)); // Luodaan satunnainen 64-merkkinen avain
}

// Alkuun haetaan 6 uusinta blogia
$stmt = $conn->prepare("SELECT blog_id, name, slug, description FROM BLOG WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$result = $stmt->get_result();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--meta name="csrf-token" content="<?//php echo $_SESSION['csrf_token']; ?>">-->
    <title>Tarinan paikka</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="htmx.js" defer></script>
</head>
<body>
    <!-- Banneri -->
    <!--TESTI-->
    <div class="banner" id="home">
        <div class="banner-text">
            <h1 style="font-size:50px">TARINAN PAIKKA</h1>
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
        <a href="#home" class="active"><img src="images/tp.png" alt="logo"></a>
        <!-- Jos käyttäjä ei ole kirjautunut sisään, näytetään esittelysivun navigaatio -->
        <?php if(!$loggedIn): ?>
            <a href="#contact">Yhteystiedot</a>
        <?php endif; ?>
        <!-- 23.1.25: Jos käyttäjä on kirjautunut sisään, näytetään hallinta-navigaatio -->
        <?php if($loggedIn): ?>
            <!-- 23.2. Tarkistetaan että onko käyttäjällä jo blogi. Jos blogi löytyy, piilotetaan "Luo blogi!" -->
            <!-- 23.3. Tarkistetaan, onko käyttäjällä jo blogi -->
            <?php $blogExists = checkBlogExists($conn, $_SESSION['user_id']); ?>
            <!-- 23.2. Jos käyttäjällä ei ole vielä blogia (FALSE) näytetään blogin luonti -linkki -->
            <?php if(!$blogExists) :?>
                <!-- 23.2. linkki joka avaa modalin jonne tulostetaan modals/create-blog-modal.php -tiedoston sisältö -->
                <a href="" alt="Luo blogi"
                    hx-get="modals/create-blog-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                >
                    Luo blogi!
                </a>
            <?php endif; ?>
            <!-- 23.2. Jos käyttäjällä on jo blogi (TRUE), näytetään linkit jossa voi luoda uuden postauksen ja linkki omalle blogi-sivulle -->
            <?php if($blogExists) :?>
                <!-- 23.2. Linkki joka avaa modalin jonne tulostetaan modals/create-article-modal.php -tiedoston sisältö -->
                <a href="" alt="Uusi julkaisu"
                    hx-get="modals/create-article-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                >
                    Uusi julkaisu
                </a>
                <?php
                // 23.2 Haetaan käyttäjän blogin nimestä luotu slug ja määritellään muuttuja
                $slug = getSlug($conn, $_SESSION['user_id']);
                // 23.2. Tulostetaan linkki jossa on osoitteena käyttäjän blogin slug
                echo "<a href='blogit/$slug' target='_blank'>Blogisivusi</a>"; ?>
                <!-- 14.3. Linkki joka avaa modalin jonne tulostetaan modals/edit-blog-modal.php -tiedoston sisältö -->
                <a href="" alt="muokkaa blogia"
                    hx-get="modals/edit-blog-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                >
                    Muokkaa blogia
                </a>
                <!-- 2.3.25 Linkki joka avaa modalin jonne tulostetaan modals/edit-article-modal.php -tiedoston sisältö -->
                <a href="" alt="muokkaa julkaisuja"
                    hx-get="modals/edit-article-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                >
                    Muokkaa julkaisuja
                </a>
            <?php endif; ?>
            
            <!-- Linkki, joka avaa modalin ja hakee user_edit_modal.php -tiedoston sisällön #modal-containeriin -->
            <a href="" alt="Käyttäjätiedot"
                hx-get="modals/user-edit-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Käyttäjätiedot
            </a>
            <!-- Linkki, joka avaa modalin ja hakee modals/password_modal.php -tiedoston sisällön #modal-containeriin -->
            <a href="" alt="Vaihda salasana"
                hx-get="modals/password_modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
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

        <!-- 24.4.25 ESITELLÄÄN UUSIMMAT BLOGIT -->
    <div id="blog-grid" class="blog-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="blog-card">
                <a href="blogit/<?= htmlspecialchars($row['slug']) ?>">
                    <h1><?= htmlspecialchars($row['name']) ?></h1>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <!--<img src="images/tp.png">-->
                    <!--<div class="blog-description"><?= htmlspecialchars($row['description']) ?></div>-->
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <button id="load-more"
            class="load-more"
            data-offset="6"
            onclick="loadMoreBlogs()">
        Lisää blogeja
    </button>
    <!-- Piilotettava viesti kun ei ole enempää -->
    <p id="no-more-message" style="display: none; text-align: center; margin-top: 1rem;">
        Ei enempää blogeja.
    </p>
</div>
        </div>
        <!-- Esittelyotsikko -->
        <div class="presentation">
            <p>Luo ilmainen blogi!</p>
            <p>Täällä voit kertoa tarinasi </p>
            <p class="orange">tai ideasi helposti.</p>
            <br>
            <a href="https://tarinanpaikka.up.railway.app/blogit/mikon-blogi" target="_blank">Lue täältä uusimmat muutokset</a>
        </div>
        

        <!-- Esittelylaatikot -->
        <div class="row">
          <div class="column">
            <img src="images/webicon_small.png">
            <h1>Helppokäyttöinen</h1>
            <p>Rekisteröidy, luo blogi ja ala kirjoittamaan. Kaikki ilman koodaamista.</p>
            <a href="" alt="omat tiedot"
                hx-get="modals/user-guide-modal.php" 
                hx-target="#modal-container" 
                hx-trigger="click"
            >
                Lue ohjeet täältä!
            </a>
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
            <a href="terms.html" alt="Käyttöehdot">Käyttöehdot</a>
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

    // Suljetaan valikko, kun linkkiä klikataan (mutta ei hampurilaisikonia)
    document.addEventListener("click", function(e) {
      // Haetaan navigaatiopalkki sen ID:n perusteella
      var x = document.getElementById("myTopnav");

      // Jos klikattu elementti on navigaatiolinkki eikä hampurilaisikoni
      if(e.target.closest(".topnav.responsive a") && !e.target.closest(".icon")) {
         x.className = "topnav"; // Sulje valikko
      }
    });

    // Funktio joka lataa blogeja
    function loadMoreBlogs() {
    const button = document.getElementById('load-more');
    const offset = parseInt(button.getAttribute('data-offset'), 10);

    fetch(`fetch_blogs.php?offset=${offset}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('blog-grid')
                .insertAdjacentHTML('beforeend', data.html);

            if (data.hasMore) {
                button.setAttribute('data-offset', offset + 6);
            } else {
                button.style.display = 'none';
                document.getElementById('no-more-message').style.display = 'block';
            }
        });
    }
</script>
</body>
</html>
<!--
Index -sivun algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Asetetaan HTTP-otsikot tietoturvan parantamiseksi.
    Konfiguroidaan istunnon tietoturva-asetukset.
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään ja että käyttäjän ID on tietokannassa.
    Luodaan tokenille aikaraja.
    Tarkistetaan, onko sessiossa jo validi "modal_key".
        -Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon.
    Aloitetaan HTML-tiedosto ja määritellään dokumentin kieli lang="en".
    Määritellään merkistökoodauksen UTF-8:ksi.
    Määritellään sivun skaalaus mobiililaitteille.
    Asetetaan sivun otsikko.
    Ladataan ulkoinen CSS-tiedosto.
    Ladataan Font Awesome -ikonikirjasto.
    Ladataan HTMX-kirjasto.
    Näytetään banneri jossa on sivun otsikko.
    Tarkistetaan, onko käyttäjä kirjautunut sisään $loggedIn-muuttujan kautta.
    Jos käyttäjä ei ole kirjautunut: 
        -Näytetään "Aloita tästä" -nappi.
        -Näytetään "Oma Blogi" -nappi.
    Jos käyttäjä on kirjautunut:
        -Näytetään uloskirjautumisnappi.
    Aloitetaan navigointivalikko.
    Etusivu-linkki lisätään aina.
    Jos käyttäjä ei ole kirjautunut, lisätään "Yhteystiedot" -linkki.
    Jos käyttäjä on kirjautunut:
        -Tarkistetaan, onko käyttäjällä blogi.
    Jos ei ole blogia -> näytetään "Luo blogi" -linkki.
    Jos on blogi:
        -Näytetään "Uusi julkaisu" -nappi.
        -Haetaan käyttäjän blogin slug tietokannasta.
        -Luodaan linkki käyttäjän blogiin.
        -Näytetään "Muokkaa blogia" -linkki.
        -Näytetään "Muokkaa julkaisuja" -linkki.
    Näytetään "Käyttäjätiedot" -linkki.
    Näytetään "Salasanan vaihto" -linkki.
    Lisätään mobiiliresponsiivinen navigointipainike.
    Modaalikontti toimii dynaamisena alueena johon modaalit latautuvat käyttäjän klikkauksen perusteella.
    Jos käyttäjä ei ole kirjautunut:
        -Näytetään esittelysivu:
        -Esittelyteksti.
        -Linkki uusimpiin muutoksiin.
        -Neljä esittelylaatikkoa jossa kerrotaan blogipalvelun eduista.
    Jos käyttäjä on kirjautunut, näytetään dashboard.php, joka toimii käyttäjän hallintapaneelina.
    Lisätään alatunnisteen osiot:
        -Tietoa-sarja (Tietoturvaseloste, sähköpostiosoite, nimi).
        -Sosiaalinen media (linkit GitHubiin ja LinkedInniin).
    JavaScript: responsiivinen navigointi
        -myFunction() -funktio vaihtaa navigaation näkyvyyttä.
        -Haetaan navigaatiopalkki ID:n avulla (myTopnav).
        -Tarkistetaan, onko luokka topnav:
            Jos kyllä -> lisätään "responsive" -luokka.
            Jos ei -> poistetaan "responsive" -luokka.
-->