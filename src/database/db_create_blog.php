<?php
require_once 'db_connect.php'; // Otetaan tietokantayhteys käyttöön

$sql = "
CREATE TABLE IF NOT EXISTS `BLOG` (
  `blog_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `published` TINYINT(1) NOT NULL DEFAULT 0, -- 0 = luonnos, 1 = julkaistu
  `visibility` ENUM('PUBLIC', 'PRIVATE') NOT NULL DEFAULT 'PUBLIC',
  `views` INT NOT NULL DEFAULT 0, -- Blogisivun katselukerrat
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  `layout_id` INT DEFAULT NULL,
  `style_id` INT DEFAULT NULL,
  PRIMARY KEY (`blog_id`),
  FOREIGN KEY (`user_id`) REFERENCES `USER`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`layout_id`) REFERENCES `LAYOUTS`(`layout_id`) ON DELETE SET NULL,
  FOREIGN KEY (`style_id`) REFERENCES `STYLES`(`style_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
";

try {
    $conn->query($sql);
    echo "✅ BLOG-taulu luotu Railway.comissa!";
} catch (Exception $e) {
    error_log("❌ Virhe taulun luonnissa: " . $e->getMessage());
    die("❌ Virhe taulun luonnissa: " . $e->getMessage());
}

$conn->close();
?>