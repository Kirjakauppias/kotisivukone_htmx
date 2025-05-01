<?php
//fetch_blogs.php
require_once './database/db_connect.php';

$offset = (int) ($_GET['offset'] ?? 0);

$stmt = $conn->prepare("SELECT blog_id, name, slug, description
                        FROM BLOG 
                        WHERE deleted_at IS NULL 
                        ORDER BY created_at DESC 
                        LIMIT 6 OFFSET ?
");
$stmt->bind_param("i", $offset);
$stmt->execute();
$result = $stmt->get_result();

$blogsHtml = '';
while ($row = $result->fetch_assoc()) {
    $image = 'images/tp.png'; // Oletuskuva
    $blogsHtml .= '<div class="blog-card">';
    $blogsHtml .= '<a href="blogit/' . htmlspecialchars($row['slug']) . '">';
    $blogsHtml .= '<h1>' . htmlspecialchars($row['name']) . '</h1>';
    $blogsHtml .= '<p>' . htmlspecialchars($row['description']) . '</p>';
    $blogsHtml .= '</a></div>';
}

/*while ($row = $result->fetch_assoc()) {
    $image = 'images/tp.png'; // Käytetään oletuskuvaa jos ei ole omaa
    echo '<div class="blog-card">';
    echo '<a href="blogit/' . htmlspecialchars($row['slug']) . '">';
    echo '<h1>' . htmlspecialchars($row['name']) . '</h1>';
    echo '<img src="images/tp.png">';
    echo '<div class="blog-description">' . htmlspecialchars($row['description']) . '</div>';
    echo '</a>';
    echo '</div>';

}
*/

// Tarkistetaan, onko vielä lisää blogeja
$stmt2 = $conn->prepare("SELECT COUNT(*) FROM BLOG WHERE deleted_at IS NULL");
$stmt2->execute();
$stmt2->bind_result($totalCount);
$stmt2->fetch();
$stmt2->close();

$hasMore = ($offset + 6) < $totalCount;

echo json_encode([
    'html' => $blogsHtml,
    'hasMore' => $hasMore
]);
?>