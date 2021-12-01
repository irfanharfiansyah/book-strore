-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `book_name` varchar(25) NOT NULL,
  `book_publisher` varchar(20) DEFAULT NULL,
  `book_price` float DEFAULT NULL,
  `image_book` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `books` (`id`, `book_name`, `book_publisher`, `book_price`, `image_book`) VALUES
(1,	'The Intelegent Investor',	'Benjamin Graham',	350000,	'the-intelligent-investor-delhi-book-market-385497.jpg'),
(2,	'How to Win Friends',	'Dale Carnegie',	100000,	'31_0.jpg'),
(3,	'ikigai',	'francesc',	100000,	'ikigai.jpg');

-- 2021-12-01 06:22:59