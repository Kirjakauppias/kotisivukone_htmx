<?php
require_once '../db_connect.php'; // Muokkaa polkua tarpeen mukaan

$sql = "CREATE TABLE IF NOT EXISTS `ARTICLE` (
    `article_id` INT NOT NULL AUTO_INCREMENT,
    `blog_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `status` ENUM('DRAFT', 'PUBLISHED') DEFAULT 'DRAFT',
    `published_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`article_id`),
    FOREIGN KEY (`blog_id`) REFERENCES `BLOG`(`blog_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

if ($conn->query($sql) === TRUE) {
    echo "✅ ARTICLE-taulu luotu onnistuneesti!";
} else {
    echo "❌ Virhe taulun luonnissa: " . $conn->error;
}

$conn->close();
?>
