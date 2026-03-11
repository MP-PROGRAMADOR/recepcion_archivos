-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 25-02-2026 a las 09:55:39
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recepcion_archivo`
--


drop database if exists recepcion_archivo;
create database recepcion_archivo;
use recepcion_archivo;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios_academicos`
--

CREATE TABLE `anios_academicos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(28, '2025 - 2026'),
(29, '2026 - 2027');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pais_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre`, `pais_id`) VALUES
(1, 'Madrid', 160),
(2, 'Mexicana', 109),
(3, 'Cotonou', 18),
(4, 'Yaunde', 29),
(5, 'malta', 17);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `tipo_cuenta` varchar(50) NOT NULL,
  `banco` varchar(100) NOT NULL,
  `numero_cuenta` varchar(30) NOT NULL,
  `tarjeta_visa` varchar(30) DEFAULT NULL,
  `fecha_caducidad_tarjeta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cuentas_bancarias`
--

INSERT INTO `cuentas_bancarias` (`id`, `estudiante_id`, `tipo_cuenta`, `banco`, `numero_cuenta`, `tarjeta_visa`, `fecha_caducidad_tarjeta`) VALUES
(1, 37, 'departamental', 'ecobank', '39369909873', 'si', '2029-06-20'),
(2, 14, 'departamental', 'ecobank', '39369909873', 'si', '2029-06-06'),
(3, 4, 'propia', 'cceibank', '5551234532123', 'si', '2030-06-12'),
(4, 36, 'departamental', 'ecobank', '3987654323', 'si', '2027-04-08'),
(5, 15, 'departamental', 'ecobank', '345678765', 'si', '2027-04-10'),
(6, 2, 'propia', 'ecobank', '5666789654332', 'no', NULL);

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
  `carrera_actual` varchar(150) NOT NULL,
  `ciudad_actual` varchar(100) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `ruta_foto` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `idioma` varchar(255) DEFAULT NULL,
  `meses_idioma` int(11) DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `archivo_beca` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `nombre_completo`, `codigo_acceso`, `fecha_nacimiento`, `pais_id`, `ciudad_id`, `universidad_id`, `idioma_id`, `anio_inicio_carrera`, `anio_fin_carrera`, `email`, `telefono`, `carrera_actual`, `ciudad_actual`, `foto_perfil`, `ruta_foto`, `creado_en`, `idioma`, `meses_idioma`, `cuenta_id`, `archivo_beca`) VALUES
