<?php
// Otetaan yhteys tietokantaan:
require_once "db_connect.php";

// Funktio joka tarkastaa, että onko haettu data jo tietokannassa
function fetchStmt($stmt){
    // Haetaan tulos
    if($stmt->fetch()) {
        // Jos data löytyy, palautetaan TRUE
        $stmt->close();
        return TRUE;
    } else {
    // Jos data ei löydy, palautetaan FALSE
    $stmt->close();
    return FALSE;
    }
}

// Funktio joka tarkistaa, onko käyttäjätunnus jo tietokannassa
function checkUsernameExists($conn, $username) {
    // SQL-lause käyttäjänimen tarkistamiseksi
    $sql = "SELECT username FROM user WHERE username = ?";

    // Valmistellaan kysely
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Sidotaan parametrit
    $stmt->execute(); // Suoritetaan kysely
    $stmt->bind_result($username); // Sidotaan tulos muuttujaan

    return fetchStmt($stmt);
}

// Funktio joka tarkistaa, onko sähköposti jo tietokannassa
function checkUserEmailExists($conn, $email) {
    // SQL-lause käyttäjänimen tarkistamiseksi
    $sql = "SELECT email FROM user WHERE email = ?";

    // Valmistellaan kysely
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Sidotaan parametrit
    $stmt->execute(); // Suoritetaan kysely
    $stmt->bind_result($email); // Sidotaan tulos muuttujaan

    return fetchStmt($stmt);
}

// Funktio joka hakee käyttäjän tiedot
function getUserByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id, username, password, status FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>