<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely

session_start(); // Aloitetaan sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
require_once "../database/db_connect.php";
require_once '../database/db_enquiry.php';
require '../funcs.php';

requireLoginModals($conn); // Jos käyttäjä ei ole kirjautunut, ohjataan ../index.php.



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  

    $blog_name = trim($_POST['blog_name']);
    $blog_description = trim($_POST['blog_description'] ?? '');

    if (empty($blog_name)) {
        die("Blogin nimi on pakollinen.");
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
     // Jos ei ole POST -ohjataan takaisin etusivulle.
     header("Location: ../index.php");
     exit();
}

/*
    create-blog-vf.php algoritmi:

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
        Aloitetaan sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan ../index.php.
*/
?>