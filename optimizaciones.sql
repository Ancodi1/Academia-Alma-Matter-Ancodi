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
