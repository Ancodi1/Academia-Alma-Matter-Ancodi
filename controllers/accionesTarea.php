<?php
require_once(__DIR__ . "/TareaController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auditoria.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../tareas.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../tareas.php?error=csrf");
    exit;
}

$controller = new TareaController();

if (isset($_POST['crearTarea'])) {
    $idAsignatura = intval($_POST['idAsignatura'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fechaEntrega = trim($_POST['fechaEntrega'] ?? '');

    if ($idAsignatura <= 0 || $titulo === '' || $fechaEntrega === '') {
        header("Location: ../tareas.php?error=validacion");
        exit;
    }

    $id = $controller->crear($idAsignatura, $titulo, $descripcion, $fechaEntrega);
    if ($id) {
        registrarAuditoria('crear', 'Tarea', $id, $titulo);
        header("Location: ../tareas.php?mensaje=creada");
        exit;
    }
}

if (isset($_POST['guardarEntregas'])) {
    $idTarea = intval($_POST['idTarea'] ?? 0);
    $estados = isset($_POST['estado']) && is_array($_POST['estado']) ? $_POST['estado'] : [];
    $comentarios = isset($_POST['comentario']) && is_array($_POST['comentario']) ? $_POST['comentario'] : [];

    foreach ($estados as $idAlumno => $estado) {
        $controller->actualizarEntrega($idTarea, intval($idAlumno), $estado, $comentarios[$idAlumno] ?? '');
    }
    registrarAuditoria('actualizar_entregas', 'Tarea', $idTarea, 'Entregas actualizadas');
    header("Location: ../tareas.php?idTarea=" . $idTarea . "&mensaje=entregas");
    exit;
}

header("Location: ../tareas.php?error=guardar");
exit;
?>
