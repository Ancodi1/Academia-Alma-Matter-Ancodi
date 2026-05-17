<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/../models/auth.php");
require_once(__DIR__ . "/../models/csrf.php");

requerirInterno();

$alumnoController = new AlumnoController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../editorAlumnos.php?error=csrf");
        exit;
    }
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    $idExamen = isset($_POST['idExamen']) ? intval($_POST['idExamen']) : 0;
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';

    if (isset($_POST['guardarExamen'])) {
        $nuevaFecha = isset($_POST['nuevaFecha']) ? trim($_POST['nuevaFecha']) : '';
        $nuevaNota = isset($_POST['nuevaNota']) ? trim($_POST['nuevaNota']) : '';
        if ($nuevaFecha === '' || $nuevaNota === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFecha) || !is_numeric($nuevaNota) || $nuevaNota < 0 || $nuevaNota > 10) {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&error=examen_validacion");
            exit;
        }
        $ok = $idExamen > 0
            ? $alumnoController->actualizarExamenPorId($idExamen, $nuevaFecha, $nuevaNota)
            : $alumnoController->actualizarExamen($idAlumno, $idAsignatura, $fecha, $nuevaFecha, $nuevaNota);
        if ($ok) {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&mensaje=examen_actualizado");
            exit;
        } else {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&error=examen_actualizar");
            exit;
        }
    }

    if (isset($_POST['eliminarExamen'])) {
        $ok = $idExamen > 0
            ? $alumnoController->eliminarExamenPorId($idExamen)
            : $alumnoController->eliminarExamen($idAlumno, $idAsignatura, $fecha);
        if ($ok) {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&mensaje=examen_eliminado");
            exit;
        } else {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&error=examen_eliminar");
            exit;
        }
    }
}

header("Location: ../editorAlumnos.php");
exit;
?>
