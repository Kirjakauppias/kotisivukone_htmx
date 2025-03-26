<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once 'adminDbConnect.php'; // Ladataan tarvittava tietokantayhteys.

// Funktio, joka tarkistaa, onko käyttäjä kirjautunut ja onko hän ADMIN
function adminLoggedIn($conn) {
    // Tarkistetaan, onko käyttäjä istunnossa
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        return false;
    }

    // Suojattu tietokantakysely käyttäjän varmistamiseksi
    $query = $conn->prepare("SELECT role FROM USER WHERE user_id = ? LIMIT 1");
    if (!$query) {
        // Jos tietokannasta ei löydy haettua user_id:tä, tehdään merkintä logiin ja 
        // palautetaan false                
        error_log("Tietokantavirhe: " . $conn->error);
        return false;
    }

    // Suoritetaan kysely ja haetaan tulokset
    $role = null;
    $query->bind_param("i", $_SESSION['user_id']);
    $query->execute();
    $query->bind_result($role);
    $query->fetch();
    $query->close();

    // Tarkistetaan, onko käyttäjän rooli ADMIN
    return ($role === 'ADMIN');
}

//Funktio, joka ohjaa käyttäjän admin-sivulle, jos hän on kirjautunut adminiksi
function isAdminLogged($conn) {
    if (adminLoggedIn($conn)) {
        header("Location: /admin/adminPage.php");
        exit();
    }
}

// Funktio joka hakee adminin tiedot
function getAdminByUsername($conn, $username) {
    // Valmistellaan kysely
    $stmt = $conn->prepare("SELECT user_id, username, password, role, deleted_at FROM USER WHERE username = ?");
    $stmt->bind_param("s", $username); // Sidotaan käyttäjätunnus parametriin
    $stmt->execute(); // Suoritetaan kysely
    $result = $stmt->get_result(); // Haetaan tulokset
    return $result->fetch_assoc(); // Palauttaa käyttäjän tiedot assosiatiivisena taulukkona
}

// Funktio, joka tarkistaa että käyttäjä on kirjautunut ja että käyttäjän ID on tietokannassa.
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

// Funktio joka tarkistaa kirjautumisen ja siirtää käyttäjän etusivulle jos ei ole kirjautunut
function requireLogin($conn) {
    if (!loggedIn($conn)) {
        header("Location: /admin/adminLogin.php");
        exit();
    }
}
/*
    adminDbEnquiry.php algoritmi

        Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
        Ladataan tarvittava tietokantayhteys.
        Funktio joka tarkistaa, onko admin kirjautunut sisään.
        Funktio joka ohjaa käyttäjän admin-sivuille jos hän on jo kirjautunut adminiksi.
        Funktio joka hakee adminin tiedot.
*/
?>