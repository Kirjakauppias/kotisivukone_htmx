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

  $user_id = $_SESSION['user_id'] ?? null; // Asetetaan muuttujaan käyttäjän ID.
  
  $blog = getBlogByUserId($conn, $user_id); // Haetaan käyttäjän blogin tiedot.
?>

<div id="loginModal" style="display: block;">
  <div class="article-modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/blog_avatar.jpg" alt="Avatar" class="avatar">
    </div>
    <!-- 6.3.25 Lisätään tiedostojen lähetys: enctype -->
    <form 
      hx-post="./verifications/create-article-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
      enctype="multipart/form-data" 
      >  
      
      <!-- Lähetetään julkaisun otsikko -->
      <label for="article_title">Julkaisun otsikko*</label>
      <input type="text" id="article_title" name="article_title" required>
      
      <!-- Lähetetään julkaisun sisältö -->
      <label for="article_content">Sisältö</label>
      <textarea id="article_content" name="article_content"></textarea>
      
      <!-- 6.3. Kuvan lataus -->
      <label for="article_image">Lisää kuva</label>
      <input type="file" id ="article_image" name="article_image" accept="image/*">
      
      <p>* pakolliset kentät</p>
      
      <!-- Lähetetään blogin ID -->
      <input type="hidden" name="blog_id" value="<?php echo (int) $blog['blog_id']; ?>">
      
      <input type="submit" value="Luo julkaisu">
      
      <!-- Peruuta -painike joka sulkee lomakkeen -->
      <button type="button" class="btn-cancel" 
      hx-get="modals/close-modal.php" 
      hx-target="#modal-container"
      hx-refresh="true"
      >
      Peruuta
    </button>
  </form>
  <div id="response" aria-live="polite" role="alert">
    <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut artikkelin luonti -->
  </div>
</div>        
</div><!--/container-->

<!--
  create-article-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php).
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, ohjataan ../index.php
    Asetetaan muuttujaan käyttäjän ID.
    Haetaan käyttäjän blogin tiedot.
  
-->