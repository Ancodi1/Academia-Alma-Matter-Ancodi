<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class ProfesorController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getProfesores() {
        return $this->conexion->realizarConsultaSQL("SELECT * FROM Profesor ORDER BY apellidos ASC, nombre ASC");
    }

    public function guardar($nombre, $apellidos, $email, $telefono, $especialidad) {
        $stmt = $this->conexion->preparar("INSERT INTO Profesor (nombre, apellidos, email, telefono, especialidad) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("sssss", $nombre, $apellidos, $email, $telefono, $especialidad);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar($id, $nombre, $apellidos, $email, $telefono, $especialidad) {
        $stmt = $this->conexion->preparar("UPDATE Profesor SET nombre = ?, apellidos = ?, email = ?, telefono = ?, especialidad = ? WHERE id = ?");
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("sssssi", $nombre, $apellidos, $email, $telefono, $especialidad, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar($id) {
        $stmt = $this->conexion->preparar("DELETE FROM Profesor WHERE id = ?");
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
