<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class HorarioController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getAsignaturas() {
        $sql = "SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function insertarHorario($idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor) {
        $stmt = $this->conexion->preparar(
            "INSERT INTO Horario (idAsignatura, diaSemana, horaInicio, horaFin, aula, profesor) VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("isssss", $idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getHorarioSemanal() {
        $order = "FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";
        $sql = "SELECT h.id, h.diaSemana, h.horaInicio, h.horaFin, h.aula, h.profesor, a.nombre AS asignatura, a.curso " .
               "FROM Horario h " .
               "JOIN Asignatura a ON h.idAsignatura = a.id " .
               "ORDER BY $order, h.horaInicio";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getProximasClases($limit = 4) {
        $order = "FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";
        $stmt = $this->conexion->preparar(
            "SELECT h.id, h.diaSemana, h.horaInicio, h.horaFin, h.aula, a.nombre AS asignatura, a.curso " .
            "FROM Horario h " .
            "JOIN Asignatura a ON h.idAsignatura = a.id " .
            "ORDER BY $order, h.horaInicio LIMIT ?"
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