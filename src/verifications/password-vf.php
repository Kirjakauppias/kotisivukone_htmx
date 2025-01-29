<?php
declare(strict_types=1);
session_start();
// getUserByUderId()
include_once '../database/db_enquiry.php';
include_once '../database/db_add_data.php';
include_once '../funcs.php';

// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
  header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Määritellään syötteet muuttujiin
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['re_password'] ?? '';

    $errors = []; // Luodaan taulukko virheviesteille.

    // Varmistetaan että salasana on oikein.
    // Varmistetaan että salasana on 8 merkkiä pitkä.
    // Varmistetaan että salasanassa on vähintään yksi iso kirjain.
    // Varmistetaan että salasanassa on vähintään yksi numero.
    // Varmistetaan että salasanassa on vähintään yksi erikoismerkki.

    if ($password !== $repassword) {
        $errors[] = "Salasana ei täsmää.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Salasanan on oltava vähintään 8 merkkiä pitkä.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi iso kirjain.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi numero";
    }
    if (!preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi erikoismerkki.";
    } 

    // Tulostetaan virheviestit.
    if (!empty($errors)) { // Jos errors -taulukko ei ole tyhjä.
        display_errors($errors);
        exit();
    } else {
        // Hashataan salasana turvallisesti.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Päivitetään salasana:
        updateUserPassword($conn, $user_id, $hashedPassword);
        // Annetaan ilmoitus että käyttäjänimi on tallennettu tietokantaan onnistuneesti.
        echo "<div id='response'><p class='success'>Salasana on vaihdettu onnistuneesti!</p></div>";
        echo "<div id='response'><p class='success'>Voit sulkea ikkunan.</p></div>";
        exit();
    }

} else {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
?>