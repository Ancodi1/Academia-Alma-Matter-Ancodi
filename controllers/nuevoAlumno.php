<?php
require_once(__DIR__ . "/AlumnoController.php");

// Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombreAlumno']) ? trim($_POST['nombreAlumno']) : '';
    $apellido = isset($_POST['apellidoAlumno']) ? trim($_POST['apellidoAlumno']) : '';
    $edad = isset($_POST['edadAlumno']) ? trim($_POST['edadAlumno']) : '';
    $email = isset($_POST['emailAlumno']) ? trim($_POST['emailAlumno']) : '';
    
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
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $fileName = uniqid() . '_' . basename($_FILES['fotoAlumno']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($fileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES['fotoAlumno']['tmp_name'], $targetFile)) {
                $fotoPath = 'uploads/' . $fileName;
            }
        }
    }
    
    // Insertar en la base de datos
    $alumnoController = new AlumnoController();
    $id = $alumnoController->agregarAlumno($nombre, $apellido, $edad, $email);
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
