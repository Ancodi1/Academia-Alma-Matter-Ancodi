-- Optimizaciones recomendadas para la base de datos
-- Ejecutar estos comandos en phpMyAdmin para mejorar el rendimiento

-- Índice para búsquedas por nombre y apellidos
ALTER TABLE Alumno ADD INDEX idx_nombre_apellidos (nombre, apellidos);

-- Índice para consultas de exámenes por alumno
ALTER TABLE Examen ADD INDEX idx_alumno_fecha (idAlumno, fecha);

-- Índice para consultas de exámenes por asignatura
ALTER TABLE Examen ADD INDEX idx_asignatura (idAsignatura);

-- Índice para búsquedas por nombre de asignatura
ALTER TABLE Asignatura ADD INDEX idx_nombre (nombre);

-- Verificar que las claves foráneas estén bien configuradas
-- (Ya están en el archivo original, pero es bueno verificar)

-- ==== Autenticación: tabla de usuarios (si no existe) ====
CREATE TABLE IF NOT EXISTS `Usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `nombre` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin','profesor','alumno') NOT NULL DEFAULT 'profesor',
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Usuario admin por defecto (contraseña: admin123). Cambiar en producción.
INSERT INTO `Usuario` (`username`, `nombre`, `rol`, `password_hash`)
SELECT 'admin', 'Administrador', 'admin',
       '$2y$10$Z3f3n7n9h5k3Qe1QJQ9yEez8i8pKxY7wYw3pKfM0S1Y0qFZ3m9vU.'
WHERE NOT EXISTS (SELECT 1 FROM `Usuario` WHERE `username` = 'admin');