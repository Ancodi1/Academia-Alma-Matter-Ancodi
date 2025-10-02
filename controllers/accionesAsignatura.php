<?php
require_once(__DIR__ . "/../models/session.php");
require_once(__DIR__ . "/../models/mysqlConnect.php");
require_once(__DIR__ . "/../models/csrf.php");

class AsignaturaController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function exportarAsignaturasCSV() {
        $stmt = $this->conexion->preparar("SELECT id, nombre, curso FROM Asignatura ORDER BY nombre ASC");
        if (!$stmt) return false;
        $stmt->execute();
        $result = $stmt->get_result();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=asignaturas.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nombre', 'Curso']);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [$row['id'], $row['nombre'], $row['curso']]);
        }
        fclose($output);
        $stmt->close();
        return true;
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

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../gestionAsignaturas.php?error=csrf");
        exit;
    }
    authorizeRoles(['admin','profesor']);

    $controller = new AsignaturaController();

    if (isset($_POST["exportarAsignaturasCSV"])) {
        $controller->exportarAsignaturasCSV();
        exit;
    }

    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : null;
    $curso = isset($_POST["curso"]) ? $_POST["curso"] : null;
    $id = isset($_POST["id"]) ? $_POST["id"] : null;

    if (isset($_POST["nuevaAsignatura"])) {
        $controller->insertarAsignatura($nombre, $curso);
    }
    if (isset($_POST["modificarAsignatura"])) {
        $controller->modificarAsignatura($id, $nombre, $curso);
    }
    if (isset($_POST["eliminarAsignatura"])) {
        authorizeRoles(['admin']);
        $controller->borrarAsignatura($id);
    }
}