DROP DATABASE IF EXISTS recepcion_archivo;
CREATE DATABASE recepcion_archivo CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE recepcion_archivo;

CREATE TABLE paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE anios_academicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) UNIQUE
) ENGINE = InnoDB;

CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100),
    codigo_acceso VARCHAR(20) UNIQUE,
    fecha_nacimiento DATE,
    pais_id INT NOT NULL,
    ruta_foto VARCHAR(255),
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_estudiantes_pais FOREIGN KEY (pais_id)
        REFERENCES paises(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT,
    anio_academico_id INT,
    observaciones TEXT,
    archivo_url VARCHAR(255),
    fecha_subida TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notas_estudiante FOREIGN KEY (estudiante_id)
        REFERENCES estudiantes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_notas_anio FOREIGN KEY (anio_academico_id)
        REFERENCES anios_academicos(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE pasaportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT,
    numero_pasaporte VARCHAR(50),
    fecha_emision DATE,
    fecha_expiracion DATE,
    archivo_url VARCHAR(255),
    fecha_subida TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pasaporte_estudiante FOREIGN KEY (estudiante_id)
        REFERENCES estudiantes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    contrasena VARCHAR(255),
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;
