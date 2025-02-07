<?php
// db_add_data.php
// Otetaan yhteys tietokantaan:
require_once "db_connect.php";

// Funktio, jolla lisätään uusi käyttäjä tietokantaan
function addUser($conn, $firstname, $lastname, $username, $email, $hashedPassword) {
    // SQL-lause uuden käyttäjän lisäämiseksi.
    $sql = "INSERT INTO USER(firstname, lastname, username, email, password)
            VALUES (?, ?, ?, ?, ?)";

    // Valmistellaan SQL-lause
    if ($stmt = $conn->prepare($sql)) { // Tarkistetaan, onnistuiko valmistelu.
        // Bindataan (sidotaan) parametrit, joilla täytetään SQL-lause.
        // Käytetään "ssssss" -tyyppimääritystä, koska kaikki arvot ovat merkkijonoja.
        $stmt->bind_param("sssss", $firstname, $lastname, $username, $email, $hashedPassword);
        // Suoritetaan kysely tietokantaan.
        $stmt->execute();               
        // Suljetaan valmisteltu lausunto, kun sitä ei enää tarvita.
        $stmt->close();
    } else {
        // Jos kyselyn valmistelu epäonnistuu, näytetään virheilmoitus käyttäjälle
        echo "
                <div id='response'>
                    <p>Virhe kyselyn valmistelussa: " . $conn->error . "
                </div>    
            ";
    }
}

// Funktio joka päivittää käyttäjän viimeisimmän kirjautumisen tietokantaan
function userLastLogin($conn, $user) {
    $stmt = $conn->prepare("UPDATE USER SET last_login = NOW() WHERE user_id = ?");
    $stmt->bind_param("i", $user['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Funktio joka päivittää käyttäjän tiedot
function updateUserData($conn, $user_id, $firstname, $lastname, $email) {
    // Valmistellaan SQL -lauseke
    $sql = "UPDATE USER SET firstname = ?, lastname = ?, email = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $firstname, $lastname, $email, $user_id);

    // Suoritetaan statement ja palautetaan tulos
    if($stmt->execute()) {
        $stmt->close();
        return "<div id='result'>Tiedot päivitetty onnistuneesti! Muutokset näkyvät kun kirjaudut palveluun uudestaan.</div>";
    } else {
        $stmt->close();
        return "<div id='result'>Virhe tietojen päivittämisessä</div>";
    }
}

// Funktio joka päivittää käyttäjän salasanan
function updateUserPassword($conn, $user_id, $hashedPassword){
    // Valmistellaan SQL -lauseke
    $sql = "UPDATE USER SET password = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $user_id);

    // Suoritetaan statement ja palautetaan tulos
    if($stmt->execute()) {
        $stmt->close();
        return "<div id='result'>Tiedot päivitetty onnistuneesti! Muutokset näkyvät kun kirjaudut palveluun uudestaan.</div>";
    } else {
        $stmt->close();
        return "<div id='result'>Virhe tietojen päivittämisessä</div>";
    }
}
