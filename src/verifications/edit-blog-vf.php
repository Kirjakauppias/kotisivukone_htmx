<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start();
// updateBlog();
include_once "../database/db_add_data.php";


function isUserLoggedIn() {
$user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        die("Virhe: Käyttäjä ei ole kirjautunut.");
    }
}

// Tarkistetaan, että käyttäjä on kirjautunut sisään
isUserLoggedIn();

// Tarkistetaan lomakkeesta saatavat tiedot
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_name = trim($_POST['blogname'] ?? '');
    $blog_description = trim($_POST['blog_description'] ?? '');
    $user_id = $_SESSION['user_id'];

    if (empty($blog_name)) {
        die("Virhe: Blogin nimi ei voi olla tyhjä");
    }

    // Luodaan slug blogin nimestä
    $slug = strtolower($blog_name);
    $slug = preg_replace('/[^a-ö0-9]+/', '-', $slug); // Poistaa erikoismerkit ja korvaa välilyönnit viivalla
    $slug = trim($slug, '-');

    // Varmistetaan että slug on uniikki
    $stmt = $conn->prepare("SELECT COUNT(*) FROM BLOG WHERE SLUG = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $slug .= '-' . rand(1000, 9999); // Lisätään satunnainen numero, jos slug on jo käytössä
    }

     echo updateBlog($conn, $user_id, $blog_name, $blog_description, $slug);
     echo "<script>
        setTimeout(() => {
            document.getElementById('modal-container').innerHTML = '';
        }, 2000); // Sulkee modalin 2 sekunnin kuluttua
        </script>";

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST -pyyntö sallittu";
    exit();
}


$conn->close();

/*
    edit-blog-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
*/
?>