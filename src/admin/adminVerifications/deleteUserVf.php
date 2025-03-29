<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id= $_POST['user_id'] ?? null;

    if (is_null($user_id)) {
        echo "<p>Virhe: Blogi-ID puuttuu.</p>";
        exit();
    }

    // Haetaan käyttäjä tila tietokannasta
    $stmt = $conn->prepare("SELECT deleted_at FROM USER WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>Virhe: Käyttäjää ei löydy.</p>";
        exit();
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Tarkistetaan, onko käyttäjä jo aktiivinen (deleted_at on NULL)
    if (!is_null($user['deleted_at'])) {
        echo "<p>Käyttäjä on jo poistettu.</p>";
        exit();
    }

    // Päivitetään käyttäjän tila
    $stmt = $conn->prepare("UPDATE USER SET deleted_at = NOW() WHERE user_id=?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
            echo "<p>Käyttäjä poistettu!</p>";
    } else {
        echo "<p>Virhe tilin tilan päivittämisessä.</p>";
    }

    $stmt->close();

} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}
?>