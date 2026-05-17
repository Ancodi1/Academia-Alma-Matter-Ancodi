<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AlumnoController {
    private $conexion;
    
    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getConexion() {
        return $this->conexion;
    }
    
    public function getTodosLosAlumnos() {
        $sql = "SELECT id, nombre, apellidos, edad, email, telefono, curso_actual FROM Alumno ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getAlumnoPorId($id) {
        $stmt = $this->conexion->preparar("SELECT * FROM Alumno WHERE id = ?");
        if (!$stmt) return false;
        $idInt = intval($id);
        $stmt->bind_param("i", $idInt);
        $stmt->execute();
        $result = $stmt->get_result();
        $alumno = $result->fetch_assoc();
        $stmt->close();
        return $alumno;
    }

    public function getAlumnosPorFiltro($curso = '', $idAsignatura = 0) {
        if (!$curso && !$idAsignatura) {
            return $this->getTodosLosAlumnos();
        }

        $sql = "SELECT DISTINCT al.id, al.nombre, al.apellidos, al.edad, al.email " .
               "FROM Alumno al " .
               "LEFT JOIN Examen ex ON ex.idAlumno = al.id " .
               "LEFT JOIN Asistencia asi ON asi.idAlumno = al.id " .
               "LEFT JOIN Asignatura asgEx ON asgEx.id = ex.idAsignatura " .
               "LEFT JOIN Asignatura asgAsi ON asgAsi.id = asi.idAsignatura";

        $where = [];
        $params = [];
        $types = "";

        if ($curso) {
            $where[] = "(asgEx.curso = ? OR asgAsi.curso = ?)";
            $types .= "ss";
            $params[] = $curso;
            $params[] = $curso;
        }

        if ($idAsignatura) {
            $where[] = "(asgEx.id = ? OR asgAsi.id = ?)";
            $types .= "ii";
            $params[] = intval($idAsignatura);
            $params[] = intval($idAsignatura);
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY al.nombre ASC";
        $stmt = $this->conexion->preparar($sql);
        if (!$stmt) return false;

        if ($types) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function buscarAlumnos($termino = '', $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        
        if ($termino) {
            $stmt = $this->conexion->preparar("SELECT * FROM Alumno WHERE nombre LIKE ? OR apellidos LIKE ? OR email LIKE ? ORDER BY nombre ASC LIMIT ? OFFSET ?");
            if (!$stmt) return false;
            $terminoLike = "%$termino%";
            $stmt->bind_param("sssii", $terminoLike, $terminoLike, $terminoLike, $porPagina, $offset);
        } else {
            $stmt = $this->conexion->preparar("SELECT * FROM Alumno ORDER BY nombre ASC LIMIT ? OFFSET ?");
            if (!$stmt) return false;
            $stmt->bind_param("ii", $porPagina, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function contarAlumnos($termino = '') {
        if ($termino) {
            $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Alumno WHERE nombre LIKE ? OR apellidos LIKE ? OR email LIKE ?");
            if (!$stmt) return 0;
            $terminoLike = "%$termino%";
            $stmt->bind_param("sss", $terminoLike, $terminoLike, $terminoLike);
        } else {
            $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Alumno");
            if (!$stmt) return 0;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
    
    public function getNumeroExamenes($idAlumno) {
        $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Examen WHERE idAlumno = ?");
        if (!$stmt) return 0;
        $idInt = intval($idAlumno);
        $stmt->bind_param("i", $idInt);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    public function getExamenesPorAlumno($idAlumno) {
        $stmt = $this->conexion->preparar("SELECT e.id, e.idAlumno, e.idAsignatura, e.fecha, e.nota, a.nombre AS asignatura
                FROM Examen e
                LEFT JOIN Asignatura a ON a.id = e.idAsignatura
                WHERE e.idAlumno = ?
                ORDER BY e.fecha DESC, e.idAsignatura DESC");
        if (!$stmt) return false;
        $idInt = intval($idAlumno);
        $stmt->bind_param("i", $idInt);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function actualizarExamenPorId($idExamen, $nuevaFecha, $nuevaNota) {
        $stmt = $this->conexion->preparar("UPDATE Examen SET fecha = ?, nota = ? WHERE id = ?");
        if (!$stmt) return false;
        $idExamenInt = intval($idExamen);
        $notaVal = is_numeric($nuevaNota) ? floatval($nuevaNota) : 0.0;
        $stmt->bind_param("sdi", $nuevaFecha, $notaVal, $idExamenInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizarExamen($idAlumno, $idAsignatura, $fechaOriginal, $nuevaFecha, $nuevaNota) {
        $stmt = $this->conexion->preparar("UPDATE Examen SET fecha = ?, nota = ? WHERE idAlumno = ? AND idAsignatura = ? AND fecha = ?");
        if (!$stmt) return false;
        $idAlumnoInt = intval($idAlumno);
        $idAsignaturaInt = intval($idAsignatura);
        $notaVal = is_numeric($nuevaNota) ? floatval($nuevaNota) : 0.0;
        $stmt->bind_param("sdiis", $nuevaFecha, $notaVal, $idAlumnoInt, $idAsignaturaInt, $fechaOriginal);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminarExamen($idAlumno, $idAsignatura, $fecha) {
        $stmt = $this->conexion->preparar("DELETE FROM Examen WHERE idAlumno = ? AND idAsignatura = ? AND fecha = ?");
        if (!$stmt) return false;
        $idAlumnoInt = intval($idAlumno);
        $idAsignaturaInt = intval($idAsignatura);
        $stmt->bind_param("iis", $idAlumnoInt, $idAsignaturaInt, $fecha);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminarExamenPorId($idExamen) {
        $stmt = $this->conexion->preparar("DELETE FROM Examen WHERE id = ?");
        if (!$stmt) return false;
        $idExamenInt = intval($idExamen);
        $stmt->bind_param("i", $idExamenInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    
    public function modificarAlumno($id, $nombre, $apellidos, $edad, $email = null, $telefono = null, $direccion = null, $tutor = null, $contactoEmergencia = null, $centro = null, $cursoActual = null, $fechaAlta = null, $observaciones = null) {
        $stmt = $this->conexion->preparar(
            "UPDATE Alumno SET nombre = ?, apellidos = ?, edad = ?, email = ?, telefono = ?, direccion = ?, tutor = ?, contacto_emergencia = ?, centro = ?, curso_actual = ?, fecha_alta = ?, observaciones = ? WHERE id = ?"
        );
        if (!$stmt) return false;
        $idInt = intval($id);
        $edadInt = intval($edad);
        $fechaAlta = $fechaAlta ?: date('Y-m-d');
        $stmt->bind_param("ssisssssssssi", $nombre, $apellidos, $edadInt, $email, $telefono, $direccion, $tutor, $contactoEmergencia, $centro, $cursoActual, $fechaAlta, $observaciones, $idInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    
    public function eliminarAlumno($id) {
        $stmt = $this->conexion->preparar("DELETE FROM Alumno WHERE id = ?");
        if (!$stmt) return false;
        $idInt = intval($id);
        $stmt->bind_param("i", $idInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    
    public function agregarAlumno($nombre, $apellidos, $edad, $email = null, $telefono = null, $direccion = null, $tutor = null, $contactoEmergencia = null, $centro = null, $cursoActual = null, $fechaAlta = null, $observaciones = null) {
        $stmt = $this->conexion->preparar(
            "INSERT INTO Alumno (nombre, apellidos, edad, email, telefono, direccion, tutor, contacto_emergencia, centro, curso_actual, fecha_alta, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return false;
        $edadInt = intval($edad);
        $fechaAlta = $fechaAlta ?: date('Y-m-d');
        $stmt->bind_param("ssisssssssss", $nombre, $apellidos, $edadInt, $email, $telefono, $direccion, $tutor, $contactoEmergencia, $centro, $cursoActual, $fechaAlta, $observaciones);
        $ok = $stmt->execute();
        $id = $this->conexion->getInsertId();
        $stmt->close();
        return $ok ? $id : false;
    }

    public function ponerNotaExamen($idAsignatura, $idAlumno, $fecha, $nota) {
        $stmt = $this->conexion->preparar("INSERT INTO Examen (idAsignatura, idAlumno, fecha, nota) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $idAsignaturaInt = intval($idAsignatura);
        $idAlumnoInt = intval($idAlumno);
        $notaVal = is_numeric($nota) ? floatval($nota) : 0.0;
        $stmt->bind_param("iisd", $idAsignaturaInt, $idAlumnoInt, $fecha, $notaVal);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getTodasLasAsignaturas() {
        $sql = "SELECT id, nombre FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getResumenAlumno($idAlumno) {
        $stmt = $this->conexion->preparar(
            "SELECT " .
            "(SELECT AVG(nota) FROM Examen WHERE idAlumno = ?) AS media, " .
            "(SELECT COUNT(*) FROM Examen WHERE idAlumno = ?) AS examenes, " .
            "(SELECT COUNT(*) FROM Asistencia WHERE idAlumno = ? AND estado = 'Ausente') AS ausencias, " .
            "(SELECT COUNT(*) FROM Asistencia WHERE idAlumno = ? AND estado = 'Justificada') AS justificadas"
        );
        if (!$stmt) return false;
        $idInt = intval($idAlumno);
        $stmt->bind_param("iiii", $idInt, $idInt, $idInt, $idInt);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function getAsistenciasPorAlumno($idAlumno, $limit = 10) {
        $stmt = $this->conexion->preparar(
            "SELECT asi.fecha, asi.estado, asig.nombre AS asignatura, asig.curso " .
            "FROM Asistencia asi JOIN Asignatura asig ON asig.id = asi.idAsignatura " .
            "WHERE asi.idAlumno = ? ORDER BY asi.fecha DESC, asi.id DESC LIMIT ?"
        );
        if (!$stmt) return false;
        $idInt = intval($idAlumno);
        $limitInt = intval($limit);
        $stmt->bind_param("ii", $idInt, $limitInt);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
