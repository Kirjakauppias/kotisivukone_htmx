<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once './database/db_connect.php';

function loggedIn($conn) {
    // Tarkistetaan, onko käyttäjä istunnossa
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        return false;
    }

    // Suojattu tietokantakysely käyttäjän varmistamiseksi
    $query = $conn->prepare("SELECT user_id FROM USER WHERE user_id = ? LIMIT 1");
    if (!$query) {
        // Jos tietokannasta ei löydy haettua user_id:tä, tehdään merkintä logiin ja 
        // palautetaan false                
        error_log("Tietokantavirhe: " . $conn->error);
        return false;
    }

    $query->bind_param("i", $_SESSION['user_id']);
    $query->execute();
    $query->store_result();
    $isLoggedIn = $query->num_rows > 0;
    $query->close();

    return $isLoggedIn;
}

function requireLogin($conn) {
    if (!loggedIn($conn)) {
        header("Location: index.php");
        exit();
    }
}

/*
loginFuncs.php -tiedoston algoritmi:

    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);

*/
?>