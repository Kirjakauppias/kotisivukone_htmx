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

  // Jos blogia ei löydy, estetään pääsy modalin kautta.
  if (!$blog || empty($blog['blog_id'])) {
    http_response_code(403);
    echo "Pääsy estetty: Blogia ei löytynyt";
    exit();
  }
?>

<!-- Luodaan modal-ikkuna julkaisun luomista varten. -->
<div id="loginModal" style="display: block;">
  <div class="article-modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/tp.png" alt="Logo" class="avatar">
    </div>
    <!-- Näytetään artikkelin luontilomake -->
    <!-- 6.3.25 Lisätään tiedostojen lähetys: enctype -->
    <!-- 14.5.25 Lisätään lataus-ilmoitus: hx-indicator -->
    <form 
      hx-post="./verifications/create-article-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
      enctype="multipart/form-data"
      hx-indicator="#loading-indicator" 
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

    <!-- 14.5.25 Tämä viesti näkyy automaattisesti kun lomake lähetetään -->
    <div id="loading-indicator" class="htmx-indicator" style="display:none; margin-top:1rem; color:#555;">
      <span class="spinner" style="display:inline-block; width:1rem; height:1rem; border:2px solid #ccc; border-top-color:#333; border-radius:50%; animation: spin 1s linear infinite;"></span>
      Ladataan artikkelia...
    </div>

    <style>
      @keyframes spin {
        to { transform: rotate(360deg); }
      }
    </style>


    <!-- Lisätään "response" -elementti virheiden ja onnistumisviestien näyttämiseen. -->
    <div id="response" aria-live="polite" role="alert">
      <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut artikkelin luonti -->
    </div>
  </div>
<!-- Suljetaan modalin pääkontaineri. -->        
</div>

<script>
  document.body.addEventListener('htmx:send', function() {
    console.log('HTMX lähettää pyynnön');
  });
  document.body.addEventListener('htmx:beforeRequest', function() {
    document.getElementById('loading-indicator').style.display = 'block';
  });
  document.body.addEventListener('htmx:afterSwap', function() {
    document.getElementById('loading-indicator').style.display = 'none';
  });
</script>

<!--
  create-article-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php).
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, annetaan 403 -virheilmoitus.
    Asetetaan muuttujaan käyttäjän ID.
    Haetaan käyttäjän blogin tiedot tietokannasta.
    Jos blogia ei löydy, estetään pääsy modalin kautta.
    Luodaan modal-ikkuna julkaisun luomista varten.
    Lisätään lataus -ilmoitus.
    Näytetään artikkelin luontilomake:
      -Otsikko (pakollinen).
      -Sisältö.
      -Kuvan lisääminen.
      -Piilotettu kenttä, joka sisältää blogin ID:n.
      -Lähetä -painike, joka sisältää tiedon create-article-vf.php:lle HTMX:n kautta.
      -Peruuta-painike, joka sulkee modalin ilman muutoksia.
    Lisätään "response" -elementti virheiden ja onnistumisviestien näyttämiseen.
    Suljetaan modalin pääkontaineri.
-->