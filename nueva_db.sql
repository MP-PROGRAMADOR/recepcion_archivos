-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Tabla de años académicos
CREATE TABLE `anios_academicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `anios_academicos` (`id`, `nombre`) VALUES
(1, '2023-2024'),
(2, '2024-2025');

-- Tabla de países
CREATE TABLE `paises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Aquí debes insertar los países como ya lo hiciste (omitido por espacio)

-- Tabla de ciudades
CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `pais_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de universidades
CREATE TABLE `universidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `ciudad_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de idiomas
CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `meses_duracion` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de estudiantes
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `codigo_acceso` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `pais_id` int(11) NOT NULL,
  `ciudad_id` int(11) DEFAULT NULL,
  `universidad_id` int(11) DEFAULT NULL,
  `idioma_id` int(11) DEFAULT NULL,
  `anio_inicio_carrera` year DEFAULT NULL,
  `anio_fin_carrera` year DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `ruta_foto` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_acceso` (`codigo_acceso`),
  KEY `fk_estudiantes_pais` (`pais_id`),
  KEY `fk_estudiantes_ciudad` (`ciudad_id`),
  KEY `fk_estudiantes_universidad` (`universidad_id`),
  KEY `fk_estudiantes_idioma` (`idioma_id`),
  FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON UPDATE CASCADE,
  FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON UPDATE CASCADE,
  FOREIGN KEY (`universidad_id`) REFERENCES `universidades` (`id`) ON UPDATE CASCADE,
  FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de notas
CREATE TABLE `notas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) DEFAULT NULL,
  `anio_academico_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notas_estudiante` (`estudiante_id`),
  KEY `fk_notas_anio` (`anio_academico_id`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`anio_academico_id`) REFERENCES `anios_academicos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de pasaportes
CREATE TABLE `pasaportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) DEFAULT NULL,
  `numero_pasaporte` varchar(50) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pasaporte_estudiante` (`estudiante_id`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Tabla de configuración del sistema
CREATE TABLE `configuracion` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_sitio` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255),
  `color_primario` VARCHAR(7),
  `descripcion` TEXT,
  `img_estudiante` VARCHAR(255) DEFAULT NULL,
  `img_admin` VARCHAR(255) DEFAULT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

COMMIT;


ALTER TABLE `estudiantes`
ADD COLUMN IF NOT EXISTS `fecha_inicio_carrera` year  NULL,
ADD COLUMN IF NOT EXISTS `ciudad_id` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `universidad_id` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS`idioma_id` int(11) DEFAULT NULL,
ADD COLUMN  IF NOT EXISTS `anio_fin_carrera` year DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `universidad_id` int(11) DEFAULT NULL;