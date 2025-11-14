-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-11-2025 a las 15:09:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) DEFAULT NULL,
  `id_categoriapadre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `id_categoriapadre`) VALUES
(1, 'Moda', NULL),
(3, 'Electronica', NULL),
(4, 'Electrodomésticos', NULL),
(7, 'Tecnología', NULL),
(10, 'laptops', 7),
(11, 'Artesania', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id_direccion` int(11) NOT NULL,
  `calle` varchar(200) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `ciudad` varchar(200) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estilos_perfil`
--

CREATE TABLE `estilos_perfil` (
  `id_estilo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_estilo` varchar(100) NOT NULL DEFAULT 'Estilo Personalizado',
  `ruta_css` varchar(500) NOT NULL DEFAULT 'estilos/estilos_perfil.css',
  `ruta_html` varchar(500) NOT NULL DEFAULT 'html_personalizado/perfil_base.php',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `esta_activo` tinyint(1) DEFAULT 1,
  `version` int(11) DEFAULT 1,
  `descripcion` text DEFAULT NULL,
  `es_plantilla` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estilos_perfil`
--

INSERT INTO `estilos_perfil` (`id_estilo`, `id_usuario`, `nombre_estilo`, `ruta_css`, `ruta_html`, `fecha_creacion`, `fecha_actualizacion`, `esta_activo`, `version`, `descripcion`, `es_plantilla`) VALUES
(32, 12, 'Estilo Personalizado', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 13:50:16', '2025-11-14 13:50:16', 1, 1, 'Plantilla inicial por defecto', 0),
(33, 16, 'Estilo Personalizado', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 13:50:16', '2025-11-14 13:50:16', 1, 1, 'Plantilla inicial por defecto', 0),
(34, 18, 'Estilo Personalizado', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 13:50:16', '2025-11-14 13:50:16', 1, 1, 'Plantilla inicial por defecto', 0),
(35, 19, 'Estilo Personalizado', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 13:50:16', '2025-11-14 13:50:16', 1, 1, 'Plantilla inicial por defecto', 0),
(36, 21, 'Estilo Personalizado', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 13:50:16', '2025-11-14 13:50:16', 1, 1, 'Plantilla inicial por defecto', 0),
(39, 24, 'Estilo Default', 'estilos/estilos_perfil.css', 'php/perfil_base.php', '2025-11-14 14:00:25', '2025-11-14 14:07:52', 0, 1, 'Plantilla inicial por defecto del sistema', 0),
(40, 24, 'Estilo IA v2025-11-14 15:05', 'estilos/estilos_perfil_24v2_1763129108.css', 'html_personalizado/perfil_html_24v2_1763129108.php', '2025-11-14 14:05:08', '2025-11-14 14:07:52', 1, 2, 'Generado por IA: creame un perfil con estilo de una tienda que vende crochet ', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nombre_marca` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nombre_marca`) VALUES
(1, 'Logitech'),
(2, 'Samsung'),
(3, 'Xiaomi'),
(4, 'Apple'),
(5, 'Nike'),
(6, 'Adidas'),
(7, 'Puma'),
(8, 'Reebok'),
(9, 'Under Armour'),
(10, 'New Balance'),
(11, 'Vans'),
(12, 'Converse'),
(13, 'Fila'),
(14, 'Asics'),
(15, 'Timberland'),
(16, 'Lacoste'),
(17, 'Guess'),
(18, 'Levi\'s'),
(19, 'Columbia'),
(20, 'Samsung'),
(21, 'Apple'),
(22, 'Xiaomi'),
(23, 'Motorola'),
(24, 'Sony'),
(25, 'LG'),
(26, 'Dell'),
(27, 'HP'),
(28, 'Lenovo'),
(29, 'Acer'),
(30, 'Asus');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

CREATE TABLE `perfil` (
  `id_perfil` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `foto_perfil` varchar(500) DEFAULT NULL,
  `foto_portada` varchar(500) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `css_personalizado` varchar(500) DEFAULT 'estilos/estilos_perfil.css',
  `html_personalizado` varchar(500) DEFAULT 'html_personalizado/perfil_base.php'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfil`
--

INSERT INTO `perfil` (`id_perfil`, `id_usuario`, `foto_perfil`, `foto_portada`, `biografia`, `css_personalizado`, `html_personalizado`) VALUES
(2, 12, './img/690af0618ca4c_1762324577.jpg', './img/690d70a8418c2_1762488488.jpg', 'tienda tecnología', 'estilos/estilos_perfil_12.css', NULL),
(4, 16, './img/690d77f5201dc_1762490357.jpg', './img/690d77f520260_1762490357.jpg', 'hola', 'estilos/estilos_perfil.css', 'perfil_base.php'),
(7, 18, './img/perfiles/perfil_6914b9d7ae898.jpg', './img/perfiles/portada_6914aaf1557ef.jpg', 'ejemplo1', 'estilos/estilos_perfil.css', ''),
(8, 19, './img/perfiles/perfil_69150df2d32b7.jpg', './img/perfiles/portada_691511aacd625.jpg', 'Agrega info para que la gente sepa más de ti', 'estilos/estilos_perfil_19v4_1763045515.css', 'html_personalizado/perfil_html_19v4_1763045515.php'),
(12, 21, './img/perfiles/perfil_691678f24a8c7.jpg', NULL, 'coffe', 'estilos/estilos_perfil_21v2_1763124787.css', 'html_personalizado/perfil_html_21v2_1763124787.php'),
(15, 24, NULL, NULL, NULL, 'estilos/estilos_perfil_24v2_1763129108.css', 'html_personalizado/perfil_html_24v2_1763129108.php');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(500) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `estado_condicion` enum('nuevo','usado','reacondicionado') DEFAULT NULL,
  `estado_venta` enum('disponible','vendido','pausado') DEFAULT NULL,
  `stock` int(11) DEFAULT 1,
  `descripcion` text DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `dir_img` varchar(500) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre_producto`, `precio`, `estado_condicion`, `estado_venta`, `stock`, `descripcion`, `fecha_publicacion`, `dir_img`, `id_usuario`, `id_categoria`, `id_marca`) VALUES
(2, 'Acer-55', 700000.00, 'nuevo', 'disponible', 12, 'procesador i7', '2025-10-21', 'img/1761017223_0_Acer.jpg', 12, 10, NULL),
(4, 'Samsung Galaxy Tab A9 ', 185999.00, 'nuevo', 'disponible', 1, 'Capacidad: 64 GB   Resolución de las cámaras traseras: 8 Mpx   Tamaño de la pantalla: 8.7 \"', '2025-11-05', 'img/1762317447_0_A9samsung.jpg', 12, 7, NULL),
(5, 'Teclado Gamer Machenike K500b', 39999.00, 'nuevo', 'disponible', 6, 'Idioma: Inglés Internacional   Tipo de switch: Rojo', '2025-11-05', 'img/1762318066_0_teclado.webp', 12, 7, NULL),
(6, 'Teclado De Ordenador Con Cable Usb, Teclas Rgb N, Teclas Enr', 35000.00, 'nuevo', 'disponible', 1, 'Teclado de ordenador con cable USB RGB N teclas enrollables Teclado de membrana para trabajar y jugar  Características: teclado compacto: este diseño compacto de 61 teclas está diseñado para ser portátil, por lo que cabe fácilmente en la mochila sin sacrificar la funcionalidad. Su diseño ligero es ideal para varios entornos móviles, ya sea que trabajes desde un café o juegues mientras estás fuera. Sin conflictos de teclas: con una tecnología de 25 teclas que evita conflictos, este teclado es imprescindible para los jugadores. Disfruta de una multitarea sin interrupciones sin demoras de entrada, lo que te permitirá liberar todo tu potencial. Retroiluminación RGB: disfruta de la retroiluminación RGB con varios modos y efectos de luz personalizables. Tanto si se encuentra en una habitación con poca luz como si simplemente quiere añadir un toque de color a su configuración, este teclado es la elección ideal. Fuerte tacto mecánico: con un tacto mecánico robusto, el teclado utiliza un grabado con inyección de aceite para obtener caracteres nítidos y transparentes. Este diseño garantiza que las teclas sean fáciles de leer incluso en condiciones de poca luz. Método de conexión: el teclado se conecta mediante un cable USB, lo que proporciona una conexión estable y fiable adecuada tanto para juegos como para tareas de productividad. Disfruta de una experiencia de toque fluida.  Especificaciones: Tipo de artículo: teclado de ordenador Material: ABS Conexión: USB Teclas con cable: 61 teclas Iluminación: RGB Modo de iluminación: 8 tipos de cambio de iluminación de un solo color  Lista de paquetes:  1 teclado de ordenador, 1 manual de instrucciones  **NOTA** Si tienes alguna pregunta, te invitamos a ponerte en contacto con nosotros a través de MENSAJES para ayudarte lo antes posible.', '2025-11-05', 'img/1762318302_0_teclado22.webp', 12, 7, NULL),
(7, 'Mause Inalámbrico M280 Logitech Color Negro', 19100.00, 'nuevo', 'disponible', 24, 'Lo que tenés que saber de este producto Es inalámbrico. Tipo de alimentación inalámbrica: pilas. Posee rueda de desplazamiento. Cuenta con interruptor de ahorro de energía. Con sensor óptico. Resolución de 1000dpi.', '2025-11-05', 'img/1762318544_0_mause.webp', 12, 7, 1),
(10, 'Notebook HP 15-fd0151la, Intel Core i5, ', 739999.00, 'nuevo', 'disponible', 1, 'Procesador: Intel Core i5 1334U.\r\nVersión del sistema operativo: 11.\r\nEdición del sistema operativo: Home.\r\nNombre del sistema operativo: Windows.\r\nCapacidad de disco SSD: 512 GB.\r\nCapacidad total del módulo de memoria RAM: 8 GB.\r\nCon pantalla táctil: Sí.\r\nResolución de la pantalla: 1920 px x 1080 px.\r\nConexión bluetooth.\r\nPosee pad numérico.', '2025-11-05', './img/1762324118_0_690aee96b8832_D_NQ_NP_2X_984532-MLA96402506599_102025-F.jpg,./img/1762324118_1_690aee96b8925_D_NQ_NP_2X_772252-MLA96402506597_102025-F.jpg', 12, 10, NULL),
(11, 'Jean Wide Leg Sky Mujer 47 Street', 54999.00, 'nuevo', 'disponible', 50, 'Jean clásico celeste matizado con detalles metálicos color cobre. Tiro medio que, según el talle, puede lucirse como tiro bajo, con piernas amplias y calce relajado que suma comodidad y estilo. Lavado artesanal que hace que cada prenda sea única. Ideal para combinar con tops cortos, remeras básicas o camisas oversize.\r\n\r\nComposición: 100% algodón.\r\n\r\nCuidados: lavar con agua fría, no usar secadora.\r\n\r\nLa modelo mide 1,71 y usa talle 24.', '2025-11-07', './img/1762490798_0_690d79ae054ec_D_NQ_NP_2X_965036-MLA93399797152_092025-F.jpg,./img/1762490798_1_690d79ae05598_D_NQ_NP_2X_856894-MLA93399797138_092025-F.jpg,./img/1762490798_2_690d79ae055dd_D_NQ_NP_2X_789941-MLA93399797134_092025-F.jpg', 16, 1, NULL),
(12, 'Jean Wide Leg Sky Mujer 47 Street', 54999.00, 'nuevo', 'disponible', 60, 'Jean clásico celeste matizado con detalles metálicos color cobre. Tiro medio que, según el talle, puede lucirse como tiro bajo, con piernas amplias y calce relajado que suma comodidad y estilo. Lavado artesanal que hace que cada prenda sea única. Ideal para combinar con tops cortos, remeras básicas o camisas oversize.\r\n\r\nComposición: 100% algodón.\r\n\r\nCuidados: lavar con agua fría, no usar secadora.\r\n\r\nLa modelo mide 1,71 y usa talle 24.', '2025-11-07', './img/1762490993_0_690d7a714341b_D_NQ_NP_2X_965036-MLA93399797152_092025-F.jpg,./img/1762490993_1_690d7a71434c4_D_NQ_NP_2X_856894-MLA93399797138_092025-F.jpg,./img/1762490993_2_690d7a714350c_D_NQ_NP_2X_789941-MLA93399797134_092025-F.jpg', 16, 1, NULL),
(13, 'CAMISA MAO LISO M/L', 118000.00, 'nuevo', 'disponible', 12, 'camisa mao liso', '2025-11-12', './img/1762962430_0_6914abfe125ff_41208_89_1767-5856758e72474f1a8d17565794396691-1024-1024.jpg,./img/1762962430_1_6914abfe12915_41208_80_0011-2dde52c0b9f6fb87e117565793768448-1024-1024.jpg', 18, 1, NULL),
(14, 'PANTALON CASCARILLA', 146999.00, 'nuevo', 'disponible', 2, 'PANTALON CASCARILLA', '2025-11-12', './img/1762962708_0_6914ad14b7392_47256_95_0069-f51afb74f69925caab17597760717055-480-0.jpg,./img/1762962708_1_6914ad14b7433_47256_95_0077-b16a35f6bee71bc14f17597760823104-640-0.jpg', 18, 1, NULL),
(15, 'Notebook HP 15-fd0151la, Intel Core i5, ', 500000.00, 'reacondicionado', 'disponible', 3, 'neetbook', '2025-11-12', './img/1762971991_0_6914d157d6329_Acer.jpg', 18, 7, NULL),
(16, 'Cafetera Expresso Digital Suono Automática Acero Inox 1.5 L Color Plateado', 158399.00, 'nuevo', 'disponible', 3, 'Tengo 3 cafeteras!', '2025-11-12', './img/1762988666_0_6915127ab85a8_D_NQ_NP_2X_833004-MLA96897922534_112025-F.jpg', 19, 4, NULL),
(19, 'Rayo mc sally', 70000.00, 'nuevo', 'disponible', 1, 'Rayo mc sally', '2025-11-13', './img/1763082430_0_691680be22ba5_4eb20b6c-78c2-4e1e-b3a8-d100e6c6622a.jpeg', 21, 11, NULL),
(21, 'cafetera italiana', 77000.00, 'nuevo', 'disponible', 12, 'cafetera italiana', '2025-11-14', './img/1763124523_0_6917252b451e0_1200px-Macchinetta.jpg', 21, 4, NULL),
(23, 'Rayo mc sally', 60000.00, 'nuevo', 'disponible', 2, 'rayo mc sally', '2025-11-14', './img/1763128914_0_691736525d8db_4eb20b6c-78c2-4e1e-b3a8-d100e6c6622a.jpeg', 24, 11, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_comp`
--

CREATE TABLE `productos_comp` (
  `id_producto_comp` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_usuario1` int(11) DEFAULT NULL,
  `id_usuario2` int(11) DEFAULT NULL,
  `precio_comp` decimal(10,2) DEFAULT NULL,
  `fecha_comp` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_usuarios`
--

CREATE TABLE `productos_usuarios` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_usuarios`
--

INSERT INTO `productos_usuarios` (`id`, `id_producto`, `id_usuario`, `fecha`) VALUES
(1, 2, 12, '2025-10-21 00:34:42'),
(4, 15, 12, '2025-11-12 15:28:59'),
(5, 21, 21, '2025-11-14 09:50:39'),
(6, 21, 21, '2025-11-14 09:50:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_follows`
--

CREATE TABLE `user_follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `contrasena` varchar(100) DEFAULT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `reputacion` int(11) DEFAULT 0,
  `id_direccion` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `seguidores` int(11) DEFAULT 0,
  `telefono` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `contrasena`, `nombre_usuario`, `edad`, `reputacion`, `id_direccion`, `descripcion`, `seguidores`, `telefono`) VALUES
(12, 'admin', 'admin', 'admin@gamil.com', '$2y$10$1uMl0pGYPNgg2C.e/fGzSuw/TyndUroPBFTe26fVV0I5M4aBU.N1m', 'admin', 26, 0, NULL, 'Shopp', 0, NULL),
(16, 'Ivan', 'Mieres', 'ivanmieres@gmail.com', '$2y$10$NiBdDmfquCDPGWbgP5mNGueJyUOpu5m5n0g8KNGEurgCDKCZiMFbS', 'IvanMieres', 26, 0, NULL, NULL, 0, NULL),
(18, 'ejemplo', 'ejemploapellido', 'ejemplo@gmail.com', '$2y$10$A.EcYf2MChIsxWb6li/odOZ1dB/yUSXOyYnV18UE8vG3O7PuTpvje', 'ejemplo', 20, 0, NULL, NULL, 0, NULL),
(19, 'ivan', 'servin', 'dailux@gmail.com', '$2y$10$bfNoloQ47gZ/A1iRZq6uAeDU07otpPtc8CKwY.Z46aJPvn.2wbvX6', 'daii', 26, 0, NULL, NULL, 0, NULL),
(21, 'seba', 'servin', 'owell.servin@gmail.com', '$2y$10$IY99JEK4kuUOPPvjsNN6VeCAKa4fFNnXGRaTb2FD78MgJGbLN2mnS', 'iloveminipk', 21, 0, NULL, NULL, 0, NULL),
(24, 'tahia', 'servin', 'tahia@gmail.com', '$2y$10$wqlK9/ZcAYcUEys0jjuTx.VXbp3iTH1..g.4Heh9Qy/d9yBtVXHla', 'tahia artesania', 40, 0, NULL, NULL, 0, NULL);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `after_usuario_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    -- Insertar en perfil
    INSERT INTO perfil (
        id_usuario,
        foto_perfil,
        foto_portada,
        biografia,
        css_personalizado,
        html_personalizado
    ) VALUES (
        NEW.id_usuario,
        NULL,
        NULL,
        NULL,
        'estilos/estilos_perfil.css',
        'php/perfil_base.php'  -- Cambiado a la ruta correcta
    );

    -- Insertar plantilla DEFAULT activa en estilos_perfil
    INSERT INTO estilos_perfil (
        id_usuario,
        nombre_estilo,
        ruta_css,
        ruta_html,
        version,
        descripcion,
        esta_activo
    ) VALUES (
        NEW.id_usuario,
        'Estilo Default',
        'estilos/estilos_perfil.css',
        'php/perfil_base.php',  -- Cambiado a la ruta correcta
        1,
        'Plantilla inicial por defecto del sistema',
        1
    );
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD KEY `id_categoriapadre` (`id_categoriapadre`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id_direccion`);

--
-- Indices de la tabla `estilos_perfil`
--
ALTER TABLE `estilos_perfil`
  ADD PRIMARY KEY (`id_estilo`),
  ADD KEY `estilos_perfil_ibfk_1` (`id_usuario`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id_perfil`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_marca` (`id_marca`);

--
-- Indices de la tabla `productos_comp`
--
ALTER TABLE `productos_comp`
  ADD PRIMARY KEY (`id_producto_comp`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario1` (`id_usuario1`),
  ADD KEY `id_usuario2` (`id_usuario2`);

--
-- Indices de la tabla `productos_usuarios`
--
ALTER TABLE `productos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `contrasena` (`contrasena`),
  ADD KEY `id_direccion` (`id_direccion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estilos_perfil`
--
ALTER TABLE `estilos_perfil`
  MODIFY `id_estilo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `perfil`
--
ALTER TABLE `perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `productos_comp`
--
ALTER TABLE `productos_comp`
  MODIFY `id_producto_comp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_usuarios`
--
ALTER TABLE `productos_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `user_follows`
--
ALTER TABLE `user_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`id_categoriapadre`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `estilos_perfil`
--
ALTER TABLE `estilos_perfil`
  ADD CONSTRAINT `estilos_perfil_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `perfil`
--
ALTER TABLE `perfil`
  ADD CONSTRAINT `perfil_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`);

--
-- Filtros para la tabla `productos_comp`
--
ALTER TABLE `productos_comp`
  ADD CONSTRAINT `productos_comp_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `productos_comp_ibfk_2` FOREIGN KEY (`id_usuario1`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `productos_comp_ibfk_3` FOREIGN KEY (`id_usuario2`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos_usuarios`
--
ALTER TABLE `productos_usuarios`
  ADD CONSTRAINT `fk_pu_producto_min` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pu_usuario_min` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_follows`
--
ALTER TABLE `user_follows`
  ADD CONSTRAINT `user_follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
