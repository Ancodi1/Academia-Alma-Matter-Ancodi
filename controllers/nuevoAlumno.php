<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/../models/auth.php");
require_once(__DIR__ . "/../models/csrf.php");

requerirInterno();

// Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../nuevoAlumno.php?error=csrf");
        exit();
    }
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombreAlumno']) ? trim($_POST['nombreAlumno']) : '';
    $apellido = isset($_POST['apellidoAlumno']) ? trim($_POST['apellidoAlumno']) : '';
    $edad = isset($_POST['edadAlumno']) ? trim($_POST['edadAlumno']) : '';
    $email = isset($_POST['emailAlumno']) ? trim($_POST['emailAlumno']) : '';
    $telefono = isset($_POST['telefonoAlumno']) ? trim($_POST['telefonoAlumno']) : '';
    $direccion = isset($_POST['direccionAlumno']) ? trim($_POST['direccionAlumno']) : '';
    $tutor = isset($_POST['tutorAlumno']) ? trim($_POST['tutorAlumno']) : '';
    $contactoEmergencia = isset($_POST['contactoEmergenciaAlumno']) ? trim($_POST['contactoEmergenciaAlumno']) : '';
    $centro = isset($_POST['centroAlumno']) ? trim($_POST['centroAlumno']) : '';
    $cursoActual = isset($_POST['cursoActualAlumno']) ? trim($_POST['cursoActualAlumno']) : '';
    $fechaAlta = isset($_POST['fechaAltaAlumno']) ? trim($_POST['fechaAltaAlumno']) : date('Y-m-d');
    $observaciones = isset($_POST['observacionesAlumno']) ? trim($_POST['observacionesAlumno']) : '';
    
    // Validar que todos los campos estén completos
    if (empty($nombre) || empty($apellido) || empty($edad) || empty($email)) {
        // Si faltan datos, redirigir de vuelta al formulario con error
        header("Location: ../nuevoAlumno.php?error=campos_vacios");
        exit();
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../nuevoAlumno.php?error=email_invalido");
        exit();
    }
    
    // Validar que la edad sea un número
    if (!is_numeric($edad) || $edad < 1 || $edad > 120) {
        header("Location: ../nuevoAlumno.php?error=edad_invalida");
        exit();
    }
    
    // Manejar upload de archivo
    $fotoPath = null;
    if (isset($_FILES['fotoAlumno']) && $_FILES['fotoAlumno']['error'] == UPLOAD_ERR_OK) {
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $mimePermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $maxBytes = 5 * 1024 * 1024;
        $original = basename($_FILES['fotoAlumno']['name']);
        $fileType = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        if ($_FILES['fotoAlumno']['size'] > $maxBytes) {
            header("Location: ../nuevoAlumno.php?error=foto_tamano");
            exit();
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['fotoAlumno']['tmp_name']);
        if (!in_array($fileType, $permitidas) || !in_array($mime, $mimePermitidos)) {
            header("Location: ../nuevoAlumno.php?error=foto_tipo");
            exit();
        }

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $fileName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['fotoAlumno']['tmp_name'], $targetFile)) {
            $fotoPath = 'uploads/' . $fileName;
        }
    }
    
    // Insertar en la base de datos
    $alumnoController = new AlumnoController();
    $id = $alumnoController->agregarAlumno($nombre, $apellido, $edad, $email, $telefono, $direccion, $tutor, $contactoEmergencia, $centro, $cursoActual, $fechaAlta, $observaciones);
    if ($id) {
        // Guardar archivo si existe
        if ($fotoPath) {
            $conexion = $alumnoController->getConexion();
            $stmt = $conexion->preparar("INSERT INTO Archivo (idAlumno, nombre_archivo, tipo, ruta) VALUES (?, ?, ?, ?)");
            $tipo = 'foto';
            $stmt->bind_param("isss", $id, $_FILES['fotoAlumno']['name'], $tipo, $fotoPath);
            $stmt->execute();
            $stmt->close();
        }
        // Si todo está correcto, redirigir a la página de confirmación
        header("Location: ../altaAlumnoCorrecta.php");
        exit();
    } else {
        // Error al insertar en la base de datos
        header("Location: ../nuevoAlumno.php?error=base_datos");
        exit();
    }
    
} else {
    // Si no se envió por POST, redirigir al formulario
    header("Location: ../nuevoAlumno.php");
    exit();
}
?>
