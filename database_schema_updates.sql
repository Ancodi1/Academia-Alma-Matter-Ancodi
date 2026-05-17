DELIMITER //

CREATE PROCEDURE add_column_if_missing(IN table_name_in VARCHAR(64), IN column_name_in VARCHAR(64), IN ddl_in TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = table_name_in
          AND COLUMN_NAME = column_name_in
    ) THEN
        SET @ddl = ddl_in;
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

CREATE PROCEDURE add_index_if_missing(IN table_name_in VARCHAR(64), IN index_name_in VARCHAR(64), IN ddl_in TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = table_name_in
          AND INDEX_NAME = index_name_in
    ) THEN
        SET @ddl = ddl_in;
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

DELIMITER ;

CALL add_column_if_missing('Alumno', 'email', 'ALTER TABLE Alumno ADD COLUMN email VARCHAR(255) AFTER edad');
CALL add_column_if_missing('Alumno', 'telefono', 'ALTER TABLE Alumno ADD COLUMN telefono VARCHAR(30) AFTER email');
CALL add_column_if_missing('Alumno', 'direccion', 'ALTER TABLE Alumno ADD COLUMN direccion VARCHAR(255) AFTER telefono');
CALL add_column_if_missing('Alumno', 'tutor', 'ALTER TABLE Alumno ADD COLUMN tutor VARCHAR(255) AFTER direccion');
CALL add_column_if_missing('Alumno', 'contacto_emergencia', 'ALTER TABLE Alumno ADD COLUMN contacto_emergencia VARCHAR(255) AFTER tutor');
CALL add_column_if_missing('Alumno', 'centro', 'ALTER TABLE Alumno ADD COLUMN centro VARCHAR(255) AFTER contacto_emergencia');
CALL add_column_if_missing('Alumno', 'curso_actual', 'ALTER TABLE Alumno ADD COLUMN curso_actual VARCHAR(100) AFTER centro');
CALL add_column_if_missing('Alumno', 'fecha_alta', 'ALTER TABLE Alumno ADD COLUMN fecha_alta DATE NULL AFTER curso_actual');
CALL add_column_if_missing('Alumno', 'observaciones', 'ALTER TABLE Alumno ADD COLUMN observaciones TEXT AFTER fecha_alta');

CREATE TABLE IF NOT EXISTS Usuario (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') DEFAULT 'teacher',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Usuario (username, password, role)
SELECT 'admin', '$2y$10$FNpZrTMTBkcwi5wkuRwBReYglkjU..3KQlZRB1rEqPo/IgYfNzI5y', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM Usuario WHERE username = 'admin');

CREATE TABLE IF NOT EXISTS Archivo (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED,
    nombre_archivo VARCHAR(255),
    tipo VARCHAR(50),
    ruta VARCHAR(255),
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Matricula (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED NOT NULL,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    fechaAlta DATE NOT NULL,
    estado ENUM('Activa', 'Baja') NOT NULL DEFAULT 'Activa',
    UNIQUE KEY uniq_matricula (idAlumno, idAsignatura),
    INDEX idx_matricula_alumno (idAlumno),
    INDEX idx_matricula_asignatura (idAsignatura),
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

CALL add_index_if_missing('Asistencia', 'uniq_asistencia_dia', 'ALTER TABLE Asistencia ADD UNIQUE KEY uniq_asistencia_dia (idAlumno, idAsignatura, fecha)');

INSERT IGNORE INTO Matricula (idAlumno, idAsignatura, fechaAlta, estado)
SELECT DISTINCT idAlumno, idAsignatura, CURDATE(), 'Activa' FROM Examen;

INSERT IGNORE INTO Matricula (idAlumno, idAsignatura, fechaAlta, estado)
SELECT DISTINCT idAlumno, idAsignatura, CURDATE(), 'Activa' FROM Asistencia;

DROP PROCEDURE add_column_if_missing;
DROP PROCEDURE add_index_if_missing;
