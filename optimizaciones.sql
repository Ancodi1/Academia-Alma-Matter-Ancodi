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
INSERT INTO Usuario (username, password, role) VALUES ('admin', '$2y$10$FNpZrTMTBkcwi5wkuRwBReYglkjU..3KQlZRB1rEqPo/IgYfNzI5y', 'admin');

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

-- Nuevas tablas relacionadas a asistencia y horarios
CREATE TABLE IF NOT EXISTS Horario (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    diaSemana VARCHAR(20) NOT NULL,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    aula VARCHAR(50) NOT NULL,
    profesor VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Asistencia (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED NOT NULL,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('Presente','Ausente','Justificada') NOT NULL DEFAULT 'Presente',
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

-- Índices para asistencia y horarios
ALTER TABLE Horario ADD INDEX idx_horario_asignatura (idAsignatura);
ALTER TABLE Asistencia ADD INDEX idx_asistencia_alumno (idAlumno), ADD INDEX idx_asistencia_asignatura (idAsignatura);
