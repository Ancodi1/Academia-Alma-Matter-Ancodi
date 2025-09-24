<?php
require_once(__DIR__ . '/../models/mysqlConnect.php');

class DashboardController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function getNumeroAlumnos(): int {
        $stmt = $this->db->preparar('SELECT COUNT(*) AS total FROM Alumno');
        if (!$stmt) return 0;
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return intval($res['total'] ?? 0);
    }

    public function getNumeroExamenes(): int {
        $stmt = $this->db->preparar('SELECT COUNT(*) AS total FROM Examen');
        if (!$stmt) return 0;
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return intval($res['total'] ?? 0);
    }

    public function getPromedioNotas(): float {
        $stmt = $this->db->preparar('SELECT AVG(nota) AS promedio FROM Examen');
        if (!$stmt) return 0.0;
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return round(floatval($res['promedio'] ?? 0.0), 2);
    }

    public function getTasaAprobacion(float $umbral = 5.0): float {
        $stmt = $this->db->preparar('SELECT SUM(nota >= ?) AS aprobados, COUNT(*) AS total FROM Examen');
        if (!$stmt) return 0.0;
        $stmt->bind_param('d', $umbral);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $total = intval($res['total'] ?? 0);
        if ($total === 0) return 0.0;
        $aprobados = intval($res['aprobados'] ?? 0);
        return round(($aprobados / $total) * 100.0, 2);
    }
}
?>


