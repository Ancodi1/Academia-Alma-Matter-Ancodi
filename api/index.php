<?php
@include_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../controllers/AlumnoController.php');

header('Content-Type: application/json; charset=utf-8');

// Autenticación por token simple
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = '';
if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
    $token = $m[1];
}
if (!defined('API_TOKEN') || !$token || !hash_equals(API_TOKEN, $token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
// Asumimos que API está en /academia/api/
$basePos = strpos($path, 'api/');
$sub = $basePos !== false ? substr($path, $basePos + 4) : '';

$controller = new AlumnoController();

if ($method === 'GET' && ($sub === 'alumnos' || $sub === 'alumnos/')) {
    $result = $controller->buscarAlumnos('', 1, 1000);
    $items = [];
    while ($row = $result->fetch_assoc()) { $items[] = $row; }
    echo json_encode(['data' => $items]);
    exit;
}

if ($method === 'GET' && preg_match('#^alumnos/(\d+)$#', $sub, $m)) {
    $id = intval($m[1]);
    // Reusar getTodos con filtro simple
    $db = new mysqli('localhost', 'root', '', 'almamater');
    $stmt = $db->prepare('SELECT id, nombre, apellidos, edad FROM Alumno WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();
    if (!$res) { http_response_code(404); echo json_encode(['error' => 'Not Found']); exit; }
    echo json_encode(['data' => $res]);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not Found']);
?>


