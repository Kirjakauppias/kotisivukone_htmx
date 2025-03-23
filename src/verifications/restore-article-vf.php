<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();
require_once "../database/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'] ?? null;

    if (!$article_id) {
        echo "<p class='error'>Virhe: Artikkelia ei löytynyt.</p>";
        exit();
    }

    // Poistetaan deleted_at-arvo (palautetaan näkyväksi)
    $stmt = $conn->prepare("UPDATE ARTICLE SET deleted_at = NULL WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);

    if ($stmt->execute()) {
        echo "<p class='success'>Artikkeli palautettu onnistuneesti!</p>";
    } else {
        echo "<p class='error'>Virhe artikkelin palauttamisessa.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST-pyynnöt sallittu.";
    exit();
}

/*
    restore-article-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
*/
?>
