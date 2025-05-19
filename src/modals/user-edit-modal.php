<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start(); // Aloitetaan sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
include_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.
checkIfModalAllowed(); // Tarkistetaan, onko URL:ssa parametrina modal_key 






  // Määritellään muuttujat
  $userId = $_SESSION['user_id'];

  $userData = getUserByUserId($conn, $userId);

  $firstname = $userData['firstname'];
  $lastname = $userData['lastname'];
  $email = $userData['email'];
?>
<!-- user-edit-modal.php -->
<!-- Sivu jossa käyttäjä voi päivittää omia tietojaan -->

<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/tp.png" alt="Logo" class="avatar">
  </div>
        <form 
          hx-post="./verifications/user-edit-vf.php" 
          hx-target="#response"
          hx-swap="innerHTML"
          hx-indicator="#loading-indicator" 
        >  
              <label for="firstname" aria-label="Etunimi">Etunimi </label>
          
              <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname);?>" required>
           
              <label for="lastname">Sukunimi </label>
            
              <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname);?>" required>
          
              <label for="email">Sähköposti</label>
            
              <!-- E-mailin oikea muoto -->
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email);?>" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">

              <input type="hidden" name="$user_id" value="<?= $_SESSION['user_id'] ?>">
          
              <input type="submit" value="Päivitä tietosi">

              <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
              <button type="submit" class="btn-cancel" 
              hx-get="modals/close-modal.php" 
              hx-target="#modal-container"
              >
                Peruuta
              </button>
            
              <!-- 13.2. Lisätty poista tili -painike joka poistaa tilin (pehmeä poisto) -->
              <button type="submit" class="btn-delete"  
              hx-post="verifications/delete-user-vf.php" 
              hx-target="#response"
              hx-swap="innerHTML"
              hx-confirm="Tilin tiedot poistetaan lopullisesti viikko poiston jälkeen. Tilin voi uudelleen aktivoida ottamalla yhteyttä järjestelmänvalvojaan."
              >
                Poista tili
              </button>
         
          </form>
          <!-- 19.5.25 Tämä viesti näkyy automaattisesti kun lomake lähetetään -->
          <div id="loading-indicator" class="htmx-indicator">
            <span class="spinner"></span>
            Päivitetään käyttäjätietoja...
          </div>
          <!-- 19.5.25 Lisätty spinneri -tyyli -->
          <style>
            @keyframes spin {
              to { transform: rotate(360deg); }
            }
          </style>
          <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut päivitys -->
          </div>
  </div>        
</div><!--/container-->
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
  user-edit-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, annetaan 403 -virheilmoitus.
-->