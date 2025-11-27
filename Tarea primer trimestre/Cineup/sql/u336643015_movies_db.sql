-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-11-2025 a las 10:36:19
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u336643015_movies_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`u336643015_Marcos`@`127.0.0.1` PROCEDURE `place_order` (IN `p_movie_id` INT, IN `p_quantity` INT, IN `p_customer_name` VARCHAR(255), OUT `p_success` TINYINT, OUT `p_msg` TEXT)   BEGIN
  DECLARE v_stock INT;
  DECLARE v_price DECIMAL(8,2);

  proc_block: BEGIN
    START TRANSACTION;

    SELECT `stock`, `price` INTO v_stock, v_price
    FROM `movies`
    WHERE `id` = p_movie_id
    FOR UPDATE;

    IF v_stock IS NULL THEN
      SET p_success = 0;
      SET p_msg = CONCAT('Pelicula con id=', p_movie_id, ' no encontrada.');
      ROLLBACK;
      LEAVE proc_block;
    END IF;

    IF v_stock < p_quantity THEN
      SET p_success = 0;
      SET p_msg = CONCAT('Stock insuficiente. Disponible: ', v_stock);
      ROLLBACK;
      LEAVE proc_block;
    END IF;

    UPDATE `movies` SET `stock` = `stock` - p_quantity WHERE `id` = p_movie_id;

    INSERT INTO `orders` (`movie_id`, `quantity`, `unit_price`, `total_price`, `customer_name`)
    VALUES (p_movie_id, p_quantity, v_price, v_price * p_quantity, p_customer_name);

    COMMIT;
    SET p_success = 1;
    SET p_msg = CONCAT('Pedido realizado. ', p_quantity, ' unidad(es) comprada(s). Total: ', FORMAT(v_price * p_quantity, 2));
  END proc_block;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `name`, `created_at`) VALUES
(3333, 'marcos', '2025-11-17 07:56:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movies`
--

CREATE TABLE `movies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `year` smallint(5) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movies`
--

INSERT INTO `movies` (`id`, `title`, `genre`, `year`, `price`, `stock`, `created_at`) VALUES
(1, 'Inception', 'Sci-Fi', 2010, 9.98, 9, '2025-11-17 07:56:12'),
(4, 'Back to the Future', 'Adventure', 1985, 6.99, 10, '2025-11-17 07:56:12'),
(5, 'wicked', 'Adventure', 2024, 7.99, 1000, '2025-11-17 07:56:12'),
(6, 'alien', 'terror', 2025, 12.00, 994, '2025-11-17 07:58:12'),
(7, 'Predator', 'Sci-Fi', 2025, 11.50, 993, '2025-11-17 07:58:59'),
(8, 'Zootropolis2', 'Animada', 2025, 10.25, 996, '2025-11-17 08:00:00'),
(9, 'el padrino', 'Mafia', 2025, 7.45, 979, '2025-11-17 08:11:15'),
(10, 'wicked 2', 'Adventure', 2025, 11.20, 999, '2025-11-24 12:30:34'),
(11, 'avatar 3', 'Sci-Fi', 2025, 8.00, 991, '2025-11-24 12:36:17'),
(12, 'fnaf 2', 'Terror', 2025, 5.50, 1000, '2025-11-24 12:40:04'),
(13, 'dracula', 'Terror', 2025, 7.55, 999, '2025-11-24 12:44:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `movie_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `movie_id`, `quantity`, `unit_price`, `total_price`, `customer_name`, `created_at`) VALUES
(3, 4, 1, 6.99, 6.99, 'george666', '2025-11-17 08:00:58'),
(4, 9, 1, 7.45, 7.45, 'admin2', '2025-11-17 08:30:24'),
(6, 9, 2, 7.45, 14.90, 'admin2', '2025-11-18 08:28:32'),
(9, 9, 1, 7.45, 7.45, 'admin3', '2025-11-24 11:56:12'),
(10, 6, 1, 12.00, 12.00, 'admin3', '2025-11-24 12:18:36'),
(11, 8, 4, 10.25, 41.00, 'admin3', '2025-11-24 12:18:52'),
(12, 9, 4, 7.45, 29.80, 'admin3', '2025-11-24 12:23:47'),
(13, 10, 1, 11.20, 11.20, 'admin3', '2025-11-24 12:32:05'),
(14, 9, 1, 7.45, 7.45, 'admin3', '2025-11-24 12:33:15'),
(15, 13, 1, 7.55, 7.55, 'admin3', '2025-11-24 12:44:32'),
(17, 9, 1, 7.45, 7.45, 'admin2', '2025-11-25 08:28:52'),
(18, 9, 2, 7.45, 14.90, 'admin2', '2025-11-25 09:06:40'),
(19, 9, 1, 7.45, 7.45, 'admin3', '2025-11-25 12:07:55'),
(20, 6, 1, 12.00, 12.00, 'jose', '2025-11-27 09:42:12'),
(22, 11, 8, 8.00, 64.00, 'marcos', '2025-11-27 09:51:57');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `purchases_view`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `purchases_view` (
`order_id` int(10) unsigned
,`movie_id` int(10) unsigned
,`title` varchar(255)
,`quantity` int(10) unsigned
,`unit_price` decimal(8,2)
,`total_price` decimal(10,2)
,`customer_name` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `is_admin`) VALUES
(1, 'george666', '$2y$10$vwsvkJ632QryjikNLxl6K.JyeQUcw5TMXAdanwhmn/dbVpfamHQkO', '2025-11-17 07:56:56', 0),
(2, 'admin2', '$2y$10$c9hS.Fs.cf3FZKBtAUcH6eUahJofP87sob4/70tIRE0WYPjoTMs.O', '2025-11-17 08:02:29', 0),
(3, 'admin3', '$2y$10$.U2AMxMcsI7fjebOHsscYOkDClevqtd6BdQ3Px0f10PcRCjPrhley', '2025-11-18 08:38:01', 0),
(4, 'jose', '$2y$10$ZgzsS0cgkgvxeiBPIit7zODIin5Z7KcyM/sg3GxK/9LtX9CupC0Tu', '2025-11-27 09:41:54', 0),
(5, 'marcos', '$2y$10$0jPZ8y20PnAsmHmGcVmHKOqcO2t1GcZBAR.obCuVncy6RYmrNMmAO', '2025-11-27 09:50:32', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indices de la tabla `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- --------------------------------------------------------

--
-- Estructura para la vista `purchases_view`
--
DROP TABLE IF EXISTS `purchases_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u336643015_Marcos`@`127.0.0.1` SQL SECURITY DEFINER VIEW `purchases_view`  AS SELECT `o`.`id` AS `order_id`, `o`.`movie_id` AS `movie_id`, `m`.`title` AS `title`, `o`.`quantity` AS `quantity`, `o`.`unit_price` AS `unit_price`, `o`.`total_price` AS `total_price`, `o`.`customer_name` AS `customer_name`, `o`.`created_at` AS `created_at` FROM (`orders` `o` join `movies` `m` on(`m`.`id` = `o`.`movie_id`)) ORDER BY `o`.`created_at` DESC ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
