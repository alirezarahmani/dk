CREATE DATABASE IF NOT EXISTS `lalamove`;
USE `lalamove`;

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` char (36) NOT NULL ,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distance` int(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;