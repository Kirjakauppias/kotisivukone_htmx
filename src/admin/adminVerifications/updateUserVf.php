<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE USER SET firstname=?, lastname=?, username=?, email=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $username, $email, $user_id);
    
    if ($stmt->execute()) {
        echo "<p>Tiedot päivitetty onnistuneesti!</p>";
        $stmt->close();
    } else {
        echo "<p>Virhe päivitettäessä tietoja.</p>";
        $stmt->close();
    }
} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}