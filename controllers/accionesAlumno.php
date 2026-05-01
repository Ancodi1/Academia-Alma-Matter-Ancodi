<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/../models/csrf.php");

$alumnoController = new AlumnoController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../editorAlumnos.php?error=csrf");
        exit;
    }
    
    $id = $_POST['id'];
    
    if (isset($_POST['modificarAlumno'])) {
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
        $edad = isset($_POST['edad']) ? trim($_POST['edad']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        if ($nombre === '' || $apellidos === '' || $edad === '' || $email === '') {
            header("Location: ../editorAlumnos.php?error=validacion_campos");
            exit;
        }
        if (!is_numeric($edad) || intval($edad) < 1 || intval($edad) > 120) {
            header("Location: ../editorAlumnos.php?error=edad_invalida");
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../editorAlumnos.php?error=email_invalido");
            exit;
        }

        if ($alumnoController->modificarAlumno($id, $nombre, $apellidos, $edad, $email)) {
            header("Location: ../editorAlumnos.php?mensaje=modificado");
            exit;
        } else {
            header("Location: ../editorAlumnos.php?error=modificar");
            exit;
        }
    }
    
    if (isset($_POST['eliminarAlumno'])) {
        if ($alumnoController->eliminarAlumno($id)) {
            header("Location: ../editorAlumnos.php?mensaje=eliminado");
            exit;
        } else {
            header("Location: ../editorAlumnos.php?error=eliminar");
            exit;
        }
    }
    
    if (isset($_POST['realizarExamen'])) {
        // Redirigir a página de exámenes (por implementar)
        header("Location: ../realizarExamen.php?id=" . $id);
        exit;
    }
    
    if (isset($_POST['verExamenesAlumno'])) {
        // Redirigir a página de exámenes realizados (por implementar)
        header("Location: ../examenesRealizados.php?id=" . $id);
        exit;
    }
    
} else {
    header("Location: ../editorAlumnos.php");
    exit;
}
?>
