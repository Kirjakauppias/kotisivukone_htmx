<?php
declare(strict_types=1);
session_start();
require_once "database/db_enquiry.php";
require_once "funcs.php";

// Tarkistetaan, onko lomake lähetetty index.php:sta.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Alustetaan muuttujat ja tarkistetaan CSRF-token.
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password']);
    // Tarkistetaan HTTP-header:
    $csrf_token = $_POST['csrf_token'] ?? '';

    $errors = []; // Virheilmoitusten taulukko

    // Tarkistetaan CSRF-token
    if ($csrf_token !== $_SESSION['csrf_token']) {
        $errors[] = "Virheellinen CSRF-token";
    }

    // Tarkistetaan tokenin voimassaolo:
    if (time() - ($_SESSION['csrf_token_time'] ?? 0) > 300) {
        $errors[] = "CSRF -token vanhentunut. Lataa sivu uudelleen.";
    }

    // Tarkistetaan, että kentät eivät ole tyhjiä
    if (empty($username) || empty($password)) {
        $errors[] = "Täytä kaikki kentät";
    }

    // Haetaan käyttäjä tietokannasta
    $user = getUserByUsername($conn, $username);
    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = "Virheellinen käyttäjätunnus tai salasana";
    }

    if (!empty($errors)) {
        echo "
            <form
                class='login_form' 
                hx-post='login.php'
                hx-target='#login_container' 
                hx-swap='innerHTML'
                autocomplete='off'
            >
                <label for='username'>Käyttäjätunnus:</label>
                <input type='text' id='username' name='username' autocomplete='username' required>

                <label for='password'>Salasana:</label>
                <input type='password' id='password' name='password' autocomplete='current-password' required>

                <!-- Lisätään lomakkeeseen piilotettu kenttä joka lisää CSRF-tokenin -->
                <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>

                <input type='submit' value='Kirjaudu'>
            </form>
        ";
        display_errors($errors);
        exit();
    } else {
        // Asetetaan sessiotiedot
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['status'] = $user['status'];

        // Generoidaan uusi CSRF-token.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();

        // Viedään käyttäjä etusivulle jolloin koko sivu päivittyy ja session-tiedot mukana
        header('HX-Redirect: index.php');
        exit();
    }
} else {
    // Jos ei ole POST-pyyntöä, palautetaan virheilmoitus.
    header('HTTP/1.1 405 Method Not Allowed');
    echo "<div id='response'><p class='error'>Vain POST-pyynnöt sallittu.</div>";
    exit();
}
?>
