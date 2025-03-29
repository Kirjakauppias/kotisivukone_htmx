<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = $_POST['blog_id'] ?? null;

    if (is_null($blog_id)) {
        echo "<p>Virhe: Blogi-ID puuttuu.</p>";
        exit();
    }

    // Haetaan blogin tiedot tietokannasta
    $stmt = $conn->prepare("SELECT deleted_at, user_id FROM BLOG WHERE blog_id=?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>Virhe: Blogia ei löydy.</p>";
        exit();
    }

    $blog = $result->fetch_assoc();
    $stmt->close();

    // Tarkistetaan, onko blogi jo aktiivinen (deleted_at on NULL)
    if (is_null($blog['deleted_at'])) {
        echo "<p>Blogi on jo aktiivinen.</p>";
        exit();
    }

    // Tarkistetaan, onko käyttäjällä jo aktiivinen blogi
    $user_id = $blog['user_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS active_blogs FROM BLOG WHERE user_id=? AND deleted_at IS NULL");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $active_blog_count = $result->fetch_assoc()['active_blogs'];
    $stmt->close();

    if ($active_blog_count > 0) {
        echo "<p>Käyttäjällä on jo aktiivinen blogi. Vain yksi aktiivinen blogi sallitaan.</p>";
        exit();
    }

    // Päivitetään blogin tila (palautetaan näkyviin)
    $stmt = $conn->prepare("UPDATE BLOG SET deleted_at = NULL WHERE blog_id=?");
    $stmt->bind_param("i", $blog_id);

    if ($stmt->execute()) {
        // Päivitetään myös kaikki blogin artikkelit näkyviin
        $stmt = $conn->prepare("UPDATE ARTICLE SET deleted_at = NULL WHERE blog_id=?");
        $stmt->bind_param("i", $blog_id);
        
        if ($stmt->execute()) {
            echo "<p>Blogi ja sen artikkelit palautettu näkyviin!</p>";
        } else {
            echo "<p>Virhe artikkeleiden palauttamisessa näkyviin.</p>";
        }
    } else {
        echo "<p>Virhe blogin tilan päivittämisessä.</p>";
    }

    $stmt->close();
} else {
    http_response_code(403);
    exit("Pääsy estetty.");
}
?>
