<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'];

    $stmt = $conn->prepare("UPDATE ARTICLE SET deleted_at = IF(deleted_at IS NULL, NOW(), NULL) WHERE article_id=?");
    $stmt->bind_param("i", $article_id);

    if ($stmt->execute()) {
        echo "<p>Tilin tila päivitetty!</p>";
    } else {
        echo "<p>Virhe tilin päivittämisessä.</p>";
    }
} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}


?>