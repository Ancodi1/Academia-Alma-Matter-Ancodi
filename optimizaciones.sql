
-- Soporte para recuperación de contraseña
CREATE TABLE IF NOT EXISTS `PasswordReset` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token_hash` (`token_hash`),
  KEY `idx_user_expires` (`user_id`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `nombre` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin','profesor','alumno') NOT NULL DEFAULT 'profesor',
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Usuario admin por defecto (email: admin@academia.com, contraseña: admin). Cambiar en producción.
INSERT INTO `Usuario` (`username`, `email`, `nombre`, `rol`, `password_hash`)
SELECT 'admin', 'admin@academia.com', 'Administrador', 'admin',
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE NOT EXISTS (SELECT 1 FROM `Usuario` WHERE `username` = 'admin');

-- Sistema de notificaciones
CREATE TABLE IF NOT EXISTS `Notificacion` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `mensaje` TEXT NOT NULL,
  `tipo` ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
  `leida` BOOLEAN DEFAULT FALSE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leida_fecha` (`leida`, `fecha_creacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Gestión Académica Avanzada

-- Calendario de exámenes y fechas importantes
CREATE TABLE IF NOT EXISTS `CalendarioAcademico` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `fecha_inicio` DATETIME NOT NULL,
  `fecha_fin` DATETIME,
  `tipo` ENUM('examen', 'entrega', 'evento', 'vacaciones', 'reunion') DEFAULT 'examen',
  `id_asignatura` INT UNSIGNED NULL,
  `id_alumno` INT UNSIGNED NULL,
  `color` VARCHAR(7) DEFAULT '#3b82f6',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fecha_tipo` (`fecha_inicio`, `tipo`),
  FOREIGN KEY (`id_asignatura`) REFERENCES `Asignatura`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_alumno`) REFERENCES `Alumno`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Sistema de asistencia/faltas
CREATE TABLE IF NOT EXISTS `Asistencia` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` INT UNSIGNED NOT NULL,
  `id_asignatura` INT UNSIGNED NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` ENUM('presente', 'falta', 'justificada', 'tardanza') DEFAULT 'presente',
  `observaciones` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_alumno_asignatura_fecha` (`id_alumno`, `id_asignatura`, `fecha`),
  FOREIGN KEY (`id_alumno`) REFERENCES `Alumno`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `Asignatura`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Trabajos y proyectos
CREATE TABLE IF NOT EXISTS `Trabajo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `id_asignatura` INT UNSIGNED NOT NULL,
  `id_alumno` INT UNSIGNED NOT NULL,
  `fecha_asignacion` DATE NOT NULL,
  `fecha_entrega` DATE NOT NULL,
  `fecha_entregado` DATE NULL,
  `archivo_entregado` VARCHAR(500) NULL,
  `nota` DECIMAL(4,2) NULL,
  `comentarios` TEXT,
  `estado` ENUM('asignado', 'entregado', 'calificado', 'atrasado') DEFAULT 'asignado',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_alumno_fecha` (`id_alumno`, `fecha_entrega`),
  KEY `idx_asignatura` (`id_asignatura`),
  FOREIGN KEY (`id_alumno`) REFERENCES `Alumno`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `Asignatura`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Comunicados y avisos
CREATE TABLE IF NOT EXISTS `Comunicado` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `contenido` TEXT NOT NULL,
  `tipo` ENUM('general', 'urgente', 'informacion', 'evento') DEFAULT 'general',
  `destinatarios` ENUM('todos', 'alumnos', 'profesores', 'padres') DEFAULT 'todos',
  `fecha_publicacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` DATETIME NULL,
  `leido_por` JSON NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  `created_by` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fecha_tipo` (`fecha_publicacion`, `tipo`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Historial académico detallado
CREATE TABLE IF NOT EXISTS `HistorialAcademico` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_alumno` INT UNSIGNED NOT NULL,
  `id_asignatura` INT UNSIGNED NOT NULL,
  `periodo` VARCHAR(50) NOT NULL,
  `tipo_evaluacion` ENUM('examen', 'trabajo', 'participacion', 'proyecto', 'practica') NOT NULL,
  `descripcion` VARCHAR(255),
  `fecha` DATE NOT NULL,
  `nota` DECIMAL(4,2) NOT NULL,
  `peso` DECIMAL(3,2) DEFAULT 1.00,
  `comentarios` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_alumno_periodo` (`id_alumno`, `periodo`),
  KEY `idx_asignatura_fecha` (`id_asignatura`, `fecha`),
  FOREIGN KEY (`id_alumno`) REFERENCES `Alumno`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `Asignatura`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;