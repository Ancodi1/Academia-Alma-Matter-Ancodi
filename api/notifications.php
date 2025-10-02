<?php
require_once(__DIR__ . '/../models/session.php');
require_once(__DIR__ . '/../controllers/NotificacionController.php');

header('Content-Type: application/json');

if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$controller = new NotificacionController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $notificaciones = $controller->getNotificaciones(10);
    $unreadCount = $controller->contarNoLeidas();
    
    echo json_encode([
        'success' => true,
        'notifications' => $notificaciones,
        'unreadCount' => $unreadCount
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'mark_read') {
        $id = (int)($input['id'] ?? 0);
        $success = $controller->marcarComoLeida($id);
        echo json_encode(['success' => $success]);
    } elseif ($action === 'mark_all_read') {
        $success = $controller->marcarTodasComoLeidas();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>
