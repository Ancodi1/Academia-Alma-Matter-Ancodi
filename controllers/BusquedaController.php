<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class BusquedaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function buscar($termino) {
        $like = "%" . $termino . "%";
        return [
            'alumnos' => $this->consulta("SELECT id, CONCAT(nombre, ' ', apellidos) AS titulo, email AS detalle FROM Alumno WHERE nombre LIKE ? OR apellidos LIKE ? OR email LIKE ? ORDER BY apellidos LIMIT 10", "sss", [$like, $like, $like]),
            'asignaturas' => $this->consulta("SELECT id, nombre AS titulo, curso AS detalle FROM Asignatura WHERE nombre LIKE ? OR curso LIKE ? ORDER BY nombre LIMIT 10", "ss", [$like, $like]),
            'profesores' => $this->consulta("SELECT id, CONCAT(nombre, ' ', apellidos) AS titulo, especialidad AS detalle FROM Profesor WHERE nombre LIKE ? OR apellidos LIKE ? OR especialidad LIKE ? ORDER BY apellidos LIMIT 10", "sss", [$like, $like, $like]),
        ];
    }

    private function consulta($sql, $types, $params) {
        $stmt = $this->conexion->preparar($sql);
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>
