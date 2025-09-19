<?php
require_once(__DIR__ . "/AlumnoController.php");

$alumnoController = new AlumnoController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';

    if (isset($_POST['guardarExamen'])) {
        $nuevaFecha = isset($_POST['nuevaFecha']) ? trim($_POST['nuevaFecha']) : '';
        $nuevaNota = isset($_POST['nuevaNota']) ? trim($_POST['nuevaNota']) : '';
        if ($nuevaFecha === '' || $nuevaNota === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFecha) || !is_numeric($nuevaNota) || $nuevaNota < 0 || $nuevaNota > 10) {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&error=examen_validacion");
            exit;
        }
        if ($alumnoController->actualizarExamen($idAlumno, $idAsignatura, $fecha, $nuevaFecha, $nuevaNota)) {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&mensaje=examen_actualizado");
            exit;
        } else {
            header("Location: ../examenesRealizados.php?id=" . $idAlumno . "&error=examen_actualizar");
            exit;
        }
    }

    if (isset($_POST['eliminarExamen'])) {
        if ($alumnoController->eliminarExamen($idAlumno, $idAsignatura, $fecha)) {
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


