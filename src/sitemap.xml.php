<?php
declare(strict_types=1);
header('Content-Type: application/xml');

require_once './config.php';
require_once './database/db_connect.php';

$baseUrl = 'https://tarinanpaikka.up.railway.app';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Etusivu -->
    <url>
       <loc><?= $baseUrl ?>/</loc>
       <priority>1.0</priority>
       <changefreq>daily</changefreq>
    </url>
    <?php
    // Haetaan blogit
    $stmt = $conn->prepare("SELECT slug FROM BLOG WHERE deleted_at IS NULL");
    $stmt->execute();
    $blogs = $stmt->get_result();
    while ($blog = $blogs->fetch_assoc()):
    ?>
       <url>
           <loc><?= $baseUrl ?>/blogit/<?= htmlspecialchars($blog['slug']) ?></loc>
           <priority>0.8</priority>
           <changefreq>weekly</changefreq>
       </url>
    <?php endwhile; ?>

    <?php
    // Haetaan artikkelit
    $stmt = $conn->prepare("SELECT A.article_id, B.slug FROM ARTICLE A 
                            JOIN BLOG B ON A.blog_id = B.blog_id 
                            WHERE A.deleted_at IS NULL");
    $stmt->execute();
    $articles = $stmt->get_result();
    while ($article = $articles->fetch_assoc()):
    ?>
        <url>
            <loc><?= $baseUrl ?>/blogit/<?= htmlspecialchars($article['slug']) ?>/article.php?id=<?= $article['article_id'] ?></loc>
            <priority>0.6</priority>
            <changefreq>monthly</changefreq>
        </url>
        <?php endwhile; ?>
</urlset>