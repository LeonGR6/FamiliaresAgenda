create database citas;

use citas;

CREATE TABLE usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(256) NOT NULL,
  username VARCHAR(256) NOT NULL,
  email VARCHAR(256) NOT NULL,
  password VARCHAR(255) NOT NULL,  -- Longitud ampliada para hashes seguros
  cargo ENUM('Espectador', 'Personal', 'Administrador') NOT NULL DEFAULT 'Espectador',  -- Roles específicos
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unq_username (username),
  UNIQUE KEY unq_email (email),
  INDEX idx_cargo (cargo)  -- Índice para búsquedas por rol
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;




CREATE TABLE reservas (
    ID INT(11) NOT NULL AUTO_INCREMENT,
    numeroCarpeta VARCHAR(50) NOT NULL,
    TipoProcedimiento VARCHAR(50) NOT NULL,
    Fecha  DATE NOT NULL,
    Hora TIME NOT NULL,
    Duracion INT(11),
    Puesto VARCHAR(100),
    Motivo VARCHAR(255),
    Juzgado VARCHAR(20) NOT NULL,
    Observaciones TEXT,
    Estado VARCHAR(20) NOT NULL,
    usuario_id INT(11) NOT NULL,
    FechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ID),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Contacto (
  Id INT AUTO_INCREMENT PRIMARY KEY,
  Descripcion VARCHAR(255) NOT NULL
);

INSERT INTO Contacto (Descripcion) VALUES ('Ingresa tus medios de contacto');