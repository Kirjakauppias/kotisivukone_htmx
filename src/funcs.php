<?php
// Tiedosto jossa on kaikki käytetyt funktiot

// Funktio virheiden tulostamista varten
function display_errors($errors) {
    echo "<div class='response' role='alert'>";
    foreach ($errors as $error) {
        echo "<p class='error'>{$error}</p>";
    }
    echo "</div>";
}

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

function isUserLoggedIn() {
    $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            die("Virhe: Käyttäjä ei ole kirjautunut.");
        }
    }