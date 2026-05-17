<?php
require_once(__DIR__ . "/mysqlConnect.php");

function registrarAuditoria($accion, $entidad, $entidadId = null, $detalle = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $usuarioId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $conexion = new mysqlConn();
    $stmt = $conexion->preparar("INSERT INTO Auditoria (idUsuario, accion, entidad, entidadId, detalle, ip) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) return false;

    $entidadId = $entidadId !== null ? intval($entidadId) : null;
    $stmt->bind_param("ississ", $usuarioId, $accion, $entidad, $entidadId, $detalle, $ip);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
?>
