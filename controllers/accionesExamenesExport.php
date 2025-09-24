<?php
require_once(__DIR__ . '/AlumnoController.php');
require_once(__DIR__ . '/../models/session.php');

authorizeRoles(['admin','profesor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exportarExamenesCSV'])) {
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    $controller = new AlumnoController();
    $controller->exportarExamenesAlumnoCSV($idAlumno);
    exit;
}

header('Location: ../editorAlumnos.php');
exit;
?>


