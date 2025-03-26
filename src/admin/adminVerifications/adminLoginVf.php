<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../adminConfig.php'; // Virheiden käsittely.
session_start(); // Aloita sessio.

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once '../adminDatabase/adminDbEnquiry.php';
require_once '../adminFuncs.php';
 
isAdminLogged($conn); // Tarkistetaan, onko admin kirjautunut sisään ja että admin löytyy tietokannasta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Alustetaan ja trimmataan muuttujat
    $username = htmlspecialchars(trim($_POST['adminusername'] ?? ''));
    $password = trim($_POST['adminpassword'] ?? '');

    $errors = []; // Luodaan virheilmoituksille tyhjä taulukko.

    // Tarkistetaan, että kentät eivät ole tyhjiä
    if (empty($username) || empty($password)) {
        $errors[] = "Täytä kaikki kentät";
    }

    // Haetaan käyttäjä tietokannasta
    $admin = getAdminByUsername($conn, $username);

    // Jos admin = null
    if (!$admin) {
        $errors[] = "Käyttäjää ei löytynyt.";
    // Jos salasana ei ole oikein.
    } elseif (!password_verify($password, $admin['password'])) {
        $errors[] = "Virheellinen käyttäjätunnus tai salasana.";
    // Jos käyttäjä on poistettu.
    } elseif ($admin['deleted_at'] !== null) {
        $errors[] = "Tili on poistettu.";
    // Jos käyttäjä ei ole admin.
    } elseif ($admin['role'] !== 'ADMIN') {
        $errors[] = "Ei riittäviä käyttöoikeuksia.";
    }

    // Jos errors-taulukko ei ole tyhjä, tulostetaan taulukon tiedot
    // ja suljetaan ohjelma, muussa tapauksessa jatketaan kirjautumis-
    // prosessia.
    if (!empty($errors)) {
        display_adminlogin_errors($errors);
        exit();
    } else {
        // Suojataan session fixation -hyökkäykset.
        session_regenerate_id(true);
        // Asetetaan sessiotiedot
        $_SESSION['user_id'] = $admin['user_id'];
        $_SESSION['adminusername'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];

        // Viedään käyttäjä admin-sivulle jolloin koko sivu päivittyy ja session-tiedot mukana
        header('HX-Redirect: /admin/adminPage.php');
        $conn->close();
        exit();
    }

} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}
/*
    adminLoginVf.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys.
        Aloita sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä jo kirjautunut sisään. isAdminLogged($conn).
        Tarkistetaan, onko tiedostoon tultu POST kautta, jos ei niin annetaan 403 virheilmoitus.
        Jos on tultu POSTin kautta:
            -Alustetaan ja trimmataan muuttujat.
            -Luodaan virheilmoituksille tyhjä taulukko.
            -Tarkistetaan, että kentät eivät ole tyhjiä. -> error
            -Haetaan käyttäjä tietokannasta.
            -Tarkistetaan, että admin != null -> error
            -Jos käyttäjän antamat tiedot ovat virheellisiä tai käyttäjällä ei ole admin -oikeuksia. -> error
            -Jos on erroreita, tulostetaan ne ja pysäytetään ohjelma.
            -Suojataan session fixation -hyökkäykset.
            -Asetetaan sessio tiedot.
            -Siirretään käyttäjä adminPage.php
            -Suljetaan tietokantayhteys.
            -Lopetetaan ohjelma.
*/

?>