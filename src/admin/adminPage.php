<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin -page</title>
</head>
<body>
    <h1>Tarinan Paikan Admin -sivut</h1>
</body>
</html>
<?php
/*
 * Admin-sivun algoritmi (adminPage.php)
 * 
 *  Otetaan käyttöön tiukka tyyppimääritys.
 * 
 *  Aloita sessio ja varmista, että käyttäjä on kirjautunut.
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