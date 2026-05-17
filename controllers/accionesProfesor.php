<?php
require_once(__DIR__ . "/ProfesorController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../profesores.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../profesores.php?error=csrf");
    exit;
}

$controller = new ProfesorController();
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$especialidad = isset($_POST['especialidad']) ? trim($_POST['especialidad']) : '';

if (isset($_POST['crearProfesor'])) {
    if ($nombre === '' || $apellidos === '') {
        header("Location: ../profesores.php?error=validacion");
        exit;
    }
    header("Location: ../profesores.php?" . ($controller->guardar($nombre, $apellidos, $email, $telefono, $especialidad) ? "mensaje=creado" : "error=guardar"));
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    header("Location: ../profesores.php?error=profesor");
    exit;
}

if (isset($_POST['actualizarProfesor'])) {
    header("Location: ../profesores.php?" . ($controller->actualizar($id, $nombre, $apellidos, $email, $telefono, $especialidad) ? "mensaje=actualizado" : "error=guardar"));
    exit;
}

if (isset($_POST['eliminarProfesor'])) {
    header("Location: ../profesores.php?" . ($controller->eliminar($id) ? "mensaje=eliminado" : "error=eliminar"));
    exit;
}

header("Location: ../profesores.php");
exit;
?>
