<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once 'adminConfig.php'; // Virheiden käsittely.
session_start(); // Aloita sessio.

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once './adminDatabase/adminDbEnquiry.php';
require_once './adminFuncs.php';
 
isAdminLogged($conn); // Tarkistetaan, onko admin kirjautunut sisään ja että admin löytyy tietokannasta
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Järjestelmävalvojan kirjautumissivu</title>
    <link rel="stylesheet" href="adminStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="htmx.js" defer></script>
</head>
<body>
    <div class="admin-login-container">
        <h1>Järjestelmävalvojan kirjautumissivu</h1>
        <!-- Näytetään kirjautumislomake -->
        <form
            hx-post="./adminVerifications/adminLoginVf.php" 
            hx-target="#response" 
            hx-swap="innerHTML"
            autocomplete="off"
        >
            <!-- Käyttäjätunnus -->
            <input type="text" id="adminusername" name="adminusername" autocomplete="adminusername" placeholder="Käyttäjänimi" required>

            <!-- Salasana -->
            <input type="password" id="adminpassword" name="adminpassword" autocomplete="adminpassword" placeholder="Salasana" required>

            <!-- Lähetä -painike, joka sisältää tiedon adminLoginVf.php:lle HTMX:n kautta -->
            <input type="submit" value="Kirjaudu sisään">
        </form>
    </div>
    <!-- Näytetään alue johon tulostetaan mahdolliset virheilmoitukset -->
    <div id="response">
        <!-- Tulostetaan mahdolliset virheilmoitukset -->
    </div>
</body>
</html>

<?php
/*
    adminLogin.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys.
        Aloita sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä jo kirjautunut sisään. isAdminLogged($conn).
        Näytetään kirjautumislomake:
            -Käyttäjätunnus
            -Salasana
            -Lähetä -painike, joka sisältää tiedon adminLoginVf.php:lle HTMX:n kautta.
        Näytetään alue johon tulostetaan mahdolliset virheilmoitukset.
*/
?>