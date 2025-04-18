<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();
// login-vf.php
// getUserByUsername($conn, $username)
require_once "../database/db_enquiry.php";
// userLastLogin($conn, $user)
require_once "../database/db_add_data.php";
// display_errors($errors)
require_once "../funcs.php";

// Tarkistetaan, onko lomake lähetetty index.php:sta.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Alustetaan muuttujat ja tarkistetaan CSRF-token.
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password']);
    // Tarkistetaan HTTP-header:
    //$csrf_token = $_POST['csrf_token'] ?? '';

    // Virheilmoitusten taulukko, tyhjä.
    $errors = []; 

    // Tarkistetaan CSRF-token
    /*if ($csrf_token !== $_SESSION['csrf_token']) {
        $errors[] = "Virheellinen CSRF-token";
    }*/

    // Tarkistetaan tokenin voimassaolo:
    /*if (time() - ($_SESSION['csrf_token_time'] ?? 0) > 300) {
        $errors[] = "Istunto on vanhentunut. Lataa sivu uudelleen.";
    }*/

    // Tarkistetaan, että kentät eivät ole tyhjiä
    if (empty($username) || empty($password)) {
        $errors[] = "Täytä kaikki kentät";
    }

    // Haetaan käyttäjä tietokannasta
    $user = getUserByUsername($conn, $username);
    if (!$user || !password_verify($password, $user['password']) || $user['deleted_at'] != NULL) {
        $errors[] = "Virheellinen käyttäjätunnus tai salasana";
    }

    // Jos errors-taulukko ei ole tyhjä, tulostetaan taulukon tiedot
    // ja suljetaan ohjelma, muussa tapauksessa jatketaan kirjautumis-
    // prosessia.
    if (!empty($errors)) {
        display_errors($errors);
        exit();
    } else {
        // Päivitetään tietokantaan käyttäjän kirjautuminen
        userLastLogin($conn, $user);

        // Asetetaan sessiotiedot
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['status'] = $user['status'];

        // Generoidaan uusi CSRF-token.
        //$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        //$_SESSION['csrf_token_time'] = time();

        // Viedään käyttäjä etusivulle jolloin koko sivu päivittyy ja session-tiedot mukana
        header('HX-Redirect: index.php');
        exit();
    }
} else {
    // Jos ei ole POST:
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST -pyyntö sallittu";
    exit();
}

/*
    login-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
*/
?>