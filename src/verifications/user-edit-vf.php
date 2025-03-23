<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();
// user-edit-vf.php
// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;

if (!isset($user_id) || !is_numeric($user_id)) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
//checkEmailUnique
include_once "../database/db_enquiry.php";
//updateUserData()
include_once "../database/db_add_data.php";

// Tarkistetaan, että pyyntö on lähetetty POST -metodilla
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Muuttujiin arvot inputeista
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $emailUnique = checkEmailUnique($conn, $email, $user_id);

    // Virheiden tarkistus ja tulostus
    $errors = [];

    if (empty($firstname)) {
        $errors[] = "Etunimi ei voi olla tyhjä.";
    }
    if (empty($lastname)) {
        $errors[] = "Sukunimi ei voi olla tyhjä.";
    }
    //14.2.25 Lisätty tarkistus että email on vain käyttäjällä itsellään.
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$emailUnique) {
        $errors[] = "Virheellinen sähköposti.";
    }

    // Tulostetaan virheet
    if (!empty($errors)) {
        echo "<div id='result'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
        exit;
    }

    echo updateUserData($conn, $user_id, $firstname, $lastname, $email);

    // Lisää skripti modalin automaattiseen sulkemiseen
    echo "<script>
    setTimeout(() => {
        document.getElementById('modal-container').innerHTML = '';
    }, 3000); // Sulkee modalin 3 sekunnin kuluttua
    </script>";

    $conn->close();
} else {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}

/*
    edit-user-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
*/
?>