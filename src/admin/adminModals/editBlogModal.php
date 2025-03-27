<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

$blog_id = (int) $_GET['blog_id'] ?? null;

// Tarkistetaan, että blog_id on annettu
if (!isset($_GET['blog_id']) || empty($_GET['blog_id'])) {
    die("Virhe: Blogi-ID puuttuu.");
}

// Haetaan blogin tiedot
$sql = "SELECT b.blog_id, b.user_id, u.username, b.name, b.slug, b.description, b.deleted_at 
        FROM BLOG b 
        JOIN USER u ON b.user_id = u.user_id 
        WHERE blog_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Virhe: Blogia ei löytynyt.";
    exit();
}

$blog = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<div id="edit-blog-modal">
    <h2>Muokkaa blogia: <?= htmlspecialchars($blog['name']) ?></h2>

    <form id="edit-blog-form" hx-post="adminVerifications/updateBlogVf.php" hx-target="#update-blog-response" hx-swap="outerHTML">

        <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
        
        <label>Nimi</label>
        <input type="text" name="name" value="<?= htmlspecialchars($blog['name']) ?>" required>
        
        <label>Kuvaus</label>
        <textarea name="description" required><?= htmlspecialchars($blog['description']) ?></textarea>
        
        <button type="submit">Päivitä</button>
    </form>

    <h3>Blogin hallinta</h3>
    <form id="toggle-blog-form" hx-post="adminVerifications/toggleBlogStatusVf.php" hx-target="#toggle-blog-response" hx-swap="innerHTML">
        <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
        <button type="submit"><?= $blog['deleted_at'] ? 'Palauta blogi' : 'Poista blogi' ?></button>
    </form>

    <div id="update-blog-response"></div>
    <div id="toggle-blog-response"></div>
</div>
