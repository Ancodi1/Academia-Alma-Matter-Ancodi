<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AsistenciaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getAlumnos() {
        $sql = "SELECT id, nombre, apellidos FROM Alumno ORDER BY apellidos ASC, nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getAsignaturas() {
        $sql = "SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function insertarAsistencia($idAlumno, $idAsignatura, $fecha, $estado) {
        $stmt = $this->conexion->preparar("INSERT INTO Asistencia (idAlumno, idAsignatura, fecha, estado) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("iiss", $idAlumno, $idAsignatura, $fecha, $estado);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
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