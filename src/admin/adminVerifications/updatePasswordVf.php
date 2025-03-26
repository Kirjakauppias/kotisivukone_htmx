<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<p>Virhe: Salasanat eivät täsmää.</p>";
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE USER SET password=? WHERE user_id=?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "<p>Salasana päivitetty onnistuneesti!</p>";
    } else {
        echo "<p>Virhe päivittäessä salasanaa.</p>";
    }
} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}
?>