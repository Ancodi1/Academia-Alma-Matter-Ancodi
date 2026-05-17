<?php
require_once(__DIR__ . "/PagoController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auditoria.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pagos.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../pagos.php?error=csrf");
    exit;
}

$controller = new PagoController();

if (isset($_POST['crearPago'])) {
    $idAlumno = intval($_POST['idAlumno'] ?? 0);
    $concepto = trim($_POST['concepto'] ?? '');
    $importe = trim($_POST['importe'] ?? '');
    $fechaVencimiento = trim($_POST['fechaVencimiento'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Pendiente');

    if ($idAlumno <= 0 || $concepto === '' || !is_numeric($importe) || $fechaVencimiento === '') {
        header("Location: ../pagos.php?error=validacion");
        exit;
    }

    $id = $controller->crear($idAlumno, $concepto, $importe, $fechaVencimiento, $estado);
    if ($id) {
        registrarAuditoria('crear', 'Pago', $id, $concepto);
        header("Location: ../pagos.php?mensaje=creado");
        exit;
    }
}

if (isset($_POST['actualizarPago'])) {
    $id = intval($_POST['id'] ?? 0);
    $estado = trim($_POST['estado'] ?? 'Pendiente');
    if ($id > 0 && $controller->actualizarEstado($id, $estado)) {
        registrarAuditoria('actualizar_estado', 'Pago', $id, $estado);
        header("Location: ../pagos.php?mensaje=actualizado");
        exit;
    }
}

header("Location: ../pagos.php?error=guardar");
exit;
?>
