<?php
@include_once(__DIR__ . '/../config.php');

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function setLocale(string $lang): void {
    $_SESSION['lang'] = $lang;
}

function getLocale(): string {
    $default = defined('DEFAULT_LANGUAGE') ? DEFAULT_LANGUAGE : 'es';
    $supported = defined('SUPPORTED_LANGUAGES') ? SUPPORTED_LANGUAGES : ['es'];
    $lang = $_SESSION['lang'] ?? $default;
    if (!in_array($lang, $supported, true)) { $lang = $default; }
    return $lang;
}

function __($key): string {
    static $cache = [];
    $lang = getLocale();
    if (!isset($cache[$lang])) {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        $cache[$lang] = file_exists($file) ? include($file) : [];
    }
    return $cache[$lang][$key] ?? $key;
}
?>


