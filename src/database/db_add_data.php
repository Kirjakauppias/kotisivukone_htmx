<?php
// db_add_data.php
// Otetaan yhteys tietokantaan:
require_once "db_connect.php";

// Funktio, jolla lisätään uusi käyttäjä tietokantaan
function addUser($conn, $firstname, $lastname, $username, $email, $hashedPassword, $status) {
    // SQL-lause uuden käyttäjän lisäämiseksi.
    $sql = "INSERT INTO user(firstname, lastname, username, email, password, status)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Valmistellaan SQL-lause
    if ($stmt = $conn->prepare($sql)) { // Tarkistetaan, onnistuiko valmistelu.
        // Bindataan (sidotaan) parametrit, joilla täytetään SQL-lause.
        // Käytetään "ssssss" -tyyppimääritystä, koska kaikki arvot ovat merkkijonoja.
        $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashedPassword, $status);
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