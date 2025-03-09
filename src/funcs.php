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

    // Haetaan kuvan tyyppi
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

    // Skaalataan kuva max 1200px leveyteen säilyttäen mittasuhteet
    $width = imagesx($image);
    $height = imagesy($image);
    if($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = ($maxWidth / $width) * $height;
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