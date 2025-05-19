<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start(); // Aloitetaan sessio.

?>
<div id="loginModal" style="display: block;">
    <div class="modal-content">
        <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
        <div class="imgcontainer">
            <img src="images/tp.png" alt="Logo" class="avatar">
        </div>
        <!-- 19.5.25 lisätty hx-indicator latausilmoitusta varten -->
        <form 
            hx-post="./verifications/login-vf.php" 
            hx-target="#response" 
            hx-swap="innerHTML"
            autocomplete="off"
            hx-indicator="#loading-indicator" 
        >
            <label for="username">Käyttäjätunnus</label>
            <input type="text" id="username" name="username" autocomplete="username" required>

            <label for="password">Salasana</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <!-- Lisätään lomakkeeseen piilotettu kenttä joka lisää CSRF-tokenin -->
            <!--<input type="hidden" name="csrf_token" value="<?php // echo $_SESSION['csrf_token']; ?>">-->

            <input type="submit" value="Kirjaudu">

            <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
            <button type="submit" class="btn-cancel" 
              hx-get="modals/close-modal.php" 
              hx-target="#modal-container"
            >
                Peruuta
            </button>
        </form>
        <!-- 19.5.25 Tämä viesti näkyy automaattisesti kun lomake lähetetään -->
        <div id="loading-indicator" class="htmx-indicator">
            <span class="spinner"></span>
            Kirjaudutaan sisään...
        </div>
        <!-- 19.5.25 Lisätty spinneri -tyyli -->
        <style>
          @keyframes spin {
            to { transform: rotate(360deg); }
          }
        </style>
        <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut rekisteröinti -->
        </div>
    </div>
</div>

<!-- 19.5.25 Lisätty scripti jolla saadaan lataus -ilmoitus-->
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
  login-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
-->