(1, 'Veneralda Jimenez Rodriga', 'VJR-200400-1I', '2000-04-20', 160, 1, 1, NULL, 2020, 2028, 'salvadormete2@gmail.com', '222478702', '', '', 'foto_perfil_1_1745998174.jpg', NULL, '2025-04-29 17:21:02', NULL, NULL, NULL, 'upload/becas/beca-VJR-200400-1I.pdf'),
(2, 'ana maria boko', 'AMB-070617-2E', '2017-06-07', 160, 1, 1, 2, 2025, 2028, 'salvadormete3@gmail.com', '+234987543', '', '', 'foto_perfil_2_1771589552.jpg', NULL, '2025-07-08 14:29:50', NULL, 12, NULL, 'uploads/becas/beca_1751984990_686d2b5e81a94.pdf'),
(3, 'Bartolome Yamal', 'BY-070694-3A', '1994-06-07', 160, 1, 1, NULL, 2020, 2025, NULL, NULL, '', '', NULL, NULL, '2025-11-07 09:54:37', NULL, NULL, NULL, 'upload/becas/beca_1762509277_690dc1ddd155b.png'),
(4, 'Beatriz EFUA EPESI', 'BEE-040504-4O', '2004-05-04', 160, 1, 1, NULL, 2021, 2027, NULL, NULL, '', '', NULL, NULL, '2025-11-07 09:58:45', NULL, NULL, NULL, 'upload/becas/beca_1762509525_690dc2d5282d8.png'),
(5, 'Maripaz Eyanga', 'ME-070201-5M', '2001-02-07', 160, 1, 1, NULL, 2024, 2028, NULL, NULL, '', '', NULL, NULL, '2025-11-07 10:00:44', NULL, NULL, NULL, 'upload/becas/beca_1762509644_690dc34cf140e.png'),
(6, 'Manuel mecheba', 'MM-100299-6K', '1999-02-10', 109, 2, 2, NULL, 2023, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 12:10:45', NULL, NULL, NULL, 'upload/becas/beca_1771503045_6996fdc58dc37.pdf'),
(7, 'Pedro Nguema Mba', 'PNM-120398-7P', '1998-03-12', 160, 1, 1, NULL, 2022, 2026, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:01', NULL, NULL, NULL, 'upload/becas/beca-PNM-120398-7P.pdf'),
(8, 'Lucia Ondo Esono', 'LOE-230801-8L', '2001-08-23', 160, 1, 1, NULL, 2021, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:02', NULL, NULL, NULL, 'upload/becas/beca-LOE.pdf'),
(9, 'Carlos Obama Ela', 'COE-010500-9C', '2000-05-01', 160, 1, 1, NULL, 2019, 2025, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:03', NULL, NULL, NULL, 'upload/becas/beca-COE.pdf'),
(10, 'Maria Nsue Nkogo', 'MNN-150902-10M', '2002-09-15', 160, 1, 1, NULL, 2023, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:04', NULL, NULL, NULL, 'upload/becas/beca-MNN.pdf'),
(11, 'Javier Ekong Abaga', 'JEA-020796-11J', '1996-07-02', 160, 1, 1, NULL, 2018, 2024, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:05', NULL, NULL, NULL, 'upload/becas/beca-JEA.pdf'),
(12, 'Sandra Nchama Nguema', 'SNN-110300-12S', '2000-03-11', 160, 1, 1, NULL, 2020, 2026, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:06', NULL, NULL, NULL, 'upload/becas/beca-SNN.pdf'),
(13, 'Miguel Ndong Oyono', 'MNO-300198-13M', '1998-01-30', 160, 1, 1, NULL, 2019, 2025, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:07', NULL, NULL, NULL, 'upload/becas/beca-MNO.pdf'),
(14, 'Teresa Abeso Esono', 'TAE-140603-14T', '2003-06-14', 160, 1, 1, NULL, 2022, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:08', NULL, NULL, NULL, 'upload/becas/beca-TAE.pdf'),
(15, 'Josefa Obama Biyogo', 'JOB-221299-15J', '1999-12-22', 160, 1, 1, NULL, 2021, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:09', NULL, NULL, NULL, 'upload/becas/beca-JOB.pdf'),
(16, 'Antonio Mba Evuna', 'AME-080197-16A', '1997-01-08', 160, 1, 1, NULL, 2018, 2024, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:10', NULL, NULL, NULL, 'upload/becas/beca-AME.pdf'),
(17, 'Rosa Ela Nsue', 'REN-170701-17R', '2001-07-17', 160, 1, 1, NULL, 2023, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:11', NULL, NULL, NULL, 'upload/becas/beca-REN.pdf'),
(18, 'Victor Ondo Mba', 'VOM-090499-18V', '1999-04-09', 160, 1, 1, NULL, 2020, 2026, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:12', NULL, NULL, NULL, 'upload/becas/beca-VOM.pdf'),
(19, 'Gloria Nguema Eyama', 'GNE-050502-19G', '2002-05-05', 160, 1, 1, NULL, 2024, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:13', NULL, NULL, NULL, 'upload/becas/beca-GNE.pdf'),
(20, 'Samuel Ndong Asumu', 'SNA-270698-20S', '1998-06-27', 160, 1, 1, NULL, 2019, 2025, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:14', NULL, NULL, NULL, 'upload/becas/beca-SNA.pdf'),
(21, 'Patricia Esono Abeso', 'PEA-031203-21P', '2003-12-03', 160, 1, 1, NULL, 2022, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:15', NULL, NULL, NULL, 'upload/becas/beca-PEA.pdf'),
(22, 'Felix Obama Ndong', 'FON-210497-22F', '1997-04-21', 160, 1, 1, NULL, 2017, 2023, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:16', NULL, NULL, NULL, 'upload/becas/beca-FON.pdf'),
(23, 'Angela Mba Eyene', 'AME-101000-23A', '2000-10-10', 160, 1, 1, NULL, 2021, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:17', NULL, NULL, NULL, 'upload/becas/beca-AME23.pdf'),
(24, 'Domingo Nsue Biyogo', 'DNB-181196-24D', '1996-11-18', 160, 1, 1, NULL, 2016, 2022, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:18', NULL, NULL, NULL, 'upload/becas/beca-DNB.pdf'),
(25, 'Isabel Nchama Ela', 'INE-260802-25I', '2002-08-26', 160, 1, 1, NULL, 2023, 2027, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:19', NULL, NULL, NULL, 'upload/becas/beca-INE.pdf'),
(26, 'Mateo Ondo Evuna', 'MOE-300399-26M', '1999-03-30', 160, 1, 1, NULL, 2019, 2025, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:20', NULL, NULL, NULL, 'upload/becas/beca-MOE.pdf'),
(27, 'Leticia Obama Esono', 'LOE-050604-27L', '2004-06-05', 160, 1, 1, NULL, 2024, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:21', NULL, NULL, NULL, 'upload/becas/beca-LOE27.pdf'),
(28, 'Francisco Ndong Nsue', 'FNN-121298-28F', '1998-12-12', 160, 1, 1, NULL, 2020, 2026, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:22', NULL, NULL, NULL, 'upload/becas/beca-FNN.pdf'),
(29, 'Raquel Mba Nkogo', 'RMN-070901-29R', '2001-09-07', 160, 1, 1, NULL, 2022, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:23', NULL, NULL, NULL, 'upload/becas/beca-RMN.pdf'),
(30, 'Esteban Eyama Nguema', 'EEN-190497-30E', '1997-04-19', 160, 1, 1, NULL, 2018, 2024, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:24', NULL, NULL, NULL, 'upload/becas/beca-EEN.pdf'),
(31, 'Claudia Nsue Obama', 'CNO-011103-31C', '2003-11-01', 160, 1, 1, NULL, 2024, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:25', NULL, NULL, NULL, 'upload/becas/beca-CNO.pdf'),
(32, 'Bernardo Ondo Ela', 'BOE-150698-32B', '1998-06-15', 160, 1, 1, NULL, 2019, 2025, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:26', NULL, NULL, NULL, 'upload/becas/beca-BOE.pdf'),
(33, 'Nuria Mba Abaga', 'NMA-040801-33N', '2001-08-04', 160, 1, 1, NULL, 2022, 2028, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:27', NULL, NULL, NULL, 'upload/becas/beca-NMA.pdf'),
(34, 'Julian Nchama Nsue', 'JNN-290597-34J', '1997-05-29', 160, 1, 1, NULL, 2018, 2024, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:28', NULL, NULL, NULL, 'upload/becas/beca-JNN.pdf'),
(35, 'Silvia Obama Eyene', 'SOE-020402-35S', '2002-04-02', 160, 1, 1, NULL, 2023, 2027, 'mpprogramacion22@gmail.com', '+234987543', 'Ingeniería Informática', 'Madrid', 'foto_perfil_35_1772007699.jpeg', NULL, '2026-02-19 11:15:29', NULL, NULL, NULL, 'upload/becas/beca-SOE.pdf'),
(36, 'Hector Ndong Mba', 'HNM-080699-36H', '1999-06-08', 160, 1, 1, NULL, 2020, 2026, NULL, NULL, '', '', NULL, NULL, '2026-02-19 11:15:30', NULL, NULL, NULL, 'upload/becas/beca-HNM.pdf'),
(37, 'Maria Araceli', 'MA-090595-37E', '1995-05-09', 109, 2, 2, NULL, 2020, 2029, NULL, NULL, '', '', NULL, NULL, '2026-02-20 13:38:31', NULL, NULL, NULL, 'upload/becas/beca_1771594711_699863d71c4db.pdf'),
(38, 'Pedro Gimenes', 'PG-040298-38B', '1998-02-04', 18, 3, 3, NULL, 2024, 2031, NULL, NULL, '', '', NULL, NULL, '2026-02-23 15:43:22', NULL, NULL, NULL, 'upload/becas/beca_1771861401_699c7599f3e98.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idiomas`
--

CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `codigo_2` char(2) NOT NULL,
  `codigo_3` char(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `idiomas`
--

INSERT INTO `idiomas` (`id`, `nombre`, `codigo_2`, `codigo_3`) VALUES
(1, 'Español', '', ''),
(2, 'Inglés', '', ''),
(3, 'Francés', '', ''),
(4, 'Alemán', '', ''),
(5, 'Italiano', '', ''),
(6, 'Portugués', '', ''),
(7, 'Ruso', '', ''),
(8, 'Chino', '', ''),
(9, 'Japonés', '', ''),
(10, 'Árabe', '', ''),
(11, 'Hindi', '', ''),
(12, 'Bengalí', '', ''),
(13, 'Coreano', '', ''),
(14, 'Turco', '', ''),
(15, 'Vietnamita', '', ''),
(16, 'Polaco', '', ''),
(17, 'Ucraniano', '', ''),
(18, 'Neerlandés', '', ''),
(19, 'Sueco', '', ''),
(20, 'Noruego', '', ''),
(21, 'Danés', '', ''),
(22, 'Finés', '', ''),
(23, 'Hebreo', '', ''),
(24, 'Griego', '', ''),
(25, 'Checo', '', ''),
(26, 'Húngaro', '', ''),
(27, 'Rumano', '', ''),
(28, 'Tailandés', '', ''),
(29, 'Indonesio', '', ''),
(30, 'Filipino', '', ''),
(31, 'Malayo', '', ''),
(32, 'Swahili', '', ''),
(33, 'Persa', '', ''),
(34, 'Urdu', '', ''),
(35, 'Punjabi', '', ''),
(36, 'Tamil', '', ''),
(37, 'Telugu', '', ''),
(38, 'Gujarati', '', ''),
(39, 'Maratí', '', ''),
(40, 'Bielorruso', '', ''),
(41, 'Afar', 'aa', 'aar'),
(42, 'Abjasio', 'ab', 'abk'),
(43, 'Afrikáans', 'af', 'afr'),
(44, 'Akan', 'ak', 'aka'),
(45, 'Albanés', 'sq', 'sqi'),
(46, 'Amárico', 'am', 'amh'),
(47, 'Árabe', 'ar', 'ara'),
(48, 'Aragonés', 'an', 'arg'),
(49, 'Armenio', 'hy', 'hye'),
(50, 'Asamés', 'as', 'asm'),
(51, 'Avar', 'av', 'ava'),
(52, 'Aymara', 'ay', 'aym'),
(53, 'Azerí', 'az', 'aze'),
(54, 'Bambara', 'bm', 'bam'),
(55, 'Bashkir', 'ba', 'bak'),
(56, 'Bielorruso', 'be', 'bel'),
(57, 'Bengalí', 'bn', 'ben'),
(58, 'Bislama', 'bi', 'bis'),
(59, 'Bosnio', 'bs', 'bos'),
(60, 'Bretón', 'br', 'bre'),
(61, 'Búlgaro', 'bg', 'bul'),
(62, 'Birmano', 'my', 'mya'),
(63, 'Catalán', 'ca', 'cat'),
(64, 'Cebuano', 'ce', 'ceb'),
(65, 'Chino', 'zh', 'zho'),
(66, 'Chuvash', 'cv', 'chv'),
(67, 'Coreano', 'ko', 'kor'),
(68, 'Croata', 'hr', 'hrv'),
(69, 'Checo', 'cs', 'ces'),
(70, 'Danés', 'da', 'dan'),
(71, 'Holandés', 'nl', 'nld'),
(72, 'Inglés', 'en', 'eng'),
(73, 'Esperanto', 'eo', 'epo'),
(74, 'Estonio', 'et', 'est'),
(75, 'Ewe', 'ee', 'ewe'),
(76, 'Faroés', 'fo', 'fao'),
(77, 'Persa', 'fa', 'fas'),
(78, 'Finés', 'fi', 'fin'),
(79, 'Francés', 'fr', 'fra'),
(80, 'Gallego', 'gl', 'glg'),
(81, 'Georgiano', 'ka', 'kat'),
(82, 'Alemán', 'de', 'deu'),
(83, 'Griego', 'el', 'ell'),
(84, 'Guaraní', 'gn', 'grn'),
(85, 'Gujarati', 'gu', 'guj'),
(86, 'Haitiano', 'ht', 'hat'),
(87, 'Hausa', 'ha', 'hau'),
(88, 'Hebreo', 'he', 'heb'),
(89, 'Hindi', 'hi', 'hin'),
(90, 'Hmong', 'hm', 'hmn'),
(91, 'Húngaro', 'hu', 'hun'),
(92, 'Islandés', 'is', 'isl'),
(93, 'Igbo', 'ig', 'ibo'),
(94, 'Indonesio', 'id', 'ind'),
(95, 'Irlandés', 'ga', 'gle'),
(96, 'Italiano', 'it', 'ita'),
(97, 'Japonés', 'ja', 'jpn'),
(98, 'Canarés', 'kn', 'kan'),
(99, 'Kazajo', 'kk', 'kaz'),
(100, 'Jemer', 'km', 'khm'),
(101, 'Kinyarwanda', 'rw', 'kin'),
(102, 'Kirguís', 'ky', 'kir'),
(103, 'Kurdo', 'ku', 'kur'),
(104, 'Lao', 'lo', 'lao'),
(105, 'Latín', 'la', 'lat'),
(106, 'Letón', 'lv', 'lav'),
(107, 'Lituano', 'lt', 'lit'),
(108, 'Luxemburgués', 'lb', 'ltz'),
(109, 'Macedonio', 'mk', 'mkd'),
(110, 'Malayalam', 'ml', 'mal'),
(111, 'Malayo', 'ms', 'msa'),
(112, 'Maltés', 'mt', 'mlt'),
(113, 'Maorí', 'mi', 'mri'),
(114, 'Maratí', 'mr', 'mar'),
(115, 'Mongol', 'mn', 'mon'),
(116, 'Nepalí', 'ne', 'nep'),
(117, 'Noruego', 'no', 'nor'),
(118, 'Panyabí', 'pa', 'pan'),
(119, 'Pastún', 'ps', 'pus'),
(120, 'Polaco', 'pl', 'pol'),
(121, 'Portugués', 'pt', 'por'),
(122, 'Quechua', 'qu', 'que'),
(123, 'Rumano', 'ro', 'ron'),
(124, 'Ruso', 'ru', 'rus'),
(125, 'Samoano', 'sm', 'smo'),
(126, 'Serbio', 'sr', 'srp'),
(127, 'Sesotho', 'st', 'sot'),
(128, 'Shona', 'sn', 'sna'),
(129, 'Eslovaco', 'sk', 'slk'),
(130, 'Esloveno', 'sl', 'slv'),
(131, 'Somalí', 'so', 'som'),
(132, 'Español', 'es', 'spa'),
(133, 'Sundanés', 'su', 'sun'),
(134, 'Suajili', 'sw', 'swa'),
(135, 'Sueco', 'sv', 'swe'),
(136, 'Tágalo', 'tl', 'tgl'),
(137, 'Tayiko', 'tg', 'tgk'),
(138, 'Tamil', 'ta', 'tam'),
(139, 'Tártaro', 'tt', 'tat'),
(140, 'Telugu', 'te', 'tel'),
(141, 'Tailandés', 'th', 'tha'),
(142, 'Tibetano', 'bo', 'bod'),
(143, 'Tigriña', 'ti', 'tir'),
(144, 'Turco', 'tr', 'tur'),
(145, 'Turcomano', 'tk', 'tuk'),
(146, 'Ucraniano', 'uk', 'ukr'),
(147, 'Urdu', 'ur', 'urd'),
(148, 'Uzbeko', 'uz', 'uzb'),
(149, 'Vietnamita', 'vi', 'vie'),
(150, 'Galés', 'cy', 'cym'),
(151, 'Xhosa', 'xh', 'xho'),
(152, 'Yidis', 'yi', 'yid'),
(153, 'Yoruba', 'yo', 'yor'),
(154, 'Zulú', 'zu', 'zul'),
(155, 'Cebuano', 'ce', 'ceb'),
(156, 'Hmong', 'hm', 'hmn'),
(157, 'Cebuano', 'ce', 'ceb'),
(158, 'Hmong', 'hm', 'hmn'),
(159, 'Cebuano', 'ce', 'ceb'),
(160, 'Hmong', 'hm', 'hmn'),
(161, 'Cebuano', 'ce', 'ceb'),
(162, 'Hmong', 'hm', 'hmn'),
(163, 'Cebuano', 'ce', 'ceb'),
(164, 'Hmong', 'hm', 'hmn'),
(165, 'Cebuano', 'ce', 'ceb'),
(166, 'Hmong', 'hm', 'hmn'),
(167, 'Cebuano', 'ce', 'ceb'),
(168, 'Hmong', 'hm', 'hmn'),
(169, 'Cebuano', 'ce', 'ceb'),
(170, 'Hmong', 'hm', 'hmn'),
(171, 'Cebuano', 'ce', 'ceb'),
(172, 'Hmong', 'hm', 'hmn'),
(173, 'Cebuano', 'ce', 'ceb'),
(174, 'Hmong', 'hm', 'hmn'),
(175, 'Cebuano', 'ce', 'ceb'),
(176, 'Hmong', 'hm', 'hmn'),
(177, 'Cebuano', 'ce', 'ceb'),
(178, 'Hmong', 'hm', 'hmn'),
(179, 'Cebuano', 'ce', 'ceb'),
(180, 'Hmong', 'hm', 'hmn'),
(181, 'Cebuano', 'ce', 'ceb'),
(182, 'Hmong', 'hm', 'hmn'),
(183, 'Cebuano', 'ce', 'ceb'),
(184, 'Hmong', 'hm', 'hmn'),
(185, 'Cebuano', 'ce', 'ceb'),
(186, 'Hmong', 'hm', 'hmn'),
(187, 'Cebuano', 'ce', 'ceb'),
(188, 'Hmong', 'hm', 'hmn'),
(189, 'Cebuano', 'ce', 'ceb'),
(190, 'Hmong', 'hm', 'hmn'),
(191, 'Cebuano', 'ce', 'ceb'),
(192, 'Hmong', 'hm', 'hmn'),
(193, 'Cebuano', 'ce', 'ceb'),
(194, 'Hmong', 'hm', 'hmn'),
(195, 'Cebuano', 'ce', 'ceb'),
(196, 'Hmong', 'hm', 'hmn'),
(197, 'Cebuano', 'ce', 'ceb'),
(198, 'Hmong', 'hm', 'hmn'),
(199, 'Cebuano', 'ce', 'ceb'),
(200, 'Hmong', 'hm', 'hmn'),
(201, 'Cebuano', 'ce', 'ceb'),
(202, 'Hmong', 'hm', 'hmn'),
(203, 'Cebuano', 'ce', 'ceb'),
(204, 'Hmong', 'hm', 'hmn'),
(205, 'Cebuano', 'ce', 'ceb'),
(206, 'Hmong', 'hm', 'hmn'),
(207, 'Cebuano', 'ce', 'ceb'),
(208, 'Hmong', 'hm', 'hmn'),
(209, 'Cebuano', 'ce', 'ceb'),
(210, 'Hmong', 'hm', 'hmn'),
(211, 'Cebuano', 'ce', 'ceb'),
(212, 'Hmong', 'hm', 'hmn'),
(213, 'Cebuano', 'ce', 'ceb'),
(214, 'Hmong', 'hm', 'hmn'),
(215, 'Cebuano', 'ce', 'ceb'),
(216, 'Hmong', 'hm', 'hmn'),
(217, 'Cebuano', 'ce', 'ceb'),
(218, 'Hmong', 'hm', 'hmn'),
(219, 'Cebuano', 'ce', 'ceb'),
(220, 'Hmong', 'hm', 'hmn'),
(221, 'Cebuano', 'ce', 'ceb'),
(222, 'Hmong', 'hm', 'hmn'),
(223, 'Cebuano', 'ce', 'ceb'),
(224, 'Hmong', 'hm', 'hmn'),
(225, 'Cebuano', 'ce', 'ceb'),
(226, 'Hmong', 'hm', 'hmn'),
(227, 'Cebuano', 'ce', 'ceb'),
(228, 'Hmong', 'hm', 'hmn'),
(229, 'Cebuano', 'ce', 'ceb'),
(230, 'Hmong', 'hm', 'hmn'),
(231, 'Cebuano', 'ce', 'ceb'),
(232, 'Hmong', 'hm', 'hmn');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_actividades`
--

CREATE TABLE `log_actividades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(100) NOT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `navegador` varchar(255) DEFAULT NULL,
  `resultado` enum('EXITO','ERROR') DEFAULT 'EXITO',
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `log_actividades`
--

INSERT INTO `log_actividades` (`id`, `usuario_id`, `accion`, `modulo`, `registro_id`, `descripcion`, `ip_address`, `navegador`, `resultado`, `fecha`) VALUES
(1, 1, 'ACTUALIZAR', 'Ciudades', 3, 'Ciudad actualizada correctamente: Cotonou2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 15:57:41'),
(2, 1, 'ACTUALIZAR', 'Ciudades', 3, 'Ciudad actualizada correctamente: Cotonou', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 15:58:08'),
(3, 1, 'ACTUALIZAR', 'Cuentas Bancarias', 14, 'Cuenta bancaria actualizada para estudiante ID: 14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:03:05'),
(4, 1, 'EDITAR', 'Estudiantes', 6, 'Estudiante actualizado correctamente. ID=6, Nombre=Manuel mecheba', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:11:55'),
(5, 1, 'CREAR', 'Ciudades', 4, 'Ciudad registrada: Yaunde, País ID: 29, ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:18:16'),
(6, 1, 'CREAR', 'Cuentas Bancarias', 3, 'Cuenta bancaria registrada para Estudiante ID 4, Cuenta ID 3.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:36:52'),
(7, 1, 'CREAR', 'Estudiantes', 38, 'Estudiante registrado. ID: 38, Código: PG-040298-38B', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:43:22'),
(8, 1, 'CREAR', 'Usuarios', 3, 'Usuario registrado: ID 3, Nombre: Trinidad, Email: trinidad345@gmail.com, Rol ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:49:17'),
(9, 1, 'LOGOUT', 'Sesión', NULL, 'El usuario cerró sesión.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:55:17'),
(10, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 16:55:40'),
(11, 1, 'LOGOUT', 'Sesión', NULL, 'El usuario cerró sesión.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:12:42'),
(12, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:14:22'),
(13, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:14:22'),
(14, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:14:37'),
(15, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:19:26'),
(16, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:19:52'),
(17, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:19:52'),
(18, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:19:56'),
(19, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:20:21'),
(20, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:20:23'),
(21, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:35:21'),
(22, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:36:10'),
(23, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:36:10'),
(24, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:36:12'),
(25, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-23 17:36:31'),
(26, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:50:42'),
(27, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:51:01'),
(28, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:51:01'),
(29, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:51:10'),
(30, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:52:00'),
(31, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:52:06'),
(32, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:52:41'),
(33, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:52:46'),
(34, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:55:40'),
(35, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:59:51'),
(36, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:59:51'),
(37, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 15:59:56'),
(38, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:00:10'),
(39, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:00:12'),
(40, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:00:23'),
(41, 1, 'CREAR', 'Usuarios', 4, 'Usuario registrado: ID 4, Nombre: Goretti Angue, Email: gorretiangue@gmail.com, Rol ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:04'),
(42, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:04'),
(43, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:10'),
(44, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:23'),
(45, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:26'),
(46, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:29'),
(47, 1, 'CREAR', 'Universidades', 4, 'Universidad registrada: ID 4, Nombre: universidad complutense de mexico, Ciudad ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:49'),
(48, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:01:49'),
(49, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:07:47'),
(50, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:00'),
(51, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:00'),
(52, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:02'),
(53, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:08'),
(54, 1, 'CREAR', 'Universidades', 5, 'Universidad registrada: ID 5, Nombre: Universidad de Yaunde', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:25'),
(55, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:25'),
(56, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:38'),
(57, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:41'),
(58, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:43'),
(59, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:08:45'),
(60, 1, 'CREAR', 'Ciudades', 5, 'Ciudad registrada: malta, País ID: 17, ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:00'),
(61, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:00'),
(62, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:02'),
(63, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:03'),
(64, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:06'),
(65, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:13'),
(66, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:18'),
(67, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:24'),
(68, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:31'),
(69, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:40'),
(70, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:41'),
(71, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:46'),
(72, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:09:48'),
(73, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:10:21'),
(74, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:10:24'),
(75, 1, 'CREAR', 'Cuentas Bancarias', 4, 'Cuenta bancaria registrada para Estudiante ID 36, Cuenta ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:11:09'),
(76, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:11:09'),
(77, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:17'),
(78, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:30'),
(79, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:30'),
(80, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:34'),
(81, 1, 'CREAR', 'Cuentas Bancarias', 5, 'Cuenta bancaria creada (ID 5) para estudiante ID 15.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:59'),
(82, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:13:59'),
(83, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:03'),
(84, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:17'),
(85, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:17'),
(86, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:20'),
(87, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:21'),
(88, 1, 'ACTUALIZAR', 'Cuentas Bancarias', 15, 'Cuenta bancaria actualizada para estudiante ID: 15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:33'),
(89, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:33'),
(90, 1, 'CREAR', 'Cuentas Bancarias', 6, 'Cuenta bancaria creada (ID 6) para estudiante ID 2.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:57'),
(91, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:16:57'),
(92, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:17:04'),
(93, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:28:31'),
(94, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:31:30'),
(95, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:31:30'),
(96, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:31:35'),
(97, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-24 16:31:42'),
(98, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:41:16'),
(99, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:42:32'),
(100, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:42:32'),
(101, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:42:37'),
(102, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:19'),
(103, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:23'),
(104, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:26'),
(105, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:27'),
(106, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:55'),
(107, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:43:57'),
(108, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:03'),
(109, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:06'),
(110, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:08'),
(111, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:11'),
(112, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:13'),
(113, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:17'),
(114, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:21'),
(115, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:23'),
(116, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:44:33'),
(117, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:48:25'),
(118, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:49:49'),
(119, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:04'),
(120, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:05'),
(121, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:07'),
(122, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:08'),
(123, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:21'),
(124, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:46'),
(125, 1, 'LOGOUT', 'Sesión', NULL, 'El usuario cerró sesión.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:50:54'),
(126, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:51:11'),
(127, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:51:11'),
(128, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:51:15'),
(129, 1, 'LOGOUT', 'Sesión', NULL, 'El usuario cerró sesión.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 08:51:28'),
(130, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:25:55'),
(131, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:25:55'),
(132, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:26:02'),
(133, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:26:05'),
(134, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:26:08'),
(135, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:26:15'),
(136, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:10'),
(137, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:26'),
(138, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:26'),
(139, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:28'),
(140, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:29'),
(141, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:39:32'),
(142, 1, 'LOGOUT', 'Sistema', NULL, 'Cierre de sesión por inactividad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:55:05'),
(143, 1, 'LOGIN', 'Usuarios', 1, 'Inicio de sesión exitoso.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:55:24'),
(144, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:55:24'),
(145, 1, 'VISUALIZAR', 'Estadísticas', NULL, 'Visualización de estadísticas de estudiantes por país', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'EXITO', '2026-02-25 09:55:26');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(192, 'Bielorrusia'),
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pasaportes`
--

INSERT INTO `pasaportes` (`id`, `estudiante_id`, `numero_pasaporte`, `fecha_emision`, `fecha_expiracion`, `archivo_url`, `fecha_subida`) VALUES
(1, 1, 'P12093', '2023-02-08', '2028-05-16', 'pasaporte_1_1745997858.pdf', '2025-04-30 07:24:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'administrador'),
(2, 'tecnico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidades`
--

CREATE TABLE `universidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ciudad_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `universidades`
--

INSERT INTO `universidades` (`id`, `nombre`, `ciudad_id`) VALUES
(1, 'Juan Carlos I', 1),
(2, 'universidad Nacional de Mexico', 2),
(3, 'Universidad nacional de Benín', 3),
(4, 'universidad complutense de mexico', 2),
(5, 'Universidad de Yaunde', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contrasena`, `rol_id`, `creado_en`) VALUES
(1, 'MINERVA GIMENEZ', 'minerva@prueba.com', '$2y$10$duVZG93NwcxWRv4WGhY0mOjE8R5EhIuvAnt6nSgyUbbYrD/Z8p.OW', 1, '2025-04-10 16:43:30'),
(2, 'SALVADOR METE BUJERI', 'salva@prueba.com', '$2y$10$Q0/t9mz9lGJA6lKLimy5GOQiMkmFM3wGkeRmOFe71dJcoxqJo83m6', 2, '2025-07-08 13:32:17'),
(3, 'Trinidad', 'trinidad345@gmail.com', '$2y$10$409X.bfebfWbybCUq/G/Z.DCJ7wX9Y2eCmJP9AuMv0q1uVO3rUJIC', 2, '2026-02-23 15:49:17'),
(4, 'Goretti Angue', 'gorretiangue@gmail.com', '$2y$10$MMcHLv0rtZjZPeNU4vIaw.LurnBtamisTq93W64yKuu4UTjwhpM6K', 2, '2026-02-24 15:01:04');

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
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_acceso` (`codigo_acceso`),
  ADD KEY `fk_estudiante_cuenta` (`cuenta_id`),
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
-- Indices de la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `accion` (`accion`),
  ADD KEY `modulo` (`modulo`),
  ADD KEY `fecha` (`fecha`);

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
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuarios_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anios_academicos`
--
ALTER TABLE `anios_academicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=233;

--
-- AUTO_INCREMENT de la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `universidades`
--
ALTER TABLE `universidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `estudiantes_ibfk_4` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_estudiante_cuenta` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_bancarias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `log_actividades`
--
ALTER TABLE `log_actividades`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

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

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
