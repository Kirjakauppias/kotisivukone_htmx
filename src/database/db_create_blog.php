<?php
require_once 'db_connect.php'; // Otetaan tietokantayhteys käyttöön

// SQL-kysely taulun luomiseen
$sql = "
CREATE TABLE IF NOT EXISTS `BLOG` (
  `blog_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `description` text,
  `published` TINYINT(1) NOT NULL DEFAULT 0, -- 0 = luonnos, 1 = julkaistu
  `visibility` ENUM('PUBLIC', 'PRIVATE') NOT NULL DEFAULT 'PUBLIC',
  `views` INT NOT NULL DEFAULT 0, -- Blogisivun katselukerrat
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `layout_id` int DEFAULT NULL,
  `style_id` int DEFAULT NULL,
  PRIMARY KEY (`blog_id`),
  FOREIGN KEY (`user_id`) REFERENCES `USER`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`layout_id`) REFERENCES `LAYOUTS`(`layout_id`) ON DELETE SET NULL,
  FOREIGN KEY (`style_id`) REFERENCES `STYLES`(`style_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
";

// Suoritetaan SQL-kysely
try {
    $conn->query($sql);
    echo "✅ BLOG-taulu luotu Railway.comissa!";
} catch (Exception $e) {
    error_log("❌ Virhe taulun luonnissa: " . $e->getMessage());
    die("❌ Virhe taulun luonnissa: " . $e->getMessage());
}

$conn->close();
?>
