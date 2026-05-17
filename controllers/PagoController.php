<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class PagoController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getAlumnos() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, apellidos FROM Alumno ORDER BY apellidos ASC, nombre ASC");
    }

    public function getPagos($estado = '', $idAlumno = 0) {
        $sql = "SELECT p.*, al.nombre, al.apellidos FROM Pago p JOIN Alumno al ON al.id = p.idAlumno";
        $where = [];
        $params = [];
        $types = "";
        if ($estado !== '') { $where[] = "p.estado = ?"; $params[] = $estado; $types .= "s"; }
        if ($idAlumno > 0) { $where[] = "p.idAlumno = ?"; $params[] = intval($idAlumno); $types .= "i"; }
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY p.fechaVencimiento DESC, p.id DESC";
        if (!$types) return $this->conexion->realizarConsultaSQL($sql);
        $stmt = $this->conexion->preparar($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function crear($idAlumno, $concepto, $importe, $fechaVencimiento, $estado) {
        $stmt = $this->conexion->preparar("INSERT INTO Pago (idAlumno, concepto, importe, fechaVencimiento, estado) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $idAlumno = intval($idAlumno);
        $importe = floatval($importe);
        $stmt->bind_param("isdss", $idAlumno, $concepto, $importe, $fechaVencimiento, $estado);
        $ok = $stmt->execute();
        $id = $this->conexion->getInsertId();
        $stmt->close();
        return $ok ? $id : false;
    }

    public function actualizarEstado($id, $estado, $fechaPago = null) {
        $fechaPago = $estado === 'Pagado' ? ($fechaPago ?: date('Y-m-d')) : null;
        $stmt = $this->conexion->preparar("UPDATE Pago SET estado = ?, fechaPago = ? WHERE id = ?");
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("ssi", $estado, $fechaPago, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
