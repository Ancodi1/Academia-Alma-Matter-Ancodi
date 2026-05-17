<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AsistenciaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function getAlumnos() {
        $sql = "SELECT id, nombre, apellidos FROM Alumno ORDER BY apellidos ASC, nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getAsignaturas() {
        $sql = "SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getAlumnosMatriculados($idAsignatura) {
        $stmt = $this->conexion->preparar(
            "SELECT al.id, al.nombre, al.apellidos " .
            "FROM Matricula m JOIN Alumno al ON al.id = m.idAlumno " .
            "WHERE m.idAsignatura = ? AND m.estado = 'Activa' " .
            "ORDER BY al.apellidos ASC, al.nombre ASC"
        );
        if (!$stmt) return false;
        $id = intval($idAsignatura);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function insertarAsistencia($idAlumno, $idAsignatura, $fecha, $estado) {
        $stmt = $this->conexion->preparar(
            "INSERT INTO Asistencia (idAlumno, idAsignatura, fecha, estado) VALUES (?, ?, ?, ?) " .
            "ON DUPLICATE KEY UPDATE estado = VALUES(estado)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("iiss", $idAlumno, $idAsignatura, $fecha, $estado);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function registrarAsistenciaEnBloque($idAsignatura, $fecha, $estados) {
        if (!$estados || !is_array($estados)) return false;

        $stmt = $this->conexion->preparar(
            "INSERT INTO Asistencia (idAlumno, idAsignatura, fecha, estado) VALUES (?, ?, ?, ?) " .
            "ON DUPLICATE KEY UPDATE estado = VALUES(estado)"
        );
        if (!$stmt) return false;

        $idAsignatura = intval($idAsignatura);
        foreach ($estados as $idAlumno => $estado) {
            $idAlumno = intval($idAlumno);
            if ($idAlumno <= 0 || !in_array($estado, ['Presente', 'Ausente', 'Justificada'])) {
                continue;
            }
            $stmt->bind_param("iiss", $idAlumno, $idAsignatura, $fecha, $estado);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
        }

        $stmt->close();
        return true;
    }

    public function getUltimasAsistencias($limit = 5) {
        $stmt = $this->conexion->preparar(
            "SELECT a.id, al.nombre, al.apellidos, asig.nombre AS asignatura, a.fecha, a.estado " .
            "FROM Asistencia a " .
            "JOIN Alumno al ON a.idAlumno = al.id " .
            "JOIN Asignatura asig ON a.idAsignatura = asig.id " .
            "ORDER BY a.fecha DESC LIMIT ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
