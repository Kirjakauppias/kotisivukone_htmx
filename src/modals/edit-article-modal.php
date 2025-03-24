<?php
  declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
  require_once '../config.php'; // Virheiden käsittely
  session_start(); // Aloitetaan sessio
  
  // Ladataan tarvittavat tietokantayhteydet ja funktiot.
  require_once "../database/db_connect.php";
  require_once '../database/db_enquiry.php';
  require '../funcs.php';

  requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.
  checkIfModalAllowed(); // Tarkistetaan, onko URL:ssa parametrina modal_key 

  // Määritellään muuttuja
  $user_id = $_SESSION['user_id'] ?? null;
  
  // Haetaan käyttäjän blogi-päivitykset
  $stmt = $conn->prepare("SELECT a.article_id, a.title, a.content, a.image_path, a.deleted_at 
                          FROM ARTICLE a 
                          JOIN BLOG b 
                          ON a.blog_id = b.blog_id 
                          WHERE b.user_id = ? AND b.deleted_at IS NULL AND a.deleted_at IS NULL"
                        );
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $articles = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  ?>

<div id="loginModal" style="display: block;">
  <div class="article-modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/blog_avatar.jpg" alt="Avatar" class="avatar">
    </div>

    <!-- Artikkelin valinta -->
    <select id="article_select" onchange="fillForm()">
      <option value="">--Valitse julkaisu--</option>
      <?php foreach ($articles as $article): ?>
        <option value="<?= htmlspecialchars(json_encode($article), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($article['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Lomake artikkelin muokkaamiseen-->
    <form 
      hx-post="./verifications/edit-article-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
      enctype="multipart/form-data"
    >  
      <input type="hidden" id="article_id" name="article_id">
      
      <label for="article_title">Julkaisun otsikko</label>
      <input type="text" id="article_title" name="article_title" required>

      <label for="article_content">Sisältö</label>
      <textarea id="article_content" name="article_content"></textarea>

      <!-- 6.3. Kuvan esittäminen ja poistaminen -->
      <label for="article_image">Lisää, poista tai vaihda kuva. Päivitä julkaisu.</label>
      <input type="file" id="article_image" name="article_image">

      <div id="image-container">
        <label>Nykyinen kuva:</label>
        <div id="current-image-wrapper">
          <img id="current-image" src="" alt="Artikkelin kuva" style="max-width: 200px; display: none;">
          <button type="button" id="delete-image-btn"
            hx-post="./verifications/delete-image-vf.php"
            hx-target="#response"
            hx-confirm="Haluatko varmasti poistaa kuvan?"
            hx-vals='{}'
            style="display: none;"
          >
            Poista kuva
          </button>
        </div>
      </div>

      
      <!--<div class="image-preview">
          <img id="article-image-preview" src="" alt="Ei kuvaa" style="max-width: 100%; display: none;">
          
      </div>-->
    
      <input type="submit" value="Päivitä julkaisu">

      
      <!-- Peruuta -painike joka sulkee lomakkeen -->
      <button type="button" class="btn-cancel" 
      hx-get="modals/close-modal.php" 
      hx-target="#modal-container"
      >
      Peruuta
    </button>
    <!-- 11.3.25: Poista julkaisu -painike -->
    <button type="button" id="delete-article-btn"
      hx-post="./verifications/delete-article-vf.php"
      hx-target="#response"
      hx-confirm="Haluatko varmasti poistaa tämän julkaisun? Julkaisun kuva poistetaan pysyvästi."
      hx-vals='{}'
      style="display: none; background-color: red; color: white; width: 100%; margin-top: 3rem;"
    >
      Poista julkaisu
    </button>
    </form>
    <div id="response" aria-live="polite" role="alert">
      <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut artikkelin luonti -->
    </div>
  </div>        
</div><!--/container-->
<script>
    function fillForm() {
    let select = document.getElementById('article_select');
    let selectedValue = select.value;
    let deleteArticleBtn = document.getElementById('delete-article-btn');

    if (selectedValue) {
        let article = JSON.parse(selectedValue);
        document.getElementById('article_id').value = article.article_id;
        document.getElementById('article_title').value = article.title;
        document.getElementById('article_content').value = article.content;

        // 6.3. Näytetään kuva, jos sellainen on
        if (article.image_path) {
            document.getElementById('current-image').src = article.image_path;
            document.getElementById('current-image').style.display = "block";
            document.getElementById('delete-image-btn').setAttribute("hx-vals", JSON.stringify({"article_id": article.article_id}));
            document.getElementById('delete-image-btn').style.display = "inline-block";
        } else {
            document.getElementById('current-image').style.display = "none";
            document.getElementById('delete-image-btn').style.display = "none";
        }

        // 11.3. Näytetään "Poista julkaisu" -painike vain, jos artikkelilla ei ole deleted_at-arvoa
        if (!article.deleted_at) { 
            deleteArticleBtn.setAttribute("hx-vals", JSON.stringify({"article_id": article.article_id}));
            deleteArticleBtn.style.display = "inline-block";
        } else {
            deleteArticleBtn.style.display = "none";
        }

    } else {
        document.getElementById('article_id').value = '';
        document.getElementById('article_title').value = '';
        document.getElementById('article_content').value = '';
        document.getElementById('current-image').style.display = "none";
        document.getElementById('delete-image-btn').style.display = "none";
        deleteArticleBtn.style.display = "none"; // Piilotetaan "Poista julkaisu" -nappi
    }
}
</script>

<!--
  edit-article-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, ohjataan ../index.php
-->
