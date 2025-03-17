<?php
declare(strict_types=1);
session_start();
//delete-user-vf.php
//Tiedosto jossa suoritetaan käyttäjän tilin poisto
//deleteUser()
include_once '../database/db_add_data.php';
include_once "../database/db_enquiry.php";
include "../funcs.php";

// Ladataan Composer autoloader
require __DIR__ . "/../vendor/autoload.php";

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Dotenv\Dotenv;

// Ladataan .env vain lokaalisti
if (file_exists(dirname(__DIR__, 1) . '/.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();
}

// Konfiguroidaan Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key' => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => [
        'secure' => true
    ]
]);

// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;

// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
  header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
  exit();
}


// Poistetaan artikkelin kuva Cloudinarysta
function deleteArticleImageFromCloudinary($conn, $article_id) {
  $sql = "SELECT image_path FROM ARTICLE WHERE article_id = ?";
  $image_path = NULL;

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    if ($image_path) {
        $public_id = pathinfo(parse_url($image_path, PHP_URL_PATH), PATHINFO_FILENAME);
        try {
            (new UploadApi())->destroy("blog_images/" . $public_id);
        } catch (Exception $e) {
            error_log("Cloudinary-kuvan poisto epäonnistui: " . $e->getMessage());
        }
    }
  }
}

// Poistaa käyttäjän blogien artikkelit ja kuvat
function deleteUserBlogsAndArticles($conn, $user_id) {
  // Haetaan kaikki käyttäjän blogit
  $query = "SELECT blog_id FROM BLOG WHERE user_id = ?";
  if ($stmt = $conn->prepare($query)) {
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $blogs = $result->fetch_all(MYSQLI_ASSOC);
      $stmt->close();

      foreach ($blogs as $blog) {
          $blog_id = $blog['blog_id'];

          // Haetaan kaikki artikkelit blogille
          $queryArticles = "SELECT article_id FROM ARTICLE WHERE blog_id = ?";
          if ($stmt = $conn->prepare($queryArticles)) {
              $stmt->bind_param("i", $blog_id);
              $stmt->execute();
              $result = $stmt->get_result();
              $articles = $result->fetch_all(MYSQLI_ASSOC);
              $stmt->close();

              // Poistetaan jokaisen artikkelin kuva Cloudinarysta
              foreach ($articles as $article) {
                  deleteArticleImageFromCloudinary($conn, $article['article_id']);
              }
          }

          // Soft-delete blogin artikkelit
          $sql = "UPDATE ARTICLE SET deleted_at = NOW() WHERE blog_id = ?";
          if ($stmt = $conn->prepare($sql)) {
              $stmt->bind_param("i", $blog_id);
              $stmt->execute();
              $stmt->close();
          }
      }
  }

  // Soft-delete käyttäjän blogit
  $sql = "UPDATE BLOG SET deleted_at = NOW() WHERE user_id = ?";
  if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $stmt->close();
  }
}

// Tarkistetaan, että pyyntö on lähetetty POST -metodilla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteUser($conn, $user_id);
    session_destroy();
    header('HX-Location: logout.php');
    exit();
}


?>