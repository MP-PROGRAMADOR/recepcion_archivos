-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-04-2025 a las 09:39:36
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10


drop database if exists recepcion_archivo;

create database recepcion_archivo;

use recepcion_archivo;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios_academicos`
--

CREATE TABLE `anios_academicos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




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
  `ruta_foto` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO paises (nombre) VALUES

('Afghanistan'),
('Albania'),
('Algeria'),
('Andorra'),
('Angola'),
('Antigua and Barbuda'),
('Argentina'),
('Armenia'),
('Australia'),
('Austria'),
('Azerbaijan'),
('Bahamas'),
('Bahrain'),
('Bangladesh'),
('Barbados'),
('Belgium'),
('Belize'),
('Benin'),
('Bhutan'),
('Bolivia'),
('Bosnia and Herzegovina'),
('Botswana'),
('Brunei'),
('Bulgaria'),
('Burkina Faso'),
('Burundi'),
('Cabo Verde'),
('Cambodia'),
('Cameroon'),
('Canada'),
('Central African Republic'),
('Chad'),
('Chile'),
('China'),
('Colombia'),
('Comoros'),
('Congo'),
('Costa Rica'),
('Croatia'),
('Cuba'),
('Cyprus'),
('Czech Republic'),
('Denmark'),
('Djibouti'),
('Dominica'),
('Dominican Republic'),
('East Timor'),
('Ecuador'),
('Egypt'),
('El Salvador'),
('Equatorial Guinea'),
('Eritrea'),
('Estonia'),
('Eswatini'),
('Ethiopia'),
('Fiji'),
('Finland'),
('France'),
('Gabon'),
('Gambia'),
('Georgia'),
('Germany'),
('Ghana'),
('Greece'),
('Grenada'),
('Guatemala'),
('Guinea'),
('Guinea-Bissau'),
('Guyana'),
('Haiti'),
('Honduras'),
('Hungary'),
('Iceland'),
('India'),
('Indonesia'),
('Iran'),
('Iraq'),
('Ireland'),
('Israel'),
('Italy'),
('Jamaica'),
('Japan'),
('Jordan'),
('Kazakhstan'),
('Kenya'),
('Kiribati'),
('Korea North'),
('Korea South'),
('Kuwait'),
('Kyrgyzstan'),
('Laos'),
('Latvia'),
('Lebanon'),
('Lesotho'),
('Liberia'),
('Libya'),
('Liechtenstein'),
('Lithuania'),
('Luxembourg'),
('Madagascar'),
('Malawi'),
('Malaysia'),
('Maldives'),
('Mali'),
('Malta'),
('Marshall Islands'),
('Mauritania'),
('Mauritius'),
('Mexico'),
('Micronesia'),
('Moldova'),
('Monaco'),
('Mongolia'),
('Montenegro'),
('Morocco'),
('Mozambique'),
('Namibia'),
('Nauru'),
('Nepal'),
('Netherlands'),
('New Zealand'),
('Nicaragua'),
('Niger'),
('Nigeria'),
('North Macedonia'),
('Norway'),
('Oman'),
('Pakistan'),
('Palau'),
('Panama'),
('Papua New Guinea'),
('Paraguay'),
('Peru'),
('Philippines'),
('Poland'),
('Portugal'),
('Qatar'),
('Romania'),
('Russia'),
('Rwanda'),
('Saint Kitts and Nevis'),
('Saint Lucia'),
('Saint Vincent and the Grenadines'),
('Samoa'),
('San Marino'),
('Sao Tome and Principe'),
('Saudi Arabia'),
('Senegal'),
('Serbia'),
('Seychelles'),
('Sierra Leone'),
('Singapore'),
('Slovakia'),
('Slovenia'),
('Solomon Islands'),
('Somalia'),
('South Africa'),
('South Korea'),
('South Sudan'),
('Spain'),
('Sri Lanka'),
('Sudan'),
('Suriname'),
('Sweden'),
('Switzerland'),
('Syria'),
('Taiwan'),
('Tajikistan'),
('Tanzania'),
('Thailand'),
('Togo'),
('Tonga'),
('Trinidad and Tobago'),
('Tunisia'),
('Turkey'),
('Turkmenistan'),
('Tuvalu'),
('Uganda'),
('Ukraine'),
('United Arab Emirates'),
('United Kingdom'),
('United States'),
('Uruguay'),
('Uzbekistan'),
('Vanuatu'),
('Vatican City'),
('Venezuela'),
('Vietnam'),
('Yemen'),
('Zambia'),
('Zimbabwe');


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
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



--
-- Indices de la tabla `anios_academicos`
--
ALTER TABLE `anios_academicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_acceso` (`codigo_acceso`),
  ADD KEY `fk_estudiantes_pais` (`pais_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_estudiantes_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_notas_anio` FOREIGN KEY (`anio_academico_id`) REFERENCES `anios_academicos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notas_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pasaportes`
--
ALTER TABLE `pasaportes`
  ADD CONSTRAINT `fk_pasaporte_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
