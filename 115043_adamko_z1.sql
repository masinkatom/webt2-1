-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Št 22.Feb 2024, 13:36
-- Verzia serveru: 8.0.36-0ubuntu0.22.04.1
-- Verzia PHP: 8.3.2-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `nobel_prizes`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `candidates`
--

CREATE TABLE `candidates` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `surname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organization` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sex` varchar(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `birth` year NOT NULL,
  `death` year DEFAULT NULL,
  `country_id` int NOT NULL,
  `prize_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `countries`
--

CREATE TABLE `countries` (
  `id` int NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `prizes`
--

CREATE TABLE `prizes` (
  `id` int NOT NULL,
  `year` year NOT NULL,
  `contribution_sk` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contribution_en` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_id` int NOT NULL,
  `prize_detail_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `prize_details`
--

CREATE TABLE `prize_details` (
  `id` int NOT NULL,
  `genre_sk` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `genre_en` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `language_sk` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `language_en` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidates_ibfk_1` (`country_id`),
  ADD KEY `candidates_ibfk_2` (`prize_id`);

--
-- Indexy pre tabuľku `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `prizes`
--
ALTER TABLE `prizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prizes_ibfk_1` (`category_id`),
  ADD KEY `prizes_ibfk_2` (`prize_detail_id`);

--
-- Indexy pre tabuľku `prize_details`
--
ALTER TABLE `prize_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `prizes`
--
ALTER TABLE `prizes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `prize_details`
--
ALTER TABLE `prize_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `candidates_ibfk_2` FOREIGN KEY (`prize_id`) REFERENCES `prizes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Obmedzenie pre tabuľku `prizes`
--
ALTER TABLE `prizes`
  ADD CONSTRAINT `prizes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `prizes_ibfk_2` FOREIGN KEY (`prize_detail_id`) REFERENCES `prize_details` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
