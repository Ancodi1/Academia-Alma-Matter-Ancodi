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

CALL add_column_if_missing('Examen', 'id', 'ALTER TABLE Examen ADD COLUMN id INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
CALL add_fk_if_missing('Usuario', 'usuario_alumno_fk', 'ALTER TABLE Usuario ADD CONSTRAINT usuario_alumno_fk FOREIGN KEY (idAlumno) REFERENCES Alumno(id) ON DELETE SET NULL ON UPDATE CASCADE');

DROP PROCEDURE add_column_if_missing;
DROP PROCEDURE add_fk_if_missing;
