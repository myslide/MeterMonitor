-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Aug 2017 um 11:48
-- Server-Version: 10.1.25-MariaDB
-- PHP-Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `consumption`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `epower`
--

CREATE TABLE IF NOT EXISTS `epower` (
  `captureDate` date NOT NULL,
  `submitDate` datetime NOT NULL,
  `value` int(11) NOT NULL,
  `AbsoluteValue` int(11) NOT NULL,
  `note` varchar(255) COLLATE utf8_german2_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8453C9F7342E0551` (`submitDate`),
  UNIQUE KEY `UNIQ_8453C9F75B39DB79` (`AbsoluteValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;


--
-- Metadaten
--
USE `phpmyadmin`;

--
-- Metadaten für Tabelle epower
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
