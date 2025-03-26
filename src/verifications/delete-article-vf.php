<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
require_once "../database/db_connect.php";
require_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.

require __DIR__ . "./../vendor/autoload.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Dotenv\Dotenv;

// Ladataan .env vain jos olemassa (lokaalisti)
if(file_exists(dirname(__DIR__, 1) . '/.env')){
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

    if (!$article_id){
        echo json_encode(['status' => 'error', 'message' => 'Julkaisua ei löydy.']);
        exit();
    }

    // Haetaan mahdollinen kuva tietokannasta
    $stmt = $conn->prepare("SELECT image_path FROM ARTICLE WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // Poistetaan kuva Cloudinarysta, jos on olemassa
    if ($image_path) {
        $public_id = pathinfo(parse_url($image_path, PHP_URL_PATH), PATHINFO_FILENAME);
        try {
            (new UploadApi())->destroy("blog_images/" . $public_id);
        } catch (Exception $e) {
            error_log("Cloudinary-kuvan poisto epäonnistui: " . $e->getMessage());
        }
    }

    // Poistetaan julkaisu tietokannasta
    $stmt = $conn->prepare("UPDATE ARTICLE SET deleted_at = NOW() WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Julkaisu poistettu onnistuneesti.']);
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 3000); // Sulkee modalin 3 sekunnin kuluttua
        </script>";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Julkaisun poistaminen epäonnistui.']);
    }

    $stmt->close();
    $conn->close();
} else {
    // Jos ei ole POST -ohjataan takaisin etusivulle.
    header("Location: ../index.php");
    exit();
}

/*
    delete-article-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
        Aloitetaan sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
*/
?>