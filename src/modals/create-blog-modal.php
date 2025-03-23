<?php
  declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
  require_once '../config.php'; // Virheiden käsittely
  session_start();

  $user_id = $_SESSION['user_id'] ?? null;
  // Tarkistetaan, onko käyttäjä kirjautunut sisään
  if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
?>
<!-- modals/create-blog-modal.php -->
<!-- Sivu jossa käyttäjä voi luoda blogin -->

<div id="loginModal" style="display: block;">
  <div class="modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/blog_avatar.jpg" alt="Avatar" class="avatar">
    </div>
    <form 
      hx-post="./verifications/create-blog-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
    >  
      <label for="blog_name">Blogin nimi*</label>
      <input type="text" id="blog_name" name="blog_name" required>
    
      <label for="blog_description">Kuvaus</label>
      <textarea id="blog_description" name="blog_description"></textarea>
    
      <p>* pakolliset kentät</p>
    
      <input type="submit" value="Luo blogi">
      
      <!-- Peruuta -painike joka sulkee lomakkeen -->
      <button type="button" class="btn-cancel" 
        hx-get="modals/close-modal.php" 
        hx-target="#modal-container"
      >
        Peruuta
      </button>
    </form>
    <div id="response" aria-live="polite" role="alert">
      <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut blogin luonti -->
    </div>
  </div>        
</div><!--/container-->

<!--
  create-blog-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
-->
