<?php
// edit-blog-vf.php
// Tiedosto joka tarkistaa ja tekee päivityksen käyttäjän blogiin
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

    // Päivitetään blogin tiedot
    echo updateBlog($conn, $user_id, $blog_name, $blog_description);

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST -pyyntö sallittu";
    exit();
}


$conn->close();
?>