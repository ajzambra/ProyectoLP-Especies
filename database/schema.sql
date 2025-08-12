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

-- Tabla para almacenar las Especies
CREATE TABLE especies (
    id_especie INT PRIMARY KEY AUTO_INCREMENT,
    id_ecosistema INT,
    nombre_comun VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo ENUM('Flora', 'Fauna') NOT NULL, -- El tipo de especie puede ser 'Flora' o 'Fauna' 
    imagen_url VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Definición de la llave foránea para relacionar con la tabla ecosistemas
    FOREIGN KEY (id_ecosistema) REFERENCES ecosistemas(id) ON DELETE SET NULL
);
