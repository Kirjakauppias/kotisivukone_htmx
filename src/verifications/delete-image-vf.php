<?php
session_start();
require_once "../database/db_connect.php";

// delete-image.vf.php
// Tiedosto jonka avulla poistetaan valittu kuva palvelimelta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'] ?? null;

    if (!$article_id) {
        echo "Julkaisua ei löydy";
        exit();
    }

    // Haetaan kuvan polku tietokannasta
    $stmt = $conn->prepare("SELECT image_path FROM ARTICLE WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    if (!$image_path) {
        echo "Kuvaa ei löydy";
        exit();
    }

    // Poistetaan kuva palvelimelta
    if (file_exists("../" . $image_path)) {
        unlink("../" . $image_path);
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