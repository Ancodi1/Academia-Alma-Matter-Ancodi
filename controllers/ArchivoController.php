<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class ArchivoController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getArchivosAlumno($idAlumno) {
        $stmt = $this->conexion->preparar("SELECT * FROM Archivo WHERE idAlumno = ? ORDER BY fecha_subida DESC");
        if (!$stmt) return false;
        $idAlumno = intval($idAlumno);
        $stmt->bind_param("i", $idAlumno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function guardarArchivo($idAlumno, $nombre, $tipo, $ruta) {
        $stmt = $this->conexion->preparar("INSERT INTO Archivo (idAlumno, nombre_archivo, tipo, ruta) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $idAlumno = intval($idAlumno);
        $stmt->bind_param("isss", $idAlumno, $nombre, $tipo, $ruta);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getArchivo($id) {
        $stmt = $this->conexion->preparar("SELECT * FROM Archivo WHERE id = ?");
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function eliminarArchivo($id) {
        $stmt = $this->conexion->preparar("DELETE FROM Archivo WHERE id = ?");
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
