<?php
// Gestión de sesión y helper de autenticación

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAuthenticated(): bool {
    return isset($_SESSION['user_id']);
}

function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function currentUserName(): string {
    return isset($_SESSION['user_name']) ? (string)$_SESSION['user_name'] : '';
}

function currentUserRole(): string {
    return isset($_SESSION['user_role']) ? (string)$_SESSION['user_role'] : 'invitado';
}

function loginUser(int $userId, string $name, string $role): void {
    // Regeneramos el ID de sesión para mitigar fijación de sesión
    if (function_exists('session_regenerate_id')) {
        session_regenerate_id(true);
    }
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $role;
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], isset($params['secure']) && $params['secure'], isset($params['httponly']) && $params['httponly']);
    }
    session_destroy();
}

function requireLogin(): void {
    if (!isAuthenticated()) {
        header('Location: /academia/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/academia/index.php'));
        exit;
    }
}

function authorizeRoles(array $allowedRoles): void {
    if (!isAuthenticated()) {
        requireLogin();
    }
    $role = currentUserRole();
    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo '<h2>Acceso denegado</h2>';
        echo '<p>No tienes permisos para acceder a esta sección.</p>';
        exit;
    }
}
?>


