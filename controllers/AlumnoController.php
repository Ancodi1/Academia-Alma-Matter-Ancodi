<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AlumnoController {
    private $conexion;
    
    public function __construct() {
        $this->conexion = new mysqlConn();
    }
    
    public function getTodosLosAlumnos() {
        $sql = "SELECT id, nombre, apellidos, edad FROM Alumno ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function exportarAlumnosCSV() {
        $stmt = $this->conexion->preparar("SELECT id, nombre, apellidos, edad FROM Alumno ORDER BY nombre ASC");
        if (!$stmt) return false;
        $stmt->execute();
        $result = $stmt->get_result();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=alumnos.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nombre', 'Apellidos', 'Edad']);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [$row['id'], $row['nombre'], $row['apellidos'], $row['edad']]);
        }
        fclose($output);
        $stmt->close();
        return true;
    }

    public function buscarAlumnos($termino = '', $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        
        if ($termino) {
            $stmt = $this->conexion->preparar("SELECT id, nombre, apellidos, edad FROM Alumno WHERE nombre LIKE ? OR apellidos LIKE ? ORDER BY nombre ASC LIMIT ? OFFSET ?");
            if (!$stmt) return false;
            $terminoLike = "%$termino%";
            $stmt->bind_param("ssii", $terminoLike, $terminoLike, $porPagina, $offset);
        } else {
            $stmt = $this->conexion->preparar("SELECT id, nombre, apellidos, edad FROM Alumno ORDER BY nombre ASC LIMIT ? OFFSET ?");
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
            $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Alumno WHERE nombre LIKE ? OR apellidos LIKE ?");
            if (!$stmt) return 0;
            $terminoLike = "%$termino%";
            $stmt->bind_param("ss", $terminoLike, $terminoLike);
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
        $stmt = $this->conexion->preparar("SELECT e.idAlumno, e.idAsignatura, e.fecha, e.nota, a.nombre AS asignatura
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

    public function exportarExamenesAlumnoCSV($idAlumno) {
        $stmt = $this->conexion->preparar("SELECT a.nombre AS asignatura, e.fecha, e.nota
                FROM Examen e
                LEFT JOIN Asignatura a ON a.id = e.idAsignatura
                WHERE e.idAlumno = ?
                ORDER BY e.fecha DESC, e.idAsignatura DESC");
        if (!$stmt) return false;
        $idInt = intval($idAlumno);
        $stmt->bind_param("i", $idInt);
        $stmt->execute();
        $result = $stmt->get_result();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=examenes_alumno_' . $idInt . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Asignatura', 'Fecha', 'Nota']);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [$row['asignatura'], $row['fecha'], $row['nota']]);
        }
        fclose($output);
        $stmt->close();
        return true;
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
    
    public function modificarAlumno($id, $nombre, $apellidos, $edad) {
        $stmt = $this->conexion->preparar("UPDATE Alumno SET nombre = ?, apellidos = ?, edad = ? WHERE id = ?");
        if (!$stmt) return false;
        $idInt = intval($id);
        $edadInt = intval($edad);
        $stmt->bind_param("ssii", $nombre, $apellidos, $edadInt, $idInt);
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
    
    public function agregarAlumno($nombre, $apellidos, $edad) {
        $stmt = $this->conexion->preparar("INSERT INTO Alumno (nombre, apellidos, edad) VALUES (?, ?, ?)");
        if (!$stmt) return false;
        $edadInt = intval($edad);
        $stmt->bind_param("ssi", $nombre, $apellidos, $edadInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
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
}
?>
