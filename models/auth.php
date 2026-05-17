<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function usuarioActualEsAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function usuarioActualEsInterno() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'teacher']);
}

function usuarioActualEsPortal() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['student', 'family']);
}

function usuarioActualTieneRol($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}

function requerirAdmin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
    if (!usuarioActualEsAdmin()) {
        header("Location: /index.php?error=sin_permiso");
        exit;
    }
}

function requerirInterno() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
    if (!usuarioActualEsInterno()) {
        header("Location: /portal.php?error=sin_permiso");
        exit;
    }
}

function requerirRol($roles) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
    if (!usuarioActualTieneRol($roles)) {
        header("Location: /index.php?error=sin_permiso");
        exit;
    }
}
?>
