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

-- Nuevas mejoras: autenticación y campos adicionales

-- Añadir email a Alumno
ALTER TABLE Alumno ADD COLUMN email VARCHAR(255) AFTER edad;

-- Crear tabla de usuarios para autenticación
CREATE TABLE Usuario (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') DEFAULT 'teacher',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario admin por defecto (password: admin123)
INSERT INTO Usuario (username, password, role) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Crear tabla para archivos subidos
CREATE TABLE Archivo (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED,
    nombre_archivo VARCHAR(255),
    tipo VARCHAR(50),
    ruta VARCHAR(255),
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE
);
