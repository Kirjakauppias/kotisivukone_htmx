<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

include_once "../database/db_connect.php";  // Yhteys tietokantaan

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];  // Saadaan slugi URL-parametrista

    // SQL-haku blogipostauksen hakemiseksi slugin perusteella
    $sql = "SELECT b.name, b.slug, b.description, l.html_php_code
            FROM BLOG b
            LEFT JOIN LAYOUTS l ON b.layout_id = l.layout_id
            WHERE b.slug = ? AND b.deleted_at IS NULL";  // Varmistetaan, ettei blogipostaus ole poistettu
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $slug);  // Bindataan slugi valmisteltuun SQL-lauseeseen
        $stmt->execute();
        $result = $stmt->get_result();

        // Tarkistetaan löytyikö blogipostaus
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();  // Haetaan rivit tietokannasta
            $blog_title = $row['name'];  // Blogin otsikko
            $blog_slug = $row['slug'];  // Slugi
            $blog_description = $row['description'];  // Blogin kuvaus
            $layout_html = $row['html_php_code'];  // Layoutin HTML/PHP-koodi

            // Haetaan blogin artikkelit
            // 6.3. Lisätty image_path
            // 12.3. Lisätty deleted_at
            $articles_html = "";
            $article_sql = "SELECT title, content, published_at, image_path, deleted_at 
                            FROM ARTICLE 
                            WHERE blog_id = (SELECT blog_id FROM BLOG WHERE slug = ? LIMIT 1) 
                            AND status = 'PUBLISHED'
                            AND deleted_at IS NULL 
                            ORDER BY published_at DESC";

            if ($article_stmt = $conn->prepare($article_sql)) {
                $article_stmt->bind_param("s", $slug);
                $article_stmt->execute();
                $article_result = $article_stmt->get_result();

                while ($article = $article_result->fetch_assoc()) {
                    $articles_html .= "<div class='article'>";
                        $articles_html .= "<h2>" . htmlspecialchars($article['title']) . "</h2>";
                        $articles_html .= "<p class='published'><small>Julkaistu: " . date("d.m.Y H:i", strtotime($article['published_at'])) . "</small></p>";

                        // 6.3. LISÄTTY: Kuva näkyviin, jos artikkelilla on sellainen
                        if (!empty($article['image_path'])) {
                            $articles_html .= "<img src='" . htmlspecialchars($article['image_path']) . "' alt='Artikkelin kuva' class='article-image'>";

                        }

                        $articles_html .= "<p class='article-content'>" . nl2br(htmlspecialchars($article['content'])) . "</p>";
                    $articles_html .= "</div>";
                }
                $article_stmt->close();
            }

            // Korvataan dynaamiset osat layoutista (esim. otsikko ja kuvaus)
            $layout_html = str_replace("{blog_title}", htmlspecialchars($blog_title), $layout_html);
            $layout_html = str_replace("{blog_description}", htmlspecialchars($blog_description), $layout_html);
            $layout_html = str_replace("{articles}", $articles_html, $layout_html);
        } else {
            // Jos slugin mukainen blogipostaus ei löydy
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 - Blogipostausta ei löytynyt</h1>";
            exit;
        }

        $stmt->close();

    }
} else {
    // Jos slugi ei ole määritelty URL:ssa
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Slugi ei ole määritelty</h1>";
    exit;
}

$conn->close();

// Tulostetaan generoidun layoutin HTML
echo $layout_html;

/*
    get_blog.php algoritmi
*/
?>