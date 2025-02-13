<?php
declare(strict_types=1);
session_start();
//delete-user-vf.php
//Tiedosto jossa suoritetaan käyttäjän tilin poisto
//deleteUser()
include_once '../database/db_add_data.php';

// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
  header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
  exit();
}

// Tarkistetaan, että pyyntö on lähetetty POST -metodilla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteUser($conn, $user_id);
    header('HX-Location: logout.php');
}


?>