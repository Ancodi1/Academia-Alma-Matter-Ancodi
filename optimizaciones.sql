-- Optimizaciones recomendadas para la base de datos
-- Ejecutar estos comandos en phpMyAdmin para mejorar el rendimiento

-- Índice para búsquedas por nombre y apellidos
ALTER TABLE Alumno ADD INDEX idx_nombre_apellidos (nombre, apellidos);

-- Índice para consultas de exámenes por alumno
ALTER TABLE Examen ADD COLUMN id INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE Examen ADD INDEX idx_alumno_fecha (idAlumno, fecha);

-- Índice para consultas de exámenes por asignatura
ALTER TABLE Examen ADD INDEX idx_asignatura (idAsignatura);

-- Índice para búsquedas por nombre de asignatura
ALTER TABLE Asignatura ADD INDEX idx_nombre (nombre);

-- Verificar que las claves foráneas estén bien configuradas
-- (Ya están en el archivo original, pero es bueno verificar)

-- Nuevas mejoras: autenticación y campos adicionales

-- Añadir datos ampliados a Alumno
ALTER TABLE Alumno ADD COLUMN email VARCHAR(255) AFTER edad;
ALTER TABLE Alumno ADD COLUMN telefono VARCHAR(30) AFTER email;
ALTER TABLE Alumno ADD COLUMN direccion VARCHAR(255) AFTER telefono;
ALTER TABLE Alumno ADD COLUMN tutor VARCHAR(255) AFTER direccion;
ALTER TABLE Alumno ADD COLUMN contacto_emergencia VARCHAR(255) AFTER tutor;
ALTER TABLE Alumno ADD COLUMN centro VARCHAR(255) AFTER contacto_emergencia;
ALTER TABLE Alumno ADD COLUMN curso_actual VARCHAR(100) AFTER centro;
ALTER TABLE Alumno ADD COLUMN fecha_alta DATE DEFAULT (CURRENT_DATE) AFTER curso_actual;
ALTER TABLE Alumno ADD COLUMN observaciones TEXT AFTER fecha_alta;

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

-- Crear tabla de matrículas alumno-asignatura
CREATE TABLE Matricula (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED NOT NULL,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    fechaAlta DATE NOT NULL DEFAULT (CURRENT_DATE),
    estado ENUM('Activa', 'Baja') NOT NULL DEFAULT 'Activa',
    UNIQUE KEY uniq_matricula (idAlumno, idAsignatura),
    INDEX idx_matricula_alumno (idAlumno),
    INDEX idx_matricula_asignatura (idAsignatura),
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

-- Profesores y enlace con horarios
CREATE TABLE Profesor (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    apellidos VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    especialidad VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE Horario ADD COLUMN idProfesor INT(5) UNSIGNED NULL AFTER idAsignatura;
ALTER TABLE Horario ADD CONSTRAINT horario_profesor_fk FOREIGN KEY (idProfesor) REFERENCES Profesor(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- Módulos avanzados: portales, pagos, tareas y auditoría
ALTER TABLE Usuario MODIFY role ENUM('admin', 'teacher', 'student', 'family') DEFAULT 'teacher';
ALTER TABLE Usuario ADD COLUMN idAlumno INT(5) UNSIGNED NULL AFTER role;
ALTER TABLE Usuario ADD CONSTRAINT usuario_alumno_fk FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE Pago (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    importe DECIMAL(10,2) NOT NULL,
    fechaVencimiento DATE NOT NULL,
    fechaPago DATE DEFAULT NULL,
    estado ENUM('Pendiente', 'Pagado', 'Vencido') NOT NULL DEFAULT 'Pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pago_alumno (idAlumno),
    INDEX idx_pago_estado (estado),
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE
);

CREATE TABLE Tarea (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fechaEntrega DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

CREATE TABLE TareaEntrega (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idTarea INT(5) UNSIGNED NOT NULL,
    idAlumno INT(5) UNSIGNED NOT NULL,
    estado ENUM('Pendiente', 'Entregada', 'Revisada') NOT NULL DEFAULT 'Pendiente',
    comentario TEXT,
    fechaActualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_tarea_alumno (idTarea, idAlumno),
    FOREIGN KEY (idTarea) REFERENCES Tarea(id) ON DELETE CASCADE,
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE
);

CREATE TABLE Auditoria (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT(5) UNSIGNED NULL,
    accion VARCHAR(80) NOT NULL,
    entidad VARCHAR(80) NOT NULL,
    entidadId INT(5) UNSIGNED NULL,
    detalle TEXT,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_auditoria_usuario (idUsuario),
    INDEX idx_auditoria_entidad (entidad, entidadId)
);

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
ALTER TABLE Asistencia ADD UNIQUE KEY uniq_asistencia_dia (idAlumno, idAsignatura, fecha);

INSERT IGNORE INTO Matricula (idAlumno, idAsignatura, fechaAlta, estado)
SELECT DISTINCT idAlumno, idAsignatura, CURDATE(), 'Activa' FROM Examen;

INSERT IGNORE INTO Matricula (idAlumno, idAsignatura, fechaAlta, estado)
SELECT DISTINCT idAlumno, idAsignatura, CURDATE(), 'Activa' FROM Asistencia;
