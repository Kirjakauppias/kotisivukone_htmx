<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();

include_once "../database/db_add_data.php";
include_once "../database/db_enquiry.php";
include "../funcs.php";

// Ladataan Compser autoloader, jotta voidaan käyttää riippuvuuksia
require __DIR__ . "./../vendor/autoload.php";

$user_id = $_SESSION['user_id'] ?? null;
$blog_id = $_POST['blog_id'] ?? null;

if (!$user_id) {
    die("Virhe: Käyttäjä ei ole kirjautunut.");
}


// Käytetään Cloudinaryn ja Dotenv-kirjastoja
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Dotenv\Dotenv;

// Ladataan .env-tiedosto vain lokaalisti (jos se on olemassa)
if (file_exists(dirname(__DIR__, 1) . '/.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();
}

// Konfiguroidaan Cloudinary API-avaimilla ympäristömuuttujista
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key' => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => [
        'secure' => true
        ]
    ]);

function deleteArticleImageFromCloudinary($conn, $article_id) {
    // Haetaan artikkelin kuva
    $sql = "SELECT image_path FROM ARTICLE WHERE article_id = ?";
    $image_path = null;
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $stmt->bind_result($image_path);
        $stmt->fetch();
        $stmt->close();

        if ($image_path) {
            $public_id = pathinfo(parse_url($image_path, PHP_URL_PATH), PATHINFO_FILENAME);

            try {
                (new UploadApi())->destroy("blog_images/" . $public_id);
            } catch (Exception $e) {
                error_log("Cloudinary-kuvan poisto epäonnistui: " . $e->getMessage());
            }
        } else {
            return "Ei kuvaa poistettavaksi.";
        } 
    } else {
        return "Virhe tietokantakäsittelyssä.";
    }
}

// Funktio joka poistaa käyttäjän blogin julkaisut
function deleteBlogArticlesByBlogId($conn, $blog_id) {
    // Haetaan kaikki artikkelit blogille
    $query = "SELECT article_id FROM ARTICLE WHERE blog_id = ? AND deleted_at IS NULL";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $articles = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Poistetaan jokaisen artikkelin kuva Cloudinarysta
        foreach ($articles as $article) {
            deleteArticleImageFromCloudinary($conn, $article['article_id']);
        }
    }

    $sql = "UPDATE ARTICLE SET deleted_at = NOW() WHERE blog_id = ? AND deleted_at IS NULL";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $blog_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                return "Kaikki blogin julkaisut on merkitty poistetuksi.";
            } else {
                return "⚠ Päivitys onnistui, mutta yhtään riviä ei muutettu. Tarkista blog_id!";
            }
        } else {
            return "⛔ Virhe päivityksessä: " . $stmt->error;
        }
        $stmt->close();
    } else {
        return "⛔ Virhe tietokantakyselyssä.";
    }
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    echo deleteBlogArticlesByBlogId($conn, $blog_id);
    echo deleteBlog($conn, $user_id);

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST -pyyntö sallittu";
    exit();
}

/*
    delete-blog-vf.php algoritmi:
*/
?>