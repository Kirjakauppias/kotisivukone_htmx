<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start(); // Aloitetaan sessio
?>
<!-- user-register-modal.php -->
<!-- Sivu jossa käyttäjä voi luoda tunnuksen itselleen -->

<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/register_avatar.png" alt="Avatar" class="avatar">
  </div>
        <form 
          hx-post="./verifications/user-register-vf.php" 
          hx-target="#response"
          hx-swap="innerHTML"
        >  
              <label for="firstname" aria-label="Etunimi">Etunimi*</label>
          
              <input type="text" id="firstname" name="firstname" required>
           
              <label for="lastname">Sukunimi*</label>
            
              <input type="text" id="lastname" name="lastname" required>
           
              <label for="username">Käyttäjätunnus*</label>
           
              <!-- Selainautomaatio estetty -->
              <input type="text" id="username" name="username" autocomplete="off" required>
          
              <label for="email">Sähköposti*</label>
            
              <!-- E-mailin oikea muoto -->
              <input type="email" id="email" name="email" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
          
              <label for="password">Salasana*</label>
           
              <!-- Salasanan minimipituus on 8 merkkiä -->
              <!-- Selainautomaatio estetty -->
              <input type="password" id="password" name="password" autocomplete="off" required minlength="8">
              <p class="password-guide">8 merkkiä pitkä 1 erikoismerkki 1 iso kirjain</p>
              
              <label for="re_password">Salasana uudelleen*</label>
          
              <!-- Salasanan minimipituus on 8 merkkiä -->
              <!-- Selainautomaatio estetty -->
              <input type="password" id="re_password" name="re_password" autocomplete="off" required minlength="8">
          
            <!-- Lisätään CSRF-token joka tarkistetaan palvelinpuolella -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <p>* pakolliset kentät</p>
          
            <input type="submit" value="Lisää tili">
            
            <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
            <button type="submit" class="btn-cancel" 
              hx-get="modals/close-modal.php" 
              hx-target="#modal-container"
            >
                Peruuta
            </button>
         
          </form>
          <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut rekisteröinti -->
          </div>
  </div>        
</div><!--/container-->

<!--
  user-register-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
-->