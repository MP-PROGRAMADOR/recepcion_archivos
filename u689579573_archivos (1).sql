-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 30-04-2025 a las 14:45:43
-- Versión del servidor: 10.11.10-MariaDB
-- Versión de PHP: 7.2.34


drop database if exists recepcion_archivo;

create DATABASE recepcion_archivo;
use recepcion_archivo;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u689579573_archivos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios_academicos`
--

CREATE TABLE `anios_academicos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `anios_academicos`
--

INSERT INTO `anios_academicos` (`id`, `nombre`) VALUES
(3, '2000 - 2001'),
(4, '2001 - 2002'),
(5, '2002 - 2003'),
(6, '2003 - 2004'),
(7, '2004 - 2005'),
(8, '2005 - 2006'),
(9, '2006 - 2007'),
(10, '2007 - 2008'),
(11, '2008 - 2009'),
(12, '2009 - 2010'),
(13, '2010 - 2011'),
(14, '2011 - 2012'),
(15, '2012 - 2013'),
(16, '2013 - 2014'),
(17, '2014 - 2015'),
(18, '2015 - 2016'),
(19, '2016 - 2017'),
(20, '2017 - 2018'),
(21, '2018 - 2019'),
(22, '2019 - 2020'),
(23, '2020 - 2021'),
(24, '2021 - 2022'),
(25, '2022 - 2023'),
(26, '2023 - 2024'),
(1, '2023-2024'),
(27, '2024 - 2025'),
(2, '2024-2025'),
(28, '2025 - 2026');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pais_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre`, `pais_id`) VALUES
(1, 'Madrid', 160);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre_sitio` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `color_primario` varchar(7) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `img_estudiante` varchar(255) DEFAULT NULL,
  `img_admin` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `codigo_acceso` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `pais_id` int(11) NOT NULL,
  `ciudad_id` int(11) DEFAULT NULL,
  `universidad_id` int(11) DEFAULT NULL,
  `idioma_id` int(11) DEFAULT NULL,
  `anio_inicio_carrera` year(4) DEFAULT NULL,
  `anio_fin_carrera` year(4) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `ruta_foto` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `idioma` varchar(255) DEFAULT NULL,
  `meses_idioma` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `nombre_completo`, `codigo_acceso`, `fecha_nacimiento`, `pais_id`, `ciudad_id`, `universidad_id`, `idioma_id`, `anio_inicio_carrera`, `anio_fin_carrera`, `email`, `telefono`, `foto_perfil`, `ruta_foto`, `creado_en`) VALUES
(1, 'Veneralda Jimenez Rodrigo', 'VJR-200400-1I', '2000-04-20', 160, 1, 1, NULL, '2020', '2027', 'salvadormete2@gmail.com', '222478702', 'foto_perfil_1_1745998174.jpg', NULL, '2025-04-29 17:21:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomas`
--

CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `meses_duracion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) DEFAULT NULL,
  `anio_academico_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `estudiante_id`, `anio_academico_id`, `observaciones`, `archivo_url`, `fecha_subida`) VALUES
