<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start();
// login-modal.php
// Palauttaa modaalin HTML-sisällön
?>
<div id="loginModal" style="display: block;">
    <div class="modal-content">
        <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
        <div class="imgcontainer">
            <img src="images/login.png" alt="Avatar" class="avatar">
        </div>
        <form 
            hx-post="./verifications/login-vf.php" 
            hx-target="#response" 
            hx-swap="innerHTML"
            autocomplete="off"
        >
            <label for="username">Käyttäjätunnus</label>
            <input type="text" id="username" name="username" autocomplete="username" required>

            <label for="password">Salasana</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <!-- Lisätään lomakkeeseen piilotettu kenttä joka lisää CSRF-tokenin -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <input type="submit" value="Kirjaudu">

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
</div>

<!--
  login-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
-->