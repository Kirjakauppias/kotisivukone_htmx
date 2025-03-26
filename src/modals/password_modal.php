<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start(); // Aloitetaan sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
include_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.
checkIfModalAllowed(); // Tarkistetaan, onko URL:ssa parametrina modal_key 

// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;
$userData = getUserByUserId($conn, $user_id);
?>

<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/password_avatar.jpg" alt="Avatar" class="avatar">
  </div>
        <form 
          hx-post="./verifications/password-vf.php" 
          hx-target="#response"
          hx-swap="innerHTML"
        >  
            <label for="password">Uusi salasana</label>
        
            <!-- Salasanan minimipituus on 8 merkkiä -->
            <!-- Selainautomaatio estetty -->
            <input type="password" id="password" name="password" autocomplete="off" required minlength="8">
            <p class="password-guide">8 merkkiä pitkä 1 erikoismerkki 1 iso kirjain</p>
            <label for="re_password">Uusi salasana uudelleen</label>

            <!-- Salasanan minimipituus on 8 merkkiä -->
            <!-- Selainautomaatio estetty -->
            <input type="password" id="re_password" name="re_password" autocomplete="off" required minlength="8">
            <input type="submit" value="Vaihda salasana">

            <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
            <button type="submit" class="btn-cancel" 
              hx-get="modals/close-modal.php" 
              hx-target="#modal-container"
            >
                Peruuta
            </button>
        </form>
          
        <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut päivitys -->
        </div>
  </div>        
</div><!--/container-->

<!--
  password-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, annetaan 403 -virheilmoitus.
-->