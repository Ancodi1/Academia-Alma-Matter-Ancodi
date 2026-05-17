<?php
require_once(__DIR__ . "/MatriculaController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../matriculas.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../matriculas.php?error=csrf");
    exit;
}

$idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
$asignaturas = isset($_POST['asignaturas']) && is_array($_POST['asignaturas']) ? $_POST['asignaturas'] : [];

if ($idAlumno <= 0) {
    header("Location: ../matriculas.php?error=alumno");
    exit;
}

$controller = new MatriculaController();
if ($controller->guardarMatriculasAlumno($idAlumno, $asignaturas)) {
    header("Location: ../matriculas.php?idAlumno=" . $idAlumno . "&mensaje=guardado");
    exit;
}

header("Location: ../matriculas.php?idAlumno=" . $idAlumno . "&error=guardar");
exit;
?>
