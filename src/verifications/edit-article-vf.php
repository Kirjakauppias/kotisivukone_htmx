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

    // 6.3. Tarkistetaan, onko uusi kuva ladattu
    if(!empty($_FILES['article_image']['name'])) {
        $upload_dir = "../uploads/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['article_image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid("img_") . "." . $file_ext;

        if (!in_array($file_ext, $allowed_types)) {
            echo "<p class='error'>Väärä tiedostotyyppi! Vain JPG, JPEG, PNG ja GIF sallittu.</p>";
            exit();
        }

        // 6.3. Haetaan vanha kuva
        $stmt = $conn->prepare("SELECT image_path FROM ARTICLE WHERE article_id = ?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $stmt->bind_result($old_image);
        $stmt->fetch();
        $stmt->close();

        // 6.3 Poistetaan vanha kuva, jos se on olemassa
        if ($old_image && file_exists("../" . $old_image)) {
            unlink("../" . $old_image);
        }

        // 6.3 Tallennetaan uusi kuva
        if (move_uploaded_file($_FILES['article_image']['tmp_name'], $upload_dir . $new_filename)) {
            $image_path = "uploads/" . $new_filename;
        } else {
            echo "<p class='error'>Kuvan lataaminen epäonnistui!</p>";
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