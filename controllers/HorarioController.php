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

    public function getProfesores() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, apellidos FROM Profesor ORDER BY apellidos ASC, nombre ASC");
    }

    public function insertarHorario($idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor, $idProfesor = null) {
        $idProfesor = intval($idProfesor);
        if ($idProfesor > 0) {
            $stmt = $this->conexion->preparar(
                "INSERT INTO Horario (idAsignatura, diaSemana, horaInicio, horaFin, aula, profesor, idProfesor) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) return false;
            $stmt->bind_param("isssssi", $idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor, $idProfesor);
        } else {
            $stmt = $this->conexion->preparar(
                "INSERT INTO Horario (idAsignatura, diaSemana, horaInicio, horaFin, aula, profesor) VALUES (?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) return false;
            $stmt->bind_param("isssss", $idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function tieneSolapamiento($diaSemana, $horaInicio, $horaFin, $aula, $idProfesor = 0) {
        $sql = "SELECT COUNT(*) AS total FROM Horario WHERE diaSemana = ? AND horaInicio < ? AND horaFin > ? AND (aula = ?";
        $types = "ssss";
        $params = [$diaSemana, $horaFin, $horaInicio, $aula];

        if ($idProfesor > 0) {
            $sql .= " OR idProfesor = ?";
            $types .= "i";
            $params[] = intval($idProfesor);
        }

        $sql .= ")";
        $stmt = $this->conexion->preparar($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return intval($row['total']) > 0;
    }

    public function getHorarioSemanal($idAsignatura = 0, $idProfesor = 0, $aula = '') {
        $order = "FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";
        $sql = "SELECT h.id, h.diaSemana, h.horaInicio, h.horaFin, h.aula, h.profesor, a.nombre AS asignatura, a.curso " .
               ", p.nombre AS profesorNombre, p.apellidos AS profesorApellidos " .
               "FROM Horario h " .
               "JOIN Asignatura a ON h.idAsignatura = a.id " .
               "LEFT JOIN Profesor p ON p.id = h.idProfesor";

        $where = [];
        $params = [];
        $types = "";
        if ($idAsignatura > 0) { $where[] = "h.idAsignatura = ?"; $params[] = intval($idAsignatura); $types .= "i"; }
        if ($idProfesor > 0) { $where[] = "h.idProfesor = ?"; $params[] = intval($idProfesor); $types .= "i"; }
        if ($aula !== '') { $where[] = "h.aula LIKE ?"; $params[] = "%" . $aula . "%"; $types .= "s"; }
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY $order, h.horaInicio";

        if (!$types) return $this->conexion->realizarConsultaSQL($sql);
        $stmt = $this->conexion->preparar($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
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
