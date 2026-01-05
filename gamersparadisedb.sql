-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Set 10, 2023 alle 23:44
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamersparadisedb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `card_number` char(16) NOT NULL,
  `card_holder_name` varchar(300) NOT NULL,
  `card_holder_lastname` varchar(300) NOT NULL,
  `cvv` varchar(300) NOT NULL,
  `expiring_date` char(5) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cards`
--

INSERT INTO `cards` (`id`, `card_number`, `card_holder_name`, `card_holder_lastname`, `cvv`, `expiring_date`, `user_id`) VALUES
(1, '6998955625350369', 'Davide ', 'Robustelli', 'NDJWVm1jRU9NUnh4V3l0R3FySjVMZz09:DCU6BaeU9XeScnfD2RqHSw==', '05/30', 7),
(11, '1234567890123456', 'jsakldjjlksaajdlka', 'fdsfsdfr', 'QW1pMEp3cGluV2Q4b0NmdzJVTUtiUT09:REisHQXA6gLDnRcA6Bbcsg==', '12/25', 7);

-- --------------------------------------------------------

--
-- Struttura della tabella `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `platform` varchar(300) NOT NULL,
  `genre` varchar(300) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `cover` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `games`
--

INSERT INTO `games` (`id`, `title`, `platform`, `genre`, `price`, `description`, `cover`) VALUES
(7, 'Call of Duty: Black Ops 2', 'Playstation 4', 'Sparatutto', 39.99, 'gioco molto bello', 'Cod_Bo2.jpg'),
(11, 'Minecraft', 'Playstation 5', 'SandBox', 3242.00, 'dfs', 'Minecraft.jpg'),
(20, 'Grand Theft Auto:5', 'PC', 'Avventura', 39.99, 'vcxvxc', 'GTA_V.jpg'),
(21, 'Elden Ring', 'Playstation 5', 'Avventura', 22.50, 'bell', 'elden-ring-key-art-1271785.jpg');

-- --------------------------------------------------------

--
-- Struttura della tabella `genre`
--

CREATE TABLE `genre` (
  `genre` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `genre`
--

INSERT INTO `genre` (`genre`) VALUES
('Avventura'),
('Indie Horror'),
('Sandbox'),
('Sparatutto');

-- --------------------------------------------------------

--
-- Struttura della tabella `keys_`
--

CREATE TABLE `keys_` (
  `id` int(11) NOT NULL,
  `key_value` varchar(300) NOT NULL,
  `game_id` int(11) NOT NULL,
  `acquired` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `keys_`
--

INSERT INTO `keys_` (`id`, `key_value`, `game_id`, `acquired`) VALUES
(2, 'U3Y2eGI1bmtrcU41K0FwOEltR2svQT09:uQPAVTP6tqqJnhjNoLiWbQ==', 7, 1),
(3, 'ZnI2ZzJXTUVXMVl6cUM1NnY3TlBLdz09:SbsdxbnXuxyaJ6+Ow7pquA==', 7, 0),
(4, 'UG44Mm9ER2orYlIwTjJkSzdtYXdOQT09:vkrhLnZ/MOY8Za0AqY7F7A==', 11, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `platform`
--

CREATE TABLE `platform` (
  `platform` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `platform`
--

INSERT INTO `platform` (`platform`) VALUES
('Nintendo Switch'),
('PC'),
('Playstation 4'),
('Playstation 5'),
('Xbox one'),
('Xbox serie x');

-- --------------------------------------------------------

--
-- Struttura della tabella `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `purchases`
--

INSERT INTO `purchases` (`id`, `order_date`, `game_id`, `user_id`, `key_id`, `card_id`) VALUES
(33, '2023-09-10 21:43:41', 7, 7, 2, 1),
(34, '2023-09-10 21:43:41', 11, 7, 4, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `first_name` varchar(300) NOT NULL,
  `last_name` varchar(300) NOT NULL,
  `role` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `role`) VALUES
(1, 'luca@gmail.com', '$2y$10$ZcX5kSHOPJZR81zuiC5U/.GUN7XsjpX8PcluwnSkk7Sb0pygs8Aea', 'Luca', 'Robustelli', 1),
(7, 'davide@gmail.com', '$2y$10$6OI.q9p57GSRMeaZdI0X6u1PP0WkRebMXufpR01L5FpS8mAqlAZJC', 'Davide', 'Robustelli', 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_genre` (`genre`),
  ADD KEY `fk_platform` (`platform`);

--
-- Indici per le tabelle `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre`);

--
-- Indici per le tabelle `keys_`
--
ALTER TABLE `keys_`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_value` (`key_value`),
  ADD KEY `game_id` (`game_id`);

--
-- Indici per le tabelle `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`platform`);

--
-- Indici per le tabelle `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_key_id` (`key_id`),
  ADD KEY `fk_purchases_cards` (`card_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT per la tabella `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT per la tabella `keys_`
--
ALTER TABLE `keys_`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT per la tabella `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limiti per la tabella `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `fk_genre` FOREIGN KEY (`genre`) REFERENCES `genre` (`genre`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_platform` FOREIGN KEY (`platform`) REFERENCES `platform` (`platform`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `keys_`
--
ALTER TABLE `keys_`
  ADD CONSTRAINT `keys__ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_key_id` FOREIGN KEY (`key_id`) REFERENCES `keys_` (`id`),
  ADD CONSTRAINT `fk_purchases_cards` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`),
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
