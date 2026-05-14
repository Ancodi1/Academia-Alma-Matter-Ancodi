<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AsignaturaController {
    private $conexion;
    
    public function __construct() {
        $this->conexion = new mysqlConn();
    }
    
    public function getTodasLasAsignaturas() {
        $sql = "SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getCursos() {
        $sql = "SELECT DISTINCT curso FROM Asignatura ORDER BY curso ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function getAsignaturasPorCurso($curso = '') {
        if ($curso) {
            $stmt = $this->conexion->preparar("SELECT id, nombre, curso FROM Asignatura WHERE curso = ? ORDER BY nombre ASC");
            if (!$stmt) return false;
            $stmt->bind_param("s", $curso);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $sql = "SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC";
        return $this->conexion->realizarConsultaSQL($sql);
    }

    public function buscarAsignaturas($termino = '', $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        
        if ($termino) {
            $stmt = $this->conexion->preparar("SELECT id, nombre, curso FROM Asignatura WHERE nombre LIKE ? OR curso LIKE ? ORDER BY nombre ASC LIMIT ? OFFSET ?");
            if (!$stmt) return false;
            $terminoLike = "%$termino%";
            $stmt->bind_param("ssii", $terminoLike, $terminoLike, $porPagina, $offset);
        } else {
            $stmt = $this->conexion->preparar("SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC LIMIT ? OFFSET ?");
            if (!$stmt) return false;
            $stmt->bind_param("ii", $porPagina, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function contarAsignaturas($termino = '') {
        if ($termino) {
            $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Asignatura WHERE nombre LIKE ? OR curso LIKE ?");
            if (!$stmt) return false;
            $terminoLike = "%$termino%";
            $stmt->bind_param("ss", $terminoLike, $terminoLike);
        } else {
            $stmt = $this->conexion->preparar("SELECT COUNT(*) as total FROM Asignatura");
            if (!$stmt) return false;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
    
    public function insertarAsignatura($nombre, $curso) {
        $stmt = $this->conexion->preparar("INSERT INTO Asignatura (nombre, curso) VALUES (?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ss", $nombre, $curso);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    
    public function modificarAsignatura($id, $nombre, $curso) {
        $stmt = $this->conexion->preparar("UPDATE Asignatura SET nombre = ?, curso = ? WHERE id = ?");
        if (!$stmt) return false;
        $idInt = intval($id);
        $stmt->bind_param("ssi", $nombre, $curso, $idInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    
    public function borrarAsignatura($id) {
        $stmt = $this->conexion->preparar("DELETE FROM Asignatura WHERE id = ?");
        if (!$stmt) return false;
        $idInt = intval($id);
        $stmt->bind_param("i", $idInt);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>