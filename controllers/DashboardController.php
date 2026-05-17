<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class DashboardController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getResumen() {
        return [
            'alumnos' => $this->contar("Alumno"),
            'asignaturas' => $this->contar("Asignatura"),
            'matriculas' => $this->contar("Matricula"),
            'ausenciasHoy' => $this->contarAusenciasHoy(),
            'mediaGeneral' => $this->mediaGeneral(),
        ];
    }

    private function contar($tabla) {
        $result = $this->conexion->realizarConsultaSQL("SELECT COUNT(*) AS total FROM $tabla");
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return intval($row['total']);
    }

    private function contarAusenciasHoy() {
        $stmt = $this->conexion->preparar("SELECT COUNT(*) AS total FROM Asistencia WHERE fecha = CURDATE() AND estado IN ('Ausente','Justificada')");
        if (!$stmt) return 0;
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return intval($row['total']);
    }

    private function mediaGeneral() {
        $result = $this->conexion->realizarConsultaSQL("SELECT AVG(nota) AS media FROM Examen");
        if (!$result) return null;
        $row = $result->fetch_assoc();
        return $row['media'] !== null ? floatval($row['media']) : null;
    }

    public function getClasesHoy() {
        $dia = $this->diaSemanaActual();
        $stmt = $this->conexion->preparar(
            "SELECT h.horaInicio, h.horaFin, h.aula, h.profesor, a.nombre AS asignatura, a.curso " .
            "FROM Horario h JOIN Asignatura a ON a.id = h.idAsignatura " .
            "WHERE h.diaSemana = ? ORDER BY h.horaInicio"
        );
        if (!$stmt) return false;
        $stmt->bind_param("s", $dia);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getAusenciasRecientes($limit = 6) {
        $stmt = $this->conexion->preparar(
            "SELECT asi.fecha, asi.estado, al.nombre, al.apellidos, asig.nombre AS asignatura " .
            "FROM Asistencia asi " .
            "JOIN Alumno al ON al.id = asi.idAlumno " .
            "JOIN Asignatura asig ON asig.id = asi.idAsignatura " .
            "WHERE asi.estado IN ('Ausente','Justificada') " .
            "ORDER BY asi.fecha DESC, asi.id DESC LIMIT ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getAlumnosEnRiesgo($limit = 6) {
        $stmt = $this->conexion->preparar(
            "SELECT al.id, al.nombre, al.apellidos, ex.media, COALESCE(asi.ausencias, 0) AS ausencias " .
            "FROM Alumno al " .
            "LEFT JOIN (SELECT idAlumno, AVG(nota) AS media FROM Examen GROUP BY idAlumno) ex ON ex.idAlumno = al.id " .
            "LEFT JOIN (SELECT idAlumno, COUNT(*) AS ausencias FROM Asistencia WHERE estado = 'Ausente' GROUP BY idAlumno) asi ON asi.idAlumno = al.id " .
            "WHERE ex.media < 5 OR COALESCE(asi.ausencias, 0) >= 3 " .
            "ORDER BY COALESCE(media, 10) ASC, ausencias DESC LIMIT ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    private function diaSemanaActual() {
        $dias = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];
        return $dias[date('l')] ?? 'Lunes';
    }
}
?>
