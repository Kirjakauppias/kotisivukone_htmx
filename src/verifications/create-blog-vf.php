<?php
declare(strict_types=1);
session_start();
require_once "../database/db_connect.php";
//verifications/create-blog-vf.php

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Virhe: Käyttäjätunnus puuttuu. Kirjaudu sisään uudelleen.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  

    $blog_name = trim($_POST['blog_name']);
    $blog_description = trim($_POST['blog_description'] ?? '');

    if (empty($blog_name)) {
        die("Blogin nimi on pakollinen.");
    }

    // Luodaan slug blogin nimestä
    $slug = strtolower($blog_name);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug); // Poistaa erikoismerkit ja korvaa välilyönnit viivalla
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

    // Lisätään blogi tietokantaan¨
    $stmt = $conn->prepare("INSERT INTO BLOG (user_id, name, slug, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user_id'], $blog_name, $slug, $blog_description);

    if ($stmt->execute()) {
        echo "Blogi luotu onnistuneesti! <a href='index.php'>Palaa omalle sivulle</a>.";
    } else {
        echo "Virhe blogin luonnissa: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Ei ole POST";
}
?>