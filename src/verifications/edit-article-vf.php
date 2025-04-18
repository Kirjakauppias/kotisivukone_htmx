<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

//session_start();
// edit-article-vf.php
// tiedosto jossa tallennetaan tietokantaan käyttäjän päivittämät julkaisut
require __DIR__ . "./../vendor/autoload.php";
require_once "../database/db_connect.php";
require_once "../funcs.php";
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
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
    $title = htmlspecialchars(trim($_POST['article_title'] ?? ''));
    $content = htmlspecialchars(trim($_POST['article_content'] ?? ''));

    if (!$article_id || empty($title) || empty($content)) {
        echo "<p class='error'>Virhe: Kaikki kentät ovat pakollisia!</p>";
        exit();
    }

    // 6.3. Haetaan vanha kuva
    $stmt = $conn->prepare("SELECT image_path FROM ARTICLE WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $stmt->bind_result($old_image);
    $stmt->fetch();
    $stmt->close();

    // 8.3. Tarkistetaan, onko uusi kuva ladattu
    if(!empty($_FILES['article_image']['tmp_name'])) {

        $file_type = mime_content_type($_FILES['article_image']['tmp_name']);

        $compressedImage = compressImage($_FILES['article_image']['tmp_name'], tempnam(sys_get_temp_dir(), 'compressed_'));

        if (!$compressedImage) {
            die("Virhe: Kuvan käsittely epäonnistui.");
        }
        

        // 8.3. Ladataan uusi kuva Cloudinaryyn
        try{
            $upload = (new UploadApi())->upload($compressedImage, [
                "folder" => "blog_images",
                "use_filename" => true,
                "unique_filename" => true,
                "quality" => "auto", // Automaattinen optimointi
                "format" => "jpg", // Muuntaa HEIC-kuvat automaattisesti JPEG-muotoon
                "overwrite" => true,
                "angle" => "exif",
                "keep_iptc" => true
            ]);
            $image_url = $upload['secure_url'];
            $image_path = $image_url;

            // Poistetaan pakattu väliaikaistiedosto
            unlink($compressedImage);

            // 8.3. Poistetaan vanha kuva Cloudinarysta, jos se on olemassa
            if ($old_image) {
              // 9.3. Haetaan vanhan kuvan public_id Cloudinarysta
              $public_id = pathinfo(parse_url($old_image, PHP_URL_PATH), PATHINFO_FILENAME);

              try {
                (new UploadApi())->destroy("blog_images/" . $public_id);
              } catch (Exception $e) {
                error_log("Cloudinary-kuvan poisto epäonnistui: " . $e->getMessage());
              }
            }
        } catch (Exception $e) {
            die("<p class='error'>Kuvan lataaminen epäonnistui: " . $e->getMessage() . "</p>");
            exit();
        }
    }

    // Päivitetään artikkelin tiedot tietokantaan
    if (isset($image_path)) {
        $stmt = $conn->prepare("UPDATE ARTICLE SET title = ?, content = ?, image_path = ?, updated_at = NOW() WHERE article_id = ?");
        $stmt->bind_param("sssi", $title, $content, $image_path, $article_id);
    } else {
        $stmt = $conn->prepare("UPDATE ARTICLE SET title = ?, content = ?, updated_at = NOW() WHERE article_id = ?");
        $stmt->bind_param("ssi", $title, $content, $article_id);
    }

    if ($stmt->execute()) {
        echo "<p class='success'>Artikkeli päivitetty onnistuneesti!</p>";
        // Lisää skripti modalin automaattiseen sulkemiseen
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 2000); // Sulkee modalin 2 sekunnin kuluttua
        </script>";
    } else {
        echo "<p class='error'>Virhe artikkelin päivittämisessä.</p>";
    }
    $stmt->close();
    $conn->close();

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST-pyynnöt sallittu.";
    exit();
}

/*
    edit-article-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
*/
?>