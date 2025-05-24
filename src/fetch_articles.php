<?php
//fetch_blogs.php
require_once './database/db_connect.php';

$offset = (int) ($_GET['offset'] ?? 0);

$article = $conn->prepare("SELECT 
                            a.article_id, 
                            a.blog_id, 
                            a.title, 
                            a.content, 
                            a.image_path,
                            b.slug 
                        FROM ARTICLE a 
                        JOIN BLOG b ON a.blog_id = b.blog_id 
                        WHERE a.deleted_at IS NULL 
                        ORDER BY a.created_at DESC 
                        LIMIT 6 OFFSET ?
");

$article->bind_param("i", $offset);
$article->execute();
$articleResult = $article->get_result();

$articlesHtml = '';

while ($row = $articleResult->fetch_assoc()) {
    $articlesHtml .= '<div class="article-card">';
    $articlesHtml .= '<a class="article-link" href="blogit/' . htmlspecialchars($row['slug']) . '">';
    $articlesHtml .= '<div class="article-card-section">';
    $articlesHtml .= '<h2>' . htmlspecialchars($row['title']) . '</h2>';
    if($row['image_path'] === null) {
        $articlesHtml .= '<div class="article-img-tp-container">';
        $articlesHtml .= '<img class="article-img-tp" src="images/tp.png">';
        $articlesHtml .= '</div>';
    } else {
        $articlesHtml .= '<img class="article-img" src="' . ($row['image_path']) . '">';
    }
    $articlesHtml .= '</div>';
    $articlesHtml .= '</a></div>';
}

// Tarkistetaan, onko vielä lisää blogeja
$stmt2 = $conn->prepare("SELECT COUNT(*) FROM ARTICLE WHERE deleted_at IS NULL");
$stmt2->execute();
$stmt2->bind_result($totalCount);
$stmt2->fetch();
$stmt2->close();

$hasMore = ($offset + 6) < $totalCount;

echo json_encode([
    'html' => $articlesHtml,
    'hasMore' => $hasMore
]);
?>