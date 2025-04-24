<?php
require_once './database/db_connect.php';

$offset = (int) ($_GET['offset'] ?? 0);

$stmt = $conn->prepare("SELECT blog_id, name, slug, description
                        FROM BLOG 
                        WHERE deleted_at IS NULL 
                        ORDER BY created_at DESC 
                        LIMIT 3 OFFSET ?
");
$stmt->bind_param("i", $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $image = 'images/tp.png'; // Käytetään oletuskuvaa jos ei ole omaa
    echo '<div class="blog-card">';
    echo '<a href="blogit/' . htmlspecialchars($row['slug']) . '">';
    echo '<h1>' . htmlspecialchars($row['name']) . '</h1>';
    echo '<img src="images/tp.png">';
    echo '<div class="blog-description">' . htmlspecialchars($row['description']) . '</div>';
    echo '</a>';
    echo '</div>';
}
?>