<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class AuditoriaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getEventos($limit = 100) {
        $stmt = $this->conexion->preparar(
            "SELECT au.*, u.username FROM Auditoria au LEFT JOIN Usuario u ON u.id = au.idUsuario ORDER BY au.created_at DESC LIMIT ?"
        );
        if (!$stmt) return false;
        $limit = intval($limit);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
