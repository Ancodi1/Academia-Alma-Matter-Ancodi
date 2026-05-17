<?php
require_once(__DIR__ . "/AsignaturaController.php");
require_once(__DIR__ . "/../models/auth.php");
require_once(__DIR__ . "/../models/csrf.php");

requerirInterno();

// Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../nuevoAsignatura.php?error=csrf");
        exit();
    }
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombreAsignatura']) ? trim($_POST['nombreAsignatura']) : '';
    $curso = isset($_POST['cursoAsignatura']) ? trim($_POST['cursoAsignatura']) : '';
    
    // Validar que todos los campos estén completos
    if (empty($nombre) || empty($curso)) {
        header("Location: ../nuevoAsignatura.php?error=campos_vacios");
        exit();
    }
    
    // Insertar en la base de datos
    $asignaturaController = new AsignaturaController();
    if ($asignaturaController->insertarAsignatura($nombre, $curso)) {
        header("Location: ../altaAsignaturaCorrecta.php");
        exit();
    } else {
        header("Location: ../nuevoAsignatura.php?error=base_datos");
        exit();
    }
    
} else {
    header("Location: ../nuevoAsignatura.php");
    exit();
}
?>
