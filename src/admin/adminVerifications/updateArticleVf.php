<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../adminConfig.php'; // Virheiden käsittely.
session_start(); // Aloita sessio.

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Virheellinen pyyntö.");
}

$article_id = (int) $_POST['article_id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);

// Päivitetään blogin tiedot
$sql = "UPDATE ARTICLE SET title = ?, content = ?, updated_at = NOW() WHERE article_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $title, $content, $article_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<p>Julkaisun tiedot päivitettiin onnistuneesti!</p>";
} else {
    echo "<p>Ei muutoksia tehty.</p>";
}

$stmt->close();
$conn->close();
?>