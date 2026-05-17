<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class MatriculaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getAlumnos() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, apellidos FROM Alumno ORDER BY apellidos ASC, nombre ASC");
    }

    public function getAsignaturas() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, curso FROM Asignatura ORDER BY curso ASC, nombre ASC");
    }

    public function getAsignaturasDeAlumno($idAlumno) {
        $stmt = $this->conexion->preparar(
            "SELECT m.id, m.fechaAlta, m.estado, a.id AS idAsignatura, a.nombre, a.curso " .
            "FROM Matricula m JOIN Asignatura a ON a.id = m.idAsignatura " .
            "WHERE m.idAlumno = ? ORDER BY a.curso ASC, a.nombre ASC"
        );
        if (!$stmt) return false;
        $id = intval($idAlumno);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getAlumnosPorAsignatura($idAsignatura) {
        $stmt = $this->conexion->preparar(
            "SELECT al.id, al.nombre, al.apellidos, al.email " .
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

    public function guardarMatriculasAlumno($idAlumno, $idsAsignaturas) {
        $idAlumno = intval($idAlumno);
        $idsAsignaturas = array_values(array_unique(array_map('intval', $idsAsignaturas)));

        $stmt = $this->conexion->preparar("DELETE FROM Matricula WHERE idAlumno = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $idAlumno);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        if (!$idsAsignaturas) return true;

        $stmt = $this->conexion->preparar("INSERT INTO Matricula (idAlumno, idAsignatura, fechaAlta, estado) VALUES (?, ?, CURDATE(), 'Activa')");
        if (!$stmt) return false;

        foreach ($idsAsignaturas as $idAsignatura) {
            if ($idAsignatura <= 0) continue;
            $stmt->bind_param("ii", $idAlumno, $idAsignatura);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
        }

        $stmt->close();
        return true;
    }
}
?>
