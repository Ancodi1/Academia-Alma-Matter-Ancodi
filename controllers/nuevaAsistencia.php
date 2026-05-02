<?php
require_once(__DIR__ . "/AsistenciaController.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $fecha = isset($_POST['fechaAsistencia']) ? trim($_POST['fechaAsistencia']) : '';
    $estado = isset($_POST['estadoAsistencia']) ? trim($_POST['estadoAsistencia']) : '';

    if ($idAlumno <= 0 || $idAsignatura <= 0 || empty($fecha) || empty($estado)) {
        header("Location: ../nuevoAsistencia.php?error=campos_vacios");
        exit();
    }

    $asistenciaController = new AsistenciaController();
    if ($asistenciaController->insertarAsistencia($idAlumno, $idAsignatura, $fecha, $estado)) {
        header("Location: ../altaAsistenciaCorrecta.php");
        exit();
    }

    header("Location: ../nuevoAsistencia.php?error=base_datos");
    exit();
}

header("Location: ../nuevoAsistencia.php");
exit();
?>