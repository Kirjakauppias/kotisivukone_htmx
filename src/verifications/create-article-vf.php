<?php
session_start();
// create-article-vf.php
// Yhdistetään tietokantaan
require_once "../database/db_connect.php";
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


/*if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Virhe: Käyttäjätunnus puuttuu. Kirjaudu sisään uudelleen.");
}*/
// Tarkistetaan, että pyyntö on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan käyttäjä ID istunnosta (tai null, jos ei ole asetettu)
    $user_id = $_SESSION['user_id'] ?? null;
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

                // Haetaan kuvan MIME-tyyppi
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
                    default:
                        return false; // Ei tuettu formaatti
                }

                // Jos kuva on JPEG, tarkistetaan ja korjataan mahdollinen väärä orientaatio
                if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($source);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $image = imagerotate($image, 180, 0); // Käännä 180 astetta
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0); // Käännä 90 astetta vastapäivään
                                break;
                            case 8:
                                $image = imagerotate($image, 90, 0); // Käännä 90 astetta myötäpäivään
                                break;
                        }
                    }
                }

                // Skaalataan kuva max 1200px leveyteen säilyttäen mittasuhteet
                $width = imagesx($image);
                $height = imagesy($image);
                if($width > $maxWidth) {
                    $newWidth = (int) $maxWidth;
                    $newHeight = (int) (($maxWidth / $width) * $height);
                    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $resizedImage;
                }

                // Tallennetaan pakattu kuva tilapäiseen tiedostoon
                $compressedPath = tempnam(sys_get_temp_dir(), 'compressed_') . 'jpg';
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
                "unique_filename" => true
            ]);

            // Tallennetaan Cloudinaryn palauttama kuvaosoite
            $image_url = $upload['secure_url'];
            // Tallennetaan kuvaosoite muuttujaan jota käytetään tietokannassa
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

    // Jos käyttäjällä ei ole blogia, lopetetaan suoritus
    if ($result->num_rows === 0) {
        die("Käyttäjällä ei ole blogia");
    }

    // Haetaan blogin ID
    $row = $result->fetch_assoc();
    $blog_id = $row['blog_id'];

    // Lisätään uusi artikkeli tietokantaan
    $sql = "INSERT INTO ARTICLE (blog_id, title, content, image_path, status, published_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $blog_id, $title, $content, $image_path, $status);

    // Tarkistetaan, onnistuiko tietokantaan lisääminen
    if ($stmt->execute()) {
        echo "Artikkeli luotu onnistuneesti. <a href='index.php'>Palaa omalle sivulle</a>";
        
        // Lisää java-skripti joka sulkee modalin 3 sekunnin kuluttua
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 3000); // Sulkee modalin 3 sekunnin kuluttua
        </script>";
    } else {
        // Jos tietokantaan lisääminen epäonnistui, palautetaan JSON-virheilmoitus
        echo json_encode(['status' => 'error', 'message' => 'Artikkelin luonti epäonnistui.']);
    }

    // Suljetaan tietokantayhteys
    $stmt->close();
    $conn->close();
}
?>