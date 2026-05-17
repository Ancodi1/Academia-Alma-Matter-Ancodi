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

DELIMITER ;

ALTER TABLE Usuario MODIFY role ENUM('admin', 'teacher', 'student', 'family') DEFAULT 'teacher';
CALL add_column_if_missing('Usuario', 'idAlumno', 'ALTER TABLE Usuario ADD COLUMN idAlumno INT(5) UNSIGNED NULL AFTER role');

CREATE TABLE IF NOT EXISTS Pago (
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

CREATE TABLE IF NOT EXISTS Tarea (
    id INT(5) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idAsignatura INT(5) UNSIGNED NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fechaEntrega DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAsignatura) REFERENCES Asignatura(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS TareaEntrega (
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

CREATE TABLE IF NOT EXISTS Auditoria (
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

DROP PROCEDURE add_column_if_missing;
