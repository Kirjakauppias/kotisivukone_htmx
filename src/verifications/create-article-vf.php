<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start(); // Aloitetaan sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
require_once "../database/db_connect.php";
require_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.

// Ladataan Compser autoloader, jotta voidaan käyttää riippuvuuksia
require __DIR__ . "./../vendor/autoload.php";

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

// Tarkistetaan, että pyyntö on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan käyttäjä ID istunnosta (tai null, jos ei ole asetettu)
    $user_id = $_SESSION['user_id'] ?? null;
    // Tallennetaan muuttujaan lähetetty blog_id
    $blog_id = isset($_POST['blog_id']) ? intval($_POST['blog_id']) : null;
    // Haetaan ja puhdistetaan käyttäjän antamat tiedot
    $title = htmlspecialchars(trim($_POST['article_title'] ?? ''));
    $content = htmlspecialchars(trim($_POST['article_content'] ?? ''));
    // Artikkelin tila asetetaan oletuksena "PUBLISHED"
    $status = 'PUBLISHED';
    // 6.3. Alustetaan kuvan osoite
    $image_path = null; 

    // Tarkistetaan että käyttäjätunnus, otsikko, ja sisältö ovat asetettuina
    if(!$user_id || empty($title) || empty($content)) {
        die("Otsikko ja sisältö ovat pakollisia kenttiä.");
    }

    // 6.3. Käsitellään kuvan lataus Cloudinaryyn, jos käyttäjä on lisännyt kuvan
    if(!empty($_FILES['article_image']['tmp_name'])) {

        try{
            // Funktio kuvan pakkaamiseen ja skaalaukseen
            function compressImage($source, $destination, $quality = 75, $maxWidth = 1200) {
                $info = getimagesize($source);
                if ($info === false) {
                    return false; // Ei kelvollinen kuva
                }
            
                // Haetaan MIME-tyyppi
                $mime = $info['mime'];
            
                // Luodaan kuva oikeasta tiedostotyypistä
                switch ($mime) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($source);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($source);
                        break;
                    case 'image/webp':
                        $image = imagecreatefromwebp($source);
                        break;
                    case 'image/heic':
                    case 'image/heif':
                        return false; // HEIC/HEIF ei tueta suoraan PHP:ssä, vaatii erillisen muunnoksen
                    default:
                        return false; // Ei tuettu formaatti
                }
            
                // 🔄 **Korjataan orientaatio (vain JPEG-kuville)**
                if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($source);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 8:
                                $image = imagerotate($image, 90, 0);
                                break;
                        }
                    }
                }

                
                // 📏 **Skaalataan kuva max 1200px leveyteen säilyttäen mittasuhteet**
                $width = imagesx($image);
                $height = imagesy($image);
                if ($width > $maxWidth) {
                    $newWidth = (int) $maxWidth;
                    $newHeight = (int) (($maxWidth / $width) * $height);
                    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $resizedImage;
                }
                
                // 💾 **Tallennetaan pakattu kuva JPEG-muodossa**
                $compressedPath = tempnam(sys_get_temp_dir(), 'compressed_') . '.jpg';
                imagejpeg($image, $compressedPath, $quality);
                imagedestroy($image);
                
                return $compressedPath;
            }
            
            $compressedImage = compressImage($_FILES['article_image']['tmp_name'], tempnam(sys_get_temp_dir(), 'compressed_'));
            
            
            if (!$compressedImage) {
                die("Virhe: Kuvan käsittely epäonnistui.");
            }
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

            /* Kuvan tietojen debuggausta varten
            $exif = @exif_read_data($_FILES['article_image']['tmp_name']);
            echo "<div id='result'>";
            print_r($exif);
            echo "</div>";*/

            // Tallennetaan Cloudinaryn palauttama kuvaosoite
            $image_url = $upload['secure_url'];
            // Tallennetaan kuvaosoite muuttujaan jota käytetään tietokannassa
            $image_path = $image_url;
        } catch (Exception $e) {
            die("Kuvan lataaminen epäonnistui: " . $e->getMessage());
        }
    }

    if (!$blog_id) {
        echo json_encode(['status' => 'error', 'message' => 'Käyttäjällä ei ole blogia.']);
        exit;
    } 

    // Lisätään uusi artikkeli tietokantaan
    $sql = "INSERT INTO ARTICLE (blog_id, title, content, image_path, status, published_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $blog_id, $title, $content, $image_path, $status);

    // Tarkistetaan, onnistuiko tietokantaan lisääminen
    if ($stmt->execute()) {
        echo "Artikkeli luotu onnistuneesti. <a href='index.php'>Palaa omalle sivulle</a>";
        
        // Lisää java-skripti joka sulkee modalin 2 sekunnin kuluttua
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 2000); // Sulkee modalin 2 sekunnin kuluttua
        </script>";
    } else {
        // Jos tietokantaan lisääminen epäonnistui, palautetaan JSON-virheilmoitus
        echo json_encode(['status' => 'error', 'message' => 'Artikkelin luonti epäonnistui.']);
    }

    // Suljetaan tietokantayhteys
    $stmt->close();
    $conn->close();
    
} else {
    // Jos ei ole POST -ohjataan takaisin etusivulle.
    header("Location: ../index.php");
    exit();
}

/*
    create-article-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
        Aloitetaan sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
*/
?>