<?php
require_once(__DIR__ . '/../models/mysqlConnect.php');

class NotificacionController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function crearNotificacion(string $titulo, string $mensaje, string $tipo = 'info'): bool {
        $stmt = $this->db->preparar('INSERT INTO Notificacion (titulo, mensaje, tipo) VALUES (?, ?, ?)');
        if (!$stmt) return false;
        $stmt->bind_param('sss', $titulo, $mensaje, $tipo);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getNotificaciones(int $limite = 10): array {
        $stmt = $this->db->preparar('SELECT id, titulo, mensaje, tipo, leida, fecha_creacion FROM Notificacion ORDER BY fecha_creacion DESC LIMIT ?');
        if (!$stmt) return [];
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        $notificaciones = [];
        while ($row = $result->fetch_assoc()) {
            $notificaciones[] = [
                'id' => (int)$row['id'],
                'titulo' => $row['titulo'],
                'mensaje' => $row['mensaje'],
                'tipo' => $row['tipo'],
                'leida' => (bool)$row['leida'],
                'fecha_creacion' => $row['fecha_creacion']
            ];
        }
        $stmt->close();
        return $notificaciones;
    }

    public function contarNoLeidas(): int {
        $stmt = $this->db->preparar('SELECT COUNT(*) as total FROM Notificacion WHERE leida = FALSE');
        if (!$stmt) return 0;
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return (int)($row['total'] ?? 0);
    }

    public function marcarComoLeida(int $id): bool {
        $stmt = $this->db->preparar('UPDATE Notificacion SET leida = TRUE WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function marcarTodasComoLeidas(): bool {
        $stmt = $this->db->preparar('UPDATE Notificacion SET leida = TRUE WHERE leida = FALSE');
        if (!$stmt) return false;
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
