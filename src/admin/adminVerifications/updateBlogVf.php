<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../adminConfig.php'; // Virheiden käsittely.
session_start(); // Aloita sessio.

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Virheellinen pyyntö.");
}

$blog_id = (int) $_POST['blog_id'];
$name = trim($_POST['name']);
$description = trim($_POST['description']);
$deleted_at = empty($_POST['deleted_at']) ? NULL : $_POST['deleted_at'];

// Haetaan nykyinen blogin nimi ja slug tietokannasta
$stmt = $conn->prepare("SELECT name, slug FROM BLOG WHERE blog_id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$stmt->bind_result($current_name, $current_slug);
$stmt->fetch();
$stmt->close();

// Jos nimi ei ole muuttunut, käytä olemassa olevaa slugia
if ($current_name === $name) {
    $slug = $current_slug;
} else {
        // Luodaan uusi slugi vain, jos nimi muuttuu
        // Luodaan slug blogin nimestä
        $slug = strtolower($name);
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
    }

// Päivitetään blogin tiedot
$sql = "UPDATE BLOG SET name = ?, slug = ?, description = ?, deleted_at = ?, updated_at = NOW() WHERE blog_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $slug, $description, $deleted_at, $blog_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<p>Blogin tiedot päivitettiin onnistuneesti!</p>";
} else {
    echo "<p>Ei muutoksia tehty.</p>";
}

$stmt->close();
$conn->close();
?>
