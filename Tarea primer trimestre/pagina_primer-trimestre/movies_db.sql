-- movies_db.sql
-- Base de datos para catálogo de películas y pedidos
-- Diseñado para MySQL / MariaDB

DROP DATABASE IF EXISTS `movies_db`;
CREATE DATABASE `movies_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `movies_db`;

-- Tabla de películas
CREATE TABLE `movies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(100) DEFAULT NULL,
  `year` SMALLINT UNSIGNED DEFAULT NULL,
  `price` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de usuarios (para inicio de sesión)
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nota: para crear usuarios con contraseña segura usa el script PHP `create_user.php` incluido en esta carpeta.

-- Tabla de pedidos (orders)
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `movie_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `unit_price` DECIMAL(8,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `customer_name` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`movie_id`),
  CONSTRAINT `fk_orders_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT INTO `movies` (`title`, `genre`, `year`, `price`, `stock`) VALUES
('Inception', 'Sci-Fi', 2010, 9.99, 10),
('The Godfather', 'Crime', 1972, 7.50, 5),
('Parasite', 'Thriller', 2019, 8.00, 8),
('Back to the Future', 'Adventure', 1985, 6.99, 12),
('The Matrix', 'Sci-Fi', 1999, 7.99, 7);

-- Procedimiento para realizar un pedido de forma transaccional
-- Uso (ejemplo): CALL place_order(1, 2, 'Juan Perez', @success, @msg); SELECT @success, @msg;
DELIMITER $$
DROP PROCEDURE IF EXISTS `place_order`$$
CREATE PROCEDURE `place_order`(
  IN p_movie_id INT,
  IN p_quantity INT,
  IN p_customer_name VARCHAR(255),
  OUT p_success TINYINT,
  OUT p_msg TEXT
)
BEGIN
  DECLARE v_stock INT;
  DECLARE v_price DECIMAL(8,2);

  -- Bloque con etiqueta para permitir LEAVE desde cualquier punto del bloque
  proc_block: BEGIN
    START TRANSACTION;

    -- Bloqueamos la fila para evitar condiciones de carrera
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

    -- Restar stock
    UPDATE `movies` SET `stock` = `stock` - p_quantity WHERE `id` = p_movie_id;

    -- Insertar pedido
    INSERT INTO `orders` (`movie_id`, `quantity`, `unit_price`, `total_price`, `customer_name`)
    VALUES (p_movie_id, p_quantity, v_price, v_price * p_quantity, p_customer_name);

    COMMIT;
    SET p_success = 1;
    SET p_msg = CONCAT('Pedido realizado. ', p_quantity, ' unidad(es) comprada(s). Total: ', FORMAT(v_price * p_quantity, 2));
  END proc_block;
END$$
DELIMITER ;

-- Vistas útiles (opcional): historial de compras por cliente
CREATE VIEW `purchases_view` AS
SELECT o.id AS order_id, o.movie_id, m.title, o.quantity, o.unit_price, o.total_price, o.customer_name, o.created_at
FROM orders o
JOIN movies m ON m.id = o.movie_id
ORDER BY o.created_at DESC;

-- Ejemplos de uso directo (sin procedimiento) para pruebas
-- 1) Ver películas:
-- SELECT * FROM movies;
-- 2) Hacer un pedido manual (en una transacción):
-- START TRANSACTION;
-- SELECT stock FROM movies WHERE id = 1 FOR UPDATE;
-- UPDATE movies SET stock = stock - 2 WHERE id = 1;
-- INSERT INTO orders (movie_id, quantity, unit_price, total_price, customer_name) VALUES (1, 2, 9.99, 19.98, 'Prueba');
-- COMMIT;

-- Fin del script
