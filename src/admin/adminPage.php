<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../config.php'; // Virheiden käsittely.
//session_destroy();
session_start(); // Aloita sessio.

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once './adminDatabase/adminDbEnquiry.php';
require_once './adminFuncs.php';

requireLogin($conn); // Varmistetaan että käyttäjä tulee kirjautumis-sivulta.

// Tarkistetaan, onko sessiossa jo validi "modal_key"
if (empty($_SESSION['modal_key'])) {
   // Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon
   $_SESSION['modal_key'] = bin2hex(random_bytes(32)); // Luodaan satunnainen 64-merkkinen avain
}

// Hakee uusimmat artikkelit, blogit ja käyttäjät
$latestArticles = fetchLatest($conn, 'ARTICLE', 'article_id, title, content, image_path, created_at, deleted_at');
$latestBlogs = fetchLatest($conn, 'BLOG', 'blog_id, name, description, created_at');
$latestUsers = fetchLatest($conn, 'USER', 'user_id, username, firstname, lastname, email, created_at');
//debug();
?>

<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin sivut</title>
   <link rel="stylesheet" href="adminStyle.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <script src="htmx.js" defer></script>
</head>
<body>
   <div class="topnav" id="myTopnav">
      <a href="" alt="home" class="imagelink"><img src="adminImages/tp.png"></a>

      <a href="" alt="Näytä käyttäjät"
         hx-get="adminModals/showUserCardsModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Käyttäjät
      </a>

      <a href="" alt="Näytä käyttäjät"
         hx-get="adminModals/showUserModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Käyttäjät (taulukko)
      </a>
      <a
         href="" alt="Näytä blogit"
         hx-get="adminModals/showBlogCardsModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Blogit
      </a> <a
         href="" alt="Näytä blogit"
         hx-get="adminModals/showBlogModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Blogit (taulukko)
      </a>
      <a href="" alt="Näytä julkaisut"
         hx-get="adminModals/showArticleCardsModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Julkaisut 
      </a>
      <a href="" alt="Näytä julkaisut"
         hx-get="adminModals/showArticleModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Julkaisut (taulukko)
      </a>
      <a href="adminLogout.php">Kirjaudu ulos</a>
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
        <h2>Viisi uusinta julkaisua</h2>
        <div id="article-cards">
            <?php if ($latestArticles->num_rows > 0): ?>
                <?php while ($article = $latestArticles->fetch_assoc()): ?>
                    <div id="cards">
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <div id="card-img-container" style="height: 100px;">
                            <?php 
                                        if ($article['deleted_at'] === null && !empty($article['image_path'])) { ?>
                                            <img src="<?= ($article['image_path']) ?>" alt="Julkaisun kuva" style="width:100px; height:100px; border-radius:80%;">
                                  <?php } else {
                                    ?><p>Julkaisussa ei kuvaa</p>
                                  <?php }
                            ?>
                        </div>
                        <p><?= htmlspecialchars($article['content']) ?></p>
                        <a href="#" 
                           hx-get="adminModals/editArticleModal.php?article_id=<?= $article['article_id'] ?>" 
                           hx-target="#modal-container" 
                           hx-swap="innerHTML">
                           Muokkaa
                        </a> 
                    </div>
                    <?php endwhile; ?>
                                
            <?php else: ?>
                <p>Ei julkaisuja.</p>
            <?php endif; ?>
        </div>

        <h2>Viisi uusinta blogia</h2>
        <div id="blog-cards">
            <?php if ($latestBlogs->num_rows > 0): ?>
                <?php while ($blog = $latestBlogs->fetch_assoc()): ?>
                    <div id="cards">
                        <h3><?= htmlspecialchars($blog['name']) ?></h3>
                        <p><?= htmlspecialchars($blog['description']) ?></p>
                        <a href="#" 
                           hx-get="adminModals/editBlogModal.php?blog_id=<?= $blog['blog_id'] ?>" 
                           hx-target="#modal-container" 
                           hx-swap="innerHTML" 
                        >
                           Muokkaa
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Ei blogeja.</p>
            <?php endif; ?>
        </div>

        <h2>Viisi uusinta käyttäjää</h2>
        <div id="user-cards">
            <?php if ($latestUsers->num_rows > 0): ?>
                <?php while ($user = $latestUsers->fetch_assoc()): ?>
                    <div id="cards">
                        <h3><?= htmlspecialchars($user['username']) ?></h3>
                        <p><strong>Etunimi:</strong> <?= htmlspecialchars($user['firstname']) ?></p>
                        <p><strong>Sukunimi:</strong> <?= htmlspecialchars($user['lastname']) ?></p>
                        <p><strong>Sähköposti:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <a href="#" 
                           hx-get="adminModals/editUserModal.php?user_id=<?= $user['user_id'] ?>" 
                           hx-target="#modal-container" 
                           hx-swap="innerHTML">
                           Muokkaa
                        </a> 
                    </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                <p>Ei käyttäjiä.</p>
            <?php endif; ?>
        </div>
    </div>

   <div id="response">
   <!-- Näytetään alue johon tulostetaan mahdolliset virheilmoitukset -->
   
           <!-- Tulostetaan mahdolliset virheilmoitukset -->
   </div>
</body>
</html>

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
</script>

<?php
/*
 * Admin-sivun algoritmi (adminPage.php)
    
    Otetaan käyttöön tiukka tyyppimääritys.
    Aloita sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Varmista, että käyttäjä on kirjautunut.
    Tarkistetaan, onko sessiossa jo validi "modal_key".
      -Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon.


 *    - Jos käyttäjä ei ole kirjautunut, ohjaa hänet login-sivulle.
 * 
 *  Tarkista, onko käyttäjällä admin-oikeudet.
 *    - Jos käyttäjä ei ole admin, näytä virheilmoitus tai ohjaa pois.
 * 
 *  Lataa tarvittavat tietokantayhteydet ja funktiot.
 *    - Sisällytä db_add_data.php, db_enquiry.php ja funcs.php.
 * 
 *  Näytä admin-paneelin käyttöliittymä:
 *    - Lista kaikista käyttäjistä ja mahdollisuus muokata/poistaa heitä.
 *    - Lista kaikista blogeista ja mahdollisuus muokata/poistaa niitä.
 *    - Yleiset asetukset (esim. käyttäjähallinta, raportit, asetukset).
 * 
 *  Käsittele lomakkeet ja AJAX-pyynnöt:
 *    - Käyttäjän lisääminen/muokkaaminen/poistaminen.
 *    - Blogien hallinta (esim. poistaminen, muokkaaminen).
 *    - Raporttien ja ilmoitusten käsittely.
 * 
 *  Sulje tietokantayhteys ja päätä skripti.
 */
?>