(1, 1, 1, 'el curso fue muy bien', 'Notas_2023-2024_VJR-200400-1I.pdf', '2025-04-30 07:25:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id`, `nombre`) VALUES
(1, 'Afghanistan'),
(2, 'Albania'),
(3, 'Algeria'),
(4, 'Andorra'),
(5, 'Angola'),
(6, 'Antigua and Barbuda'),
(7, 'Argentina'),
(8, 'Armenia'),
(9, 'Australia'),
(10, 'Austria'),
(11, 'Azerbaijan'),
(12, 'Bahamas'),
(13, 'Bahrain'),
(14, 'Bangladesh'),
(15, 'Barbados'),
(16, 'Belgium'),
(17, 'Belize'),
(18, 'Benin'),
(19, 'Bhutan'),
(20, 'Bolivia'),
(21, 'Bosnia and Herzegovina'),
(22, 'Botswana'),
(23, 'Brunei'),
(24, 'Bulgaria'),
(25, 'Burkina Faso'),
(26, 'Burundi'),
(27, 'Cabo Verde'),
(28, 'Cambodia'),
(29, 'Cameroon'),
(30, 'Canada'),
(31, 'Central African Republic'),
(32, 'Chad'),
(33, 'Chile'),
(34, 'China'),
(35, 'Colombia'),
(36, 'Comoros'),
(37, 'Congo'),
(38, 'Costa Rica'),
(39, 'Croatia'),
(40, 'Cuba'),
(41, 'Cyprus'),
(42, 'Czech Republic'),
(43, 'Denmark'),
(44, 'Djibouti'),
(45, 'Dominica'),
(46, 'Dominican Republic'),
(47, 'East Timor'),
(48, 'Ecuador'),
(49, 'Egypt'),
(50, 'El Salvador'),
(51, 'Equatorial Guinea'),
(52, 'Eritrea'),
(53, 'Estonia'),
(54, 'Eswatini'),
(55, 'Ethiopia'),
(56, 'Fiji'),
(57, 'Finland'),
(58, 'France'),
(59, 'Gabon'),
(60, 'Gambia'),
(61, 'Georgia'),
(62, 'Germany'),
(63, 'Ghana'),
(64, 'Greece'),
(65, 'Grenada'),
(66, 'Guatemala'),
(67, 'Guinea'),
(68, 'Guinea-Bissau'),
(69, 'Guyana'),
(70, 'Haiti'),
(71, 'Honduras'),
(72, 'Hungary'),
(73, 'Iceland'),
(74, 'India'),
(75, 'Indonesia'),
(76, 'Iran'),
(77, 'Iraq'),
(78, 'Ireland'),
(79, 'Israel'),
(80, 'Italy'),
(81, 'Jamaica'),
(82, 'Japan'),
(83, 'Jordan'),
(84, 'Kazakhstan'),
(85, 'Kenya'),
(86, 'Kiribati'),
(87, 'Korea North'),
(88, 'Korea South'),
(89, 'Kuwait'),
(90, 'Kyrgyzstan'),
(91, 'Laos'),
(92, 'Latvia'),
(93, 'Lebanon'),
(94, 'Lesotho'),
(95, 'Liberia'),
(96, 'Libya'),
(97, 'Liechtenstein'),
(98, 'Lithuania'),
(99, 'Luxembourg'),
(100, 'Madagascar'),
(101, 'Malawi'),
(102, 'Malaysia'),
(103, 'Maldives'),
(104, 'Mali'),
(105, 'Malta'),
(106, 'Marshall Islands'),
(107, 'Mauritania'),
(108, 'Mauritius'),
(109, 'Mexico'),
(110, 'Micronesia'),
(111, 'Moldova'),
(112, 'Monaco'),
(113, 'Mongolia'),
(114, 'Montenegro'),
(115, 'Morocco'),
(116, 'Mozambique'),
(117, 'Namibia'),
(118, 'Nauru'),
(119, 'Nepal'),
(120, 'Netherlands'),
(121, 'New Zealand'),
(122, 'Nicaragua'),
(123, 'Niger'),
(124, 'Nigeria'),
(125, 'North Macedonia'),
(126, 'Norway'),
(127, 'Oman'),
(128, 'Pakistan'),
(129, 'Palau'),
(130, 'Panama'),
(131, 'Papua New Guinea'),
(132, 'Paraguay'),
(133, 'Peru'),
(134, 'Philippines'),
(135, 'Poland'),
(136, 'Portugal'),
(137, 'Qatar'),
(138, 'Romania'),
(139, 'Russia'),
(140, 'Rwanda'),
(141, 'Saint Kitts and Nevis'),
(142, 'Saint Lucia'),
(143, 'Saint Vincent and the Grenadines'),
(144, 'Samoa'),
(145, 'San Marino'),
(146, 'Sao Tome and Principe'),
(147, 'Saudi Arabia'),
(148, 'Senegal'),
(149, 'Serbia'),
(150, 'Seychelles'),
(151, 'Sierra Leone'),
(152, 'Singapore'),
(153, 'Slovakia'),
(154, 'Slovenia'),
(155, 'Solomon Islands'),
(156, 'Somalia'),
(157, 'South Africa'),
(158, 'South Korea'),
(159, 'South Sudan'),
(160, 'Spain'),
(161, 'Sri Lanka'),
(162, 'Sudan'),
(163, 'Suriname'),
(164, 'Sweden'),
(165, 'Switzerland'),
(166, 'Syria'),
(167, 'Taiwan'),
(168, 'Tajikistan'),
(169, 'Tanzania'),
(170, 'Thailand'),
(171, 'Togo'),
(172, 'Tonga'),
(173, 'Trinidad and Tobago'),
(174, 'Tunisia'),
(175, 'Turkey'),
(176, 'Turkmenistan'),
(177, 'Tuvalu'),
(178, 'Uganda'),
(179, 'Ukraine'),
(180, 'United Arab Emirates'),
(181, 'United Kingdom'),
(182, 'United States'),
(183, 'Uruguay'),
(184, 'Uzbekistan'),
(185, 'Vanuatu'),
(186, 'Vatican City'),
(187, 'Venezuela'),
(188, 'Vietnam'),
(189, 'Yemen'),
(190, 'Zambia'),
(191, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pasaportes`
--

CREATE TABLE `pasaportes` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) DEFAULT NULL,
  `numero_pasaporte` varchar(50) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `pasaportes`
--

INSERT INTO `pasaportes` (`id`, `estudiante_id`, `numero_pasaporte`, `fecha_emision`, `fecha_expiracion`, `archivo_url`, `fecha_subida`) VALUES
(1, 1, 'P12093', '2023-02-08', '2028-05-16', 'pasaporte_1_1745997858.pdf', '2025-04-30 07:24:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidades`
--

CREATE TABLE `universidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ciudad_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `universidades`
--

INSERT INTO `universidades` (`id`, `nombre`, `ciudad_id`) VALUES
(1, 'Juan Carlos I', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contrasena`, `creado_en`) VALUES
(1, 'MINERVA GIMENEZ', 'minerva@prueba.com', '$2y$10$duVZG93NwcxWRv4WGhY0mOjE8R5EhIuvAnt6nSgyUbbYrD/Z8p.OW', '2025-04-10 16:43:30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anios_academicos`
--
ALTER TABLE `anios_academicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pais_id` (`pais_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_acceso` (`codigo_acceso`),
  ADD KEY `fk_estudiantes_pais` (`pais_id`),
  ADD KEY `fk_estudiantes_ciudad` (`ciudad_id`),
  ADD KEY `fk_estudiantes_universidad` (`universidad_id`),
  ADD KEY `fk_estudiantes_idioma` (`idioma_id`);

--
-- Indices de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notas_estudiante` (`estudiante_id`),
  ADD KEY `fk_notas_anio` (`anio_academico_id`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pasaporte_estudiante` (`estudiante_id`);

--
-- Indices de la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ciudad_id` (`ciudad_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anios_academicos`
--
ALTER TABLE `anios_academicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT de la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `universidades`
--
ALTER TABLE `universidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `estudiantes_ibfk_2` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `estudiantes_ibfk_3` FOREIGN KEY (`universidad_id`) REFERENCES `universidades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `estudiantes_ibfk_4` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`anio_academico_id`) REFERENCES `anios_academicos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  ADD CONSTRAINT `pasaportes_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD CONSTRAINT `universidades_ibfk_1` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
