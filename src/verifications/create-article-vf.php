<?php
session_start();
// create-article-vf.php
require __DIR__ . "./../vendor/autoload.php";
require_once "../database/db_connect.php";

use Cloudinary\Configuration\Configuration;
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
    'url' => [
        'secure' => true
    ]
]);


/*if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Virhe: Käyttäjätunnus puuttuu. Kirjaudu sisään uudelleen.");
}*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $title = htmlspecialchars(trim($_POST['article_title'] ?? ''));
    $content = htmlspecialchars(trim($_POST['article_content'] ?? ''));
    $status = 'PUBLISHED';
    // 6.3. Alustetaan kuvan osoite
    $image_path = null; 

    // Tarkistetaan pakolliset kentät
    if(!$user_id || empty($title) || empty($content)) {
        die("Otsikko ja sisältö ovat pakollisia kenttiä.");
    }

    // 6.3. Käsitellään kuvan lataus Cloudinaryyn
    if(!empty($_FILES['article_image']['tmp_name'])) {
        try{
            $upload = (new UploadApi())->upload($_FILES['article_image']['tmp_name'], [
                "folder" => "blog_images",
                "use_filename" => true,
                "unique_filename" => true,
                "resource_type" => "image",
                "transformation" => [
                    ["quality" => "auto:low", "fetch_format" => "jpg", "width" => 1000, "height" => 1000, "crop" => "limit", "dpr" => 0.8]
                    ]
            ]);
            $image_url = $upload['secure_url'];
            $image_path = $image_url;
        } catch (Exception $e) {
            die("Kuvan lataaminen epäonnistui: " . $e->getMessage());
        }
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
    $sql = "INSERT INTO ARTICLE (blog_id, title, content, image_path, status, published_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $blog_id, $title, $content, $image_path, $status);

    if ($stmt->execute()) {
        echo "Artikkeli luotu onnistuneesti. <a href='index.php'>Palaa omalle sivulle</a>";
        // Lisää skripti modalin automaattiseen sulkemiseen
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 3000); // Sulkee modalin 3 sekunnin kuluttua
        </script>";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Artikkelin luonti epäonnistui.']);
    }

    $stmt->close();
    $conn->close();
}
?>