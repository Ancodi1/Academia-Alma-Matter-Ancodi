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

CREATE PROCEDURE add_fk_if_missing(IN table_name_in VARCHAR(64), IN fk_name_in VARCHAR(64), IN ddl_in TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
          AND TABLE_NAME = table_name_in
          AND CONSTRAINT_NAME = fk_name_in
    ) THEN
        SET @ddl = ddl_in;
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

DELIMITER ;

CREATE TABLE IF NOT EXISTS Profesor (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    apellidos VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    especialidad VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CALL add_column_if_missing('Horario', 'idProfesor', 'ALTER TABLE Horario ADD COLUMN idProfesor INT(5) UNSIGNED NULL AFTER idAsignatura');
CALL add_fk_if_missing('Horario', 'horario_profesor_fk', 'ALTER TABLE Horario ADD CONSTRAINT horario_profesor_fk FOREIGN KEY (idProfesor) REFERENCES Profesor(id) ON DELETE SET NULL ON UPDATE CASCADE');

CREATE TABLE IF NOT EXISTS Archivo (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAlumno INT(5) UNSIGNED,
    nombre_archivo VARCHAR(255),
    tipo VARCHAR(50),
    ruta VARCHAR(255),
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE CASCADE
);

DROP PROCEDURE add_column_if_missing;
DROP PROCEDURE add_fk_if_missing;
