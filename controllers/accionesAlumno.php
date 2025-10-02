<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/session.php");

$alumnoController = new AlumnoController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../editorAlumnos.php?error=csrf");
        exit;
    }
    
    $id = $_POST['id'];
    
    if (isset($_POST['modificarAlumno'])) {
        authorizeRoles(['admin','profesor']);
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
        $edad = isset($_POST['edad']) ? trim($_POST['edad']) : '';

        if ($nombre === '' || $apellidos === '' || $edad === '') {
            header("Location: ../editorAlumnos.php?error=validacion_campos");
            exit;
        }
        if (!is_numeric($edad) || intval($edad) < 1 || intval($edad) > 120) {
            header("Location: ../editorAlumnos.php?error=edad_invalida");
            exit;
        }

        if ($alumnoController->modificarAlumno($id, $nombre, $apellidos, $edad)) {
            // Crear notificación
            require_once(__DIR__ . '/NotificacionController.php');
            $notifController = new NotificacionController();
            $notifController->crearNotificacion(
                'Alumno modificado',
                "Se han actualizado los datos del alumno: $nombre $apellidos",
                'success'
            );
            header("Location: ../editorAlumnos.php?mensaje=modificado");
            exit;
        } else {
            header("Location: ../editorAlumnos.php?error=modificar");
            exit;
        }
    }
    
    if (isset($_POST['eliminarAlumno'])) {
        authorizeRoles(['admin']);
        if ($alumnoController->eliminarAlumno($id)) {
            header("Location: ../editorAlumnos.php?mensaje=eliminado");
            exit;
        } else {
            header("Location: ../editorAlumnos.php?error=eliminar");
            exit;
        }
    }
    
    if (isset($_POST['realizarExamen'])) {
        authorizeRoles(['admin','profesor']);
        // Redirigir a página de exámenes (por implementar)
        header("Location: ../realizarExamen.php?id=" . $id);
        exit;
    }
    
    if (isset($_POST['verExamenesAlumno'])) {
        authorizeRoles(['admin','profesor']);
        // Redirigir a página de exámenes realizados (por implementar)
        header("Location: ../examenesRealizados.php?id=" . $id);
        exit;
    }

    if (isset($_POST['exportarAlumnosCSV'])) {
        authorizeRoles(['admin','profesor']);
        // Exportación de alumnos en CSV
        $alumnoController->exportarAlumnosCSV();
        exit;
    }

    if (isset($_POST['importarAlumnosCSV'])) {
        authorizeRoles(['admin','profesor']);
        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
            header("Location: ../editorAlumnos.php?error=upload");
            exit;
        }

        $tmpPath = $_FILES['archivo_csv']['tmp_name'];
        $handle = fopen($tmpPath, 'r');
        if ($handle === false) {
            header("Location: ../editorAlumnos.php?error=abrir_csv");
            exit;
        }

        // Opcionalmente saltar cabecera si detectamos texto
        $lineNumber = 0;
        $importados = 0;
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $lineNumber++;
            if ($lineNumber === 1) {
                $posiblesCabeceras = array_map('strtolower', $data);
                if (in_array('nombre', $posiblesCabeceras) || in_array('apellidos', $posiblesCabeceras) || in_array('edad', $posiblesCabeceras)) {
                    continue; // saltamos cabecera
                }
            }
            if (count($data) < 3) { continue; }
            $nombre = trim($data[0]);
            $apellidos = trim($data[1]);
            $edad = trim($data[2]);

            if ($nombre === '' || $apellidos === '') { continue; }
            if (!is_numeric($edad) || intval($edad) < 1 || intval($edad) > 120) { continue; }

            if ($alumnoController->agregarAlumno($nombre, $apellidos, intval($edad))) {
                $importados++;
            }
        }
        fclose($handle);

        header("Location: ../editorAlumnos.php?mensaje=importados_" . $importados);
        exit;
    }
    
} else {
    header("Location: ../editorAlumnos.php");
    exit;
}
?>
