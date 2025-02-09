-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql_db
-- Generation Time: 09.02.2025 klo 11:07
-- Palvelimen versio: 8.3.0
-- PHP Version: 8.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webpage`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `USER`
--

CREATE TABLE `USER` (
  `user_id` int NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `role` enum('customer','admin') DEFAULT 'customer',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Vedos taulusta `USER`
--

INSERT INTO `USER` (`user_id`, `firstname`, `lastname`, `username`, `email`, `password`, `status`, `role`, `last_login`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Mikko', 'Lepistö', 'Leppari', 'metarktis@hotmail.com', '$2y$10$R9W/APGfOQSbB3slSNzpp.Un4/mK2OE1NL1LctnixezvI5YjwvbWO', 'active', 'customer', '2025-02-09 10:48:51', '2025-01-06 11:32:44', '2025-02-09 10:48:51', NULL),
(3, 'Testi', 'Testaaja', 'Testaaja', 'testaaja@hotmail.com', '$2y$10$KKgKbi2yM5p2q25e7ByT3OzKqUnQlBTTK1Pslh174qJkczlGJ1SX.', 'active', 'customer', '2025-01-07 13:45:25', '2025-01-07 13:42:11', '2025-01-07 13:45:25', NULL),
(4, 'Mikko', 'Lepistö', 'Leppari79', 'leppari@hotmail.com', '$2y$10$OBQv6cLUKxyOISarHNRnYuJKkot3fyALW4H75Wj7nO.y5vNjE.YGq', 'active', 'customer', NULL, '2025-01-07 13:59:44', '2025-01-07 13:59:44', NULL),
(5, 'Milla', 'Magia', 'Magia', 'magia@gmail.com', '$2y$10$75S.kuICWfXtnz5KHTSoI.lmcjhOvdo21Xh8xpmHu.ABhyYnbR3Hy', 'active', 'customer', '2025-01-07 14:02:56', '2025-01-07 14:02:38', '2025-01-07 14:02:56', NULL),
(6, 'Testaaja', 'Testi', 'Testi1', 'testi@testi.com', '$2y$10$5nujH2ex5atY9QtHjqv45O5W7qle7w6RWWOu94AU9W74qsZxtfRbG', 'active', 'customer', NULL, '2025-02-09 10:59:20', '2025-02-09 10:59:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `USER`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `USER`
--
ALTER TABLE `USER`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
