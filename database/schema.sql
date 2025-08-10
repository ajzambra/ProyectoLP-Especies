-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS biodiversidad
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE biodiversidad;

-- 🌳 Tabla: ecosistemas
CREATE TABLE ecosistemas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  descripcion TEXT,
  clasificacion ENUM('bosque','lago','playa') NOT NULL DEFAULT 'bosque',
  lugar VARCHAR(120),
  imagen_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_ecosistemas_nombre (nombre)
) ENGINE=InnoDB;
