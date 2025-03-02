<?php
session_start();
// edit-article-vf.php
// tiedosto jossa tallennetaan tietokantaan käyttäjän päivittämät julkaisut

require_once "../database/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'] ?? null;
    $title = htmlspecialchars(trim($_POST['article_title'] ?? ''));
    $content = htmlspecialchars(trim($_POST['article_content'] ?? ''));

    if (!$article_id || empty($title) || empty($content)) {
        echo "<p class='error'>Virhe: Kaikki kentät ovat pakollisia!</p>";
        exit();
    }

    $stmt = $conn->prepare("UPDATE ARTICLE SET title = ?, content = ?, updated_at = NOW() WHERE article_id = ?");
    $stmt->bind_param("ssi", $title, $content, $article_id);

    if ($stmt->execute()) {
        echo "<p class='success'>Artikkeli päivitetty onnistuneesti!</p>";
        // Lisää skripti modalin automaattiseen sulkemiseen
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 3000); // Sulkee modalin 3 sekunnin kuluttua
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
?>