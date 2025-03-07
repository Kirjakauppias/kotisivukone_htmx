<?php
session_start();

// create-article-vf.php
require_once "../database/db_connect.php";

/*if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Virhe: Käyttäjätunnus puuttuu. Kirjaudu sisään uudelleen.");
}*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $title = htmlspecialchars(trim($_POST['article_title'] ?? ''));
    $content = htmlspecialchars(trim($_POST['article_content'] ?? ''));
    $status = 'PUBLISHED';
    // 6.3. Alustetaan kuvan osoite
    $image_path = null; 

    // Tarkistetaan pakolliset kentät
    if(!$user_id || empty($title) || empty($content)) {
        die("Otsikko ja sisältö ovat pakollisia kenttiä.");
    }

    // 6.3. Käsitellään kuvan lataus
    if(!empty($_FILES['article_image']['name'])) {
        $upload_dir = "../uploads/";
        $filename = time() . "_" . basename($_FILES["article_image"]["name"]);
        $target_file = $upload_dir . $filename;

        // 6.3. Tarkistetaan tiedostomuoto
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if(!in_array($_FILES['article_image']['type'], $allowed_types)) {
            echo "<p class='error'>Vain JPG, PNG ja GIF tiedostot sallittu.</p>";
            exit();
        }

        // 6.3. Tallennetaan kuva palvelimelle
        if (move_uploaded_file($_FILES["article_image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/" . $filename; // Tallennetaan suhteellinen polku
        } else {
            echo "<p class='error'>Virhe kuvan lataamisessa.</p>";
            exit();
        }
    }


    // Haetaan käyttäjän blogin ID
    $sql = "SELECT blog_id FROM BLOG WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Käyttäjällä ei ole blogia");
    }

    $row = $result->fetch_assoc();
    $blog_id = $row['blog_id'];

    // Lisätään uusi artikkeli tietokantaan
    $sql = "INSERT INTO ARTICLE (blog_id, title, content, image_path, status, published_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $blog_id, $title, $content, $image_path, $status);

    if ($stmt->execute()) {
        echo "Artikkeli luotu onnistuneesti. <a href='index.php'>Palaa omalle sivulle</a>";
        // Lisää skripti modalin automaattiseen sulkemiseen
        echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 3000); // Sulkee modalin 3 sekunnin kuluttua
        </script>";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Artikkelin luonti epäonnistui.']);
    }

    $stmt->close();
    $conn->close();
}
?>