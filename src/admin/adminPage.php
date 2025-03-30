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
   <div class="topnav" id="myTopnav">
      <a href="" alt="home" class="imagelink"><img src="adminImages/tp.png"></a>

      <a href="" alt="Näytä käyttäjät"
         hx-get="adminModals/showUserModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Näytä käyttäjät
      </a>
      <a
         href="" alt="Näytä blogit"
         hx-get="adminModals/showBlogCardsModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Näytä blogit
      </a> <a
         href="" alt="Näytä blogit"
         hx-get="adminModals/showBlogModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Näytä blogit (taulukkona)
      </a>
      <a href="" alt="Näytä julkaisut"
         hx-get="adminModals/showArticleModal.php" 
         hx-target="#modal-container" 
         hx-trigger="click"
      >
         Näytä julkaisut
      </a>
      <a href="adminLogout.php">Kirjaudu ulos</a>
      <!-- 
            Tämä on responsiivisen navigaatiopalkin painike, 
            joka näkyy mobiililaitteilla. Kun käyttäjä klikkaa 
            painiketta, kutsutaan JavaScript-funktiota `myFunction()`, 
            joka vaihtaa navigaatiopalkin näkyvyyttä.
        -->
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i> <!-- Font Awesome -ikonina hampurilaisvalikko -->
        </a>
   </div>
   
  

   <div id="modal-container">

   </div>

   
   <!-- Näytetään alue johon tulostetaan mahdolliset virheilmoitukset -->
   <div id="response">
           <!-- Tulostetaan mahdolliset virheilmoitukset -->
   </div>
</body>
</html>

<script>
    // Funktio navigoinnin responsiivisuuteen
    function myFunction() {
        // Haetaan navigaatiopalkki sen ID:n perusteella
        var x = document.getElementById("myTopnav");

        // Tarkistetaan, onko navigaatiopalkin nykyinen luokka "topnav"
        if (x.className === "topnav") {
            // Jos on, lisätään "responsive"-luokka, joka näyttää valikon
            x.className += " responsive";
        } else {
            // Jos "responsive" -luokka on jo lisätty, poistetaan se
            x.className = "topnav";
        }
    }
</script>

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