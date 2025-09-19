<?php
require_once(__DIR__ . "/AlumnoController.php");

$alumnoController = new AlumnoController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idAlumno = isset($_POST["idAlumno"]) ? intval($_POST["idAlumno"]) : null;
    $idAsignatura = isset($_POST["idAsignatura"]) ? intval($_POST["idAsignatura"]) : null;
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : null;
    $nota = isset($_POST["nota"]) ? trim($_POST["nota"]) : null;

    // Validaciones básicas
    if (!$idAlumno || !$idAsignatura || $fecha === null || $nota === null || $fecha === '' || $nota === '') {
        header("Location: ../realizarExamen.php?id=" . ($idAlumno ?: 0) . "&error=campos");
        exit;
    }
    if (!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $fecha)) {
        header("Location: ../realizarExamen.php?id=" . $idAlumno . "&error=fecha");
        exit;
    }
    if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
        header("Location: ../realizarExamen.php?id=" . $idAlumno . "&error=nota");
        exit;
    }

    if ($idAlumno && $idAsignatura && $fecha !== null && $nota !== null) {
        if ($alumnoController->ponerNotaExamen($idAsignatura, $idAlumno, $fecha, $nota)) {
            header("Location: ../editorAlumnos.php?mensaje=examen_creado");
            exit;
        } else {
            header("Location: ../editorAlumnos.php?error=examen_crear");
            exit;
        }
    } else {
        header("Location: ../editorAlumnos.php?error=examen_datos");
        exit;
    }
} else {
    header("Location: ../editorAlumnos.php");
    exit;
}