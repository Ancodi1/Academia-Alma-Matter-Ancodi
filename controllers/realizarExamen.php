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
            // Enviar email de notificación
            $conexion = $alumnoController->getConexion();
            $stmtAlumno = $conexion->preparar("SELECT nombre, apellidos, email FROM Alumno WHERE id = ?");
            $stmtAlumno->bind_param("i", $idAlumno);
            $stmtAlumno->execute();
            $alumno = $stmtAlumno->get_result()->fetch_assoc();
            $stmtAlumno->close();
            if ($alumno && $alumno['email']) {
                require_once(__DIR__ . "/../models/mail.php");
                $subject = "Nueva nota registrada";
                $body = "Hola {$alumno['nombre']} {$alumno['apellidos']},<br>Se ha registrado una nueva nota: $nota en la fecha $fecha.";
                enviarEmail($alumno['email'], $subject, $body);
            }
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
