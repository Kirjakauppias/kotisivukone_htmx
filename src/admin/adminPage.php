<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../config.php'; // Virheiden käsittely.
//session_destroy();
session_start(); // Aloita sessio.

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once './adminDatabase/adminDbEnquiry.php';
require_once './adminFuncs.php';

requireLogin($conn); // Varmistetaan että käyttäjä tulee kirjautumis-sivulta.

// Tarkistetaan, onko sessiossa jo validi "modal_key"
if (empty($_SESSION['modal_key'])) {
   // Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon
   $_SESSION['modal_key'] = bin2hex(random_bytes(32)); // Luodaan satunnainen 64-merkkinen avain
}

//debug();
?>

<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin sivut</title>
   <link rel="stylesheet" href="adminStyle.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <script src="htmx.js" defer></script>
</head>
<body>
   <h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['adminusername'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
   <!-- Uloskirjautumis -painike josta ohjataan logout.php -tiedostoon ja sitä kautta takaisin index.php -tiedostoon -->
   <button class="btn-logout"><a href="adminLogout.php">Kirjaudu ulos</a></button>
   <br>
   <button
      hx-get="adminModals/showUserModal.php" 
      hx-target="#modal-container" 
      hx-trigger="click"
   >
      Näytä käyttäjät
   </button>

   <div id="modal-container">

   </div>

   
   <!-- Näytetään alue johon tulostetaan mahdolliset virheilmoitukset -->
   <div id="response">
           <!-- Tulostetaan mahdolliset virheilmoitukset -->
   </div>
</body>
</html>



<?php
/*
 * Admin-sivun algoritmi (adminPage.php)
    
    Otetaan käyttöön tiukka tyyppimääritys.
    Aloita sessio.
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Varmista, että käyttäjä on kirjautunut.
    Tarkistetaan, onko sessiossa jo validi "modal_key".
      -Jos ei ole, luodaan satunnainen avain ja tallennetaan se sessioon.


 *    - Jos käyttäjä ei ole kirjautunut, ohjaa hänet login-sivulle.
 * 
 *  Tarkista, onko käyttäjällä admin-oikeudet.
 *    - Jos käyttäjä ei ole admin, näytä virheilmoitus tai ohjaa pois.
 * 
 *  Lataa tarvittavat tietokantayhteydet ja funktiot.
 *    - Sisällytä db_add_data.php, db_enquiry.php ja funcs.php.
 * 
 *  Näytä admin-paneelin käyttöliittymä:
 *    - Lista kaikista käyttäjistä ja mahdollisuus muokata/poistaa heitä.
 *    - Lista kaikista blogeista ja mahdollisuus muokata/poistaa niitä.
 *    - Yleiset asetukset (esim. käyttäjähallinta, raportit, asetukset).
 * 
 *  Käsittele lomakkeet ja AJAX-pyynnöt:
 *    - Käyttäjän lisääminen/muokkaaminen/poistaminen.
 *    - Blogien hallinta (esim. poistaminen, muokkaaminen).
 *    - Raporttien ja ilmoitusten käsittely.
 * 
 *  Sulje tietokantayhteys ja päätä skripti.
 */
?>