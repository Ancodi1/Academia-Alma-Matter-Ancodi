<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function throttle(string $key, int $maxAttempts, int $windowSeconds): bool {
    $now = time();
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [];
    }
    // Limpiar intentos fuera de ventana
    $_SESSION['rate_limit'][$key] = array_filter(
        $_SESSION['rate_limit'][$key],
        function ($ts) use ($now, $windowSeconds) { return ($now - $ts) <= $windowSeconds; }
    );
    if (count($_SESSION['rate_limit'][$key]) >= $maxAttempts) {
        return false; // bloqueado
    }
    $_SESSION['rate_limit'][$key][] = $now;
    return true;
}

function isThrottled(string $key, int $maxAttempts, int $windowSeconds): bool {
    $now = time();
    $list = $_SESSION['rate_limit'][$key] ?? [];
    $list = array_filter($list, function ($ts) use ($now, $windowSeconds) { return ($now - $ts) <= $windowSeconds; });
    return count($list) >= $maxAttempts;
}
?>


