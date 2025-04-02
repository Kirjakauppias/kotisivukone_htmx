<?php
//declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start(); // Aloitetaan sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
require_once "../database/db_connect.php";
require_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.
checkIfModalAllowed(); // Tarkistetaan, onko URL:ssa parametrina modal_key 

$user_id = $_SESSION['user_id'] ?? null; // Määritellään user_id -muuttuja.

$blog = getBlogByUserId($conn, $user_id); // Haetaan blogi tietokannasta.

if(!$blog) {
    die("Blogin tietoja ei löytynyt");
}

// Asetetaan arvot muuttujiin
$blogname = htmlspecialchars($blog['name'] ?? '');
$blogdescription = ($blog['description'] ?? '');
$blog_id = ($blog['blog_id'] ?? '');

?>

<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/tp.png" alt="Logo" class="avatar">
  </div>
        <form 
          hx-post="./verifications/edit-blog-vf.php" 
          hx-target="#response"
          hx-swap="innerHTML"
        >     
            <input type="hidden" name="blog_id" value="<?= htmlspecialchars($blog_id); ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">

            <label for="blogname" aria-label="blogname">Blogin nimi </label>
        
            <input type="text" id="blogname" name="blogname" value="<?php echo htmlspecialchars($blogname);?>" required>
        
            <label for="blog_description">Kuvaus</label>
            
            <textarea id="blog_description" name="blog_description"><?= $blogdescription; ?></textarea>
            <input type="submit" value="Päivitä blogitiedot">
            
            <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
            <button type="submit" class="btn-cancel" 
            hx-get="modals/close-modal.php" 
            hx-target="#modal-container"
            >
              Peruuta
            </button>
        
            <!-- 13.2. Lisätty poista blogi -painike joka poistaa blogin (pehmeä poisto) -->
            <button type="submit" class="btn-delete"  
            hx-post="verifications/delete-blog-vf.php" 
            hx-target="#response"
            hx-swap="innerHTML"
            hx-confirm="Blogin tiedot poistetaan lopullisesti viikko poiston jälkeen. Tilin voi uudelleen aktivoida ottamalla yhteyttä järjestelmänvalvojaan."
            >
              Poista blogi
            </button>
         
          </form>
        
          <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut päivitys -->
          </div>
  </div>        
</div><!--/container-->

<?php
    $conn->close();
?>

<!--
  edit-blog-modal.php algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1); HUOM! EI TOIMI 23.3.25
    Ladataan virheidenkäsittely (config.php)
    Aloitetaan sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
    Jos käyttäjä yrittää avata modalia URL:n kautta, annetaan 403 -virheilmoitus.
-->