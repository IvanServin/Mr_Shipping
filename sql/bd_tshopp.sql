-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-12-2024 a las 05:16:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_tshopp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` int(11) NOT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `dir_img` varchar(500) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio`, `estado`, `descripcion`, `dir_img`, `id_usuario`) VALUES
(1, 'remera negra ', 32000, 'Nuevo', 'Remera básica color negra ', 'remera negra.jpg', 1),
(5, 'gorra', 4000, 'nuevo', 'gorra de lana ', NULL, 1),
(6, 'pantalon', 55000, 'nuevo', 'Pantalón elegante marca AGNE!!', '', 1),
(7, 'pantalones', 60000, 'nuevo', 'Pantalón elegante marca AGNE!!', '1734147415_0_18-c682125f85ccc9cb9e17192190831905-1024-1024.png,1734147415_1_images.jpg,1734147415_2_00027702550090_3-150x150.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_usuarios`
--

CREATE TABLE `productos_usuarios` (
  `id_producto` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_usuarios`
--

INSERT INTO `productos_usuarios` (`id_producto`, `id_usuario`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `dir_img_perfil` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `correo`, `nombre`, `apellido`, `edad`, `contrasena`, `nombre_usuario`, `descripcion`, `dir_img_perfil`) VALUES
(1, 'ivanmieres04@gmail.com', 'Ivan', 'Mieres', 25, '$2y$10$JcINX/3jVC7hlMG4fbfDHebwzqsm5th1ikGj29mLyEkccL92fHVpq', 'dailux', 'tienda de indumentaria masculina ', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos_usuarios`
--
ALTER TABLE `productos_usuarios`
  ADD PRIMARY KEY (`id_producto`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos_usuarios`
--
ALTER TABLE `productos_usuarios`
  ADD CONSTRAINT `productos_usuarios_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_usuarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
