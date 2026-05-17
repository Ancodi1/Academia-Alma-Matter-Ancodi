<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

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
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
        $tutor = isset($_POST['tutor']) ? trim($_POST['tutor']) : '';
        $contactoEmergencia = isset($_POST['contacto_emergencia']) ? trim($_POST['contacto_emergencia']) : '';
        $centro = isset($_POST['centro']) ? trim($_POST['centro']) : '';
        $cursoActual = isset($_POST['curso_actual']) ? trim($_POST['curso_actual']) : '';
        $fechaAlta = isset($_POST['fecha_alta']) ? trim($_POST['fecha_alta']) : date('Y-m-d');
        $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

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

        $origen = isset($_POST['origen']) && $_POST['origen'] === 'ficha' ? "../fichaAlumno.php?id=" . intval($id) : "../editorAlumnos.php";
        if ($alumnoController->modificarAlumno($id, $nombre, $apellidos, $edad, $email, $telefono, $direccion, $tutor, $contactoEmergencia, $centro, $cursoActual, $fechaAlta, $observaciones)) {
            header("Location: " . $origen . (strpos($origen, '?') === false ? '?' : '&') . "mensaje=modificado");
            exit;
        } else {
            header("Location: " . $origen . (strpos($origen, '?') === false ? '?' : '&') . "error=modificar");
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
