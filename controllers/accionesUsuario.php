<?php
require_once(__DIR__ . "/UsuarioController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirAdmin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../usuarios.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../usuarios.php?error=csrf");
    exit;
}

$controller = new UsuarioController();

if (isset($_POST['crearUsuario'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'teacher';
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;

    if ($username === '' || strlen($password) < 6) {
        header("Location: ../usuarios.php?error=validacion");
        exit;
    }

    header("Location: ../usuarios.php?" . ($controller->crearUsuario($username, $password, $role, $idAlumno) ? "mensaje=creado" : "error=guardar"));
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    header("Location: ../usuarios.php?error=usuario");
    exit;
}

if (isset($_POST['actualizarUsuario'])) {
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'teacher';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    header("Location: ../usuarios.php?" . ($controller->actualizarUsuario($id, $role, $password, $idAlumno) ? "mensaje=actualizado" : "error=guardar"));
    exit;
}

if (isset($_POST['eliminarUsuario'])) {
    if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id) {
        header("Location: ../usuarios.php?error=propio_usuario");
        exit;
    }
    header("Location: ../usuarios.php?" . ($controller->eliminarUsuario($id) ? "mensaje=eliminado" : "error=eliminar"));
    exit;
}

header("Location: ../usuarios.php");
exit;
?>
