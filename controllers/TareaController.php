<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class TareaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getAsignaturas() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, curso FROM Asignatura ORDER BY curso ASC, nombre ASC");
    }

    public function getTareas($idAsignatura = 0) {
        $sql = "SELECT t.*, a.nombre AS asignatura, a.curso FROM Tarea t JOIN Asignatura a ON a.id = t.idAsignatura";
        if ($idAsignatura > 0) {
            $stmt = $this->conexion->preparar($sql . " WHERE t.idAsignatura = ? ORDER BY t.fechaEntrega DESC");
            if (!$stmt) return false;
            $idAsignatura = intval($idAsignatura);
            $stmt->bind_param("i", $idAsignatura);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
        return $this->conexion->realizarConsultaSQL($sql . " ORDER BY t.fechaEntrega DESC");
    }

    public function crear($idAsignatura, $titulo, $descripcion, $fechaEntrega) {
        $stmt = $this->conexion->preparar("INSERT INTO Tarea (idAsignatura, titulo, descripcion, fechaEntrega) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $idAsignatura = intval($idAsignatura);
        $stmt->bind_param("isss", $idAsignatura, $titulo, $descripcion, $fechaEntrega);
        $ok = $stmt->execute();
        $id = $this->conexion->getInsertId();
        $stmt->close();
        return $ok ? $id : false;
    }

    public function actualizarEntrega($idTarea, $idAlumno, $estado, $comentario = '') {
        $stmt = $this->conexion->preparar(
            "INSERT INTO TareaEntrega (idTarea, idAlumno, estado, comentario, fechaActualizacion) VALUES (?, ?, ?, ?, NOW()) " .
            "ON DUPLICATE KEY UPDATE estado = VALUES(estado), comentario = VALUES(comentario), fechaActualizacion = NOW()"
        );
        if (!$stmt) return false;
        $idTarea = intval($idTarea);
        $idAlumno = intval($idAlumno);
        $stmt->bind_param("iiss", $idTarea, $idAlumno, $estado, $comentario);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getEntregas($idTarea) {
        $stmt = $this->conexion->preparar(
            "SELECT al.id, al.nombre, al.apellidos, te.estado, te.comentario, te.fechaActualizacion " .
            "FROM Matricula m JOIN Alumno al ON al.id = m.idAlumno " .
            "JOIN Tarea t ON t.idAsignatura = m.idAsignatura " .
            "LEFT JOIN TareaEntrega te ON te.idTarea = t.id AND te.idAlumno = al.id " .
            "WHERE t.id = ? ORDER BY al.apellidos ASC, al.nombre ASC"
        );
        if (!$stmt) return false;
        $idTarea = intval($idTarea);
        $stmt->bind_param("i", $idTarea);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
