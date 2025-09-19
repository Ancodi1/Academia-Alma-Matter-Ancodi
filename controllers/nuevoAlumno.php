<?php
require_once(__DIR__ . "/AlumnoController.php");

// Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombreAlumno']) ? trim($_POST['nombreAlumno']) : '';
    $apellido = isset($_POST['apellidoAlumno']) ? trim($_POST['apellidoAlumno']) : '';
    $edad = isset($_POST['edadAlumno']) ? trim($_POST['edadAlumno']) : '';
    
    // Validar que todos los campos estén completos
    if (empty($nombre) || empty($apellido) || empty($edad)) {
        // Si faltan datos, redirigir de vuelta al formulario con error
        header("Location: ../nuevoAlumno.php?error=campos_vacios");
        exit();
    }
    
    // Validar que la edad sea un número
    if (!is_numeric($edad) || $edad < 1 || $edad > 120) {
        header("Location: ../nuevoAlumno.php?error=edad_invalida");
        exit();
    }
    
    // Insertar en la base de datos
    $alumnoController = new AlumnoController();
    if ($alumnoController->agregarAlumno($nombre, $apellido, $edad)) {
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
