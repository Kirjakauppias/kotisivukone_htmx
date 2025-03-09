<?php
session_start();
require_once "../database/db_connect.php";
require __DIR__ . "./../vendor/autoload.php";

// delete-image.vf.php
// Tiedosto jonka avulla poistetaan valittu kuva palvelimelta

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Dotenv\Dotenv;

// Ladataan .env-tiedosto vain lokaalisti (jos se on olemassa)
if (file_exists(dirname(__DIR__, 1) . '/.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();
}

// Cloudinary-konfiguraatio
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key' => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => ['secure' => true]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'] ?? null;

    // Haetaan kuvan polku tietokannasta
    $stmt = $conn->prepare("SELECT image_path FROM ARTICLE WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    if (!$article_id) {
        echo "Julkaisua ei löydy";
        exit();
    }

    if (!$image_path) {
        echo "Kuvaa ei löydy";
        exit();
    }

    // 9.3. Poistetaan kuva Cloudinarysta
    $public_id = pathinfo(parse_url($image_path, PHP_URL_PATH), PATHINFO_FILENAME);

    try {
        (new UploadApi())->destroy("blog_images/" . $public_id);
    } catch (Exception $e) {
        error_log("Cloudinary-kuvan poisto epäonnistui: " . $e->getMessage());
    }

    // Päivitetään tietokanta (asetetaan image_path NULL-arvoksi)
    $stmt = $conn->prepare("UPDATE ARTICLE SET image_path = NULL WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);

    if ($stmt->execute()) {
        echo "Kuva poistettu onnistuneesti.";
    } else {
        echo "Tietokantapäivitys epäonnistui";
    }

    $stmt->close();
    $conn->close();

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['status' => 'error', 'message' => 'Vain POST-pyynnöt sallittu.']);
    exit();
}
?>