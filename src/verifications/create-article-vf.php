<?php
session_start();

require_once "../database/db_connect.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Virhe: Käyttäjätunnus puuttuu. Kirjaudu sisään uudelleen.");
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['article_title'] ?? '');
$content = trim($_POST['article_content'] ?? '');
$status = 'PUBLISHED';

// Tarkistetaan pakolliset kentät
if(empty($title) || empty($content)) {
    die("Otsikko ja sisältö ovat pakollisia kenttiä.");
}

// Haetaan käyttäjän blogin ID
$sql = "SELECT blog_id FROM BLOG WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Käyttäjällä ei ole blogia");
}

$row = $result->fetch_assoc();
$blog_id = $row['blog_id'];

// Lisätään uusi artikkeli tietokantaan
$sql = "INSERT INTO ARTICLE (blog_id, title, content, status, published_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isss', $blog_id, $title, $content, $status);

if ($stmt->execute()) {
    echo "Artikkeli luotu onnistuneesti.";
} else {
    echo json_encode(['status' => 'error', 'message' => 'Artikkelin luonti epäonnistui.']);
}

$stmt->close();
$conn->close();
?>