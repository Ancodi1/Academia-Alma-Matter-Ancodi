<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once(__DIR__ . "/../models/auth.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$scriptActual = basename($_SERVER['SCRIPT_NAME'] ?? '');
$paginasPortal = ['portal.php', 'logout.php'];
$paginasAdmin = ['usuarios.php', 'auditoria.php'];

if (usuarioActualEsPortal() && !in_array($scriptActual, $paginasPortal)) {
    header("Location: /portal.php?error=sin_permiso");
    exit;
}

if (in_array($scriptActual, $paginasAdmin) && !usuarioActualEsAdmin()) {
    header("Location: /index.php?error=sin_permiso");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b66c3">
    <link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
    <?php $cssVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia.css') ?: time(); ?>
    <link rel="stylesheet" type="text/css" href="/academia.css?v=<?php echo $cssVersion; ?>">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
    <title>Refuerzo Escolar</title>
    <script src="https://use.fontawesome.com/bc32f7bfed.js"></script>
</head>
<body>
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <!--Divisor de la cabezera -->
    <div id="cabecera">
		<div id="logo">
            <?php $logoVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/img/logo.png') ?: time(); ?>
			<a href="/index.php" title="Ir al inicio"><img src="/img/logo.png?v=<?php echo $logoVersion; ?>" alt="Refuerzo Escolar"></a>
        </div>
        <div id="menu">
            <?php if (usuarioActualEsPortal()): ?>
			<a href="/portal.php">Portal</a>
            <?php else: ?>
            <form action="/buscar.php" method="get" class="menu-search">
                <input type="text" name="q" placeholder="Buscar">
            </form>
			<a href="/index.php">Inicio</a>
			<a href="/gestionAlumnos.php">Gestión Alumnos</a>
			<a href="/gestionAsignaturas.php">Gestión Asignaturas</a>
			<a href="/gestionAsistencia.php">Asistencia / Horarios</a>
			<a href="/profesores.php">Profesores</a>
			<a href="/pagos.php">Pagos</a>
			<a href="/tareas.php">Tareas</a>
			<a href="/reportes.php">Reportes</a>
                <?php if (usuarioActualEsAdmin()): ?>
			<a href="/usuarios.php">Usuarios</a>
			<a href="/auditoria.php">Auditoría</a>
                <?php endif; ?>
            <?php endif; ?>
			<a href="/logout.php">Logout</a>
		<button id="theme-toggle" aria-label="Cambiar tema" class="theme-toggle">🌙</button>
        </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;
            const savedTheme = localStorage.getItem('theme') || 'light';
            
            if (savedTheme === 'dark') {
                body.classList.add('dark');
                themeToggle.textContent = '☀️';
            } else {
                themeToggle.textContent = '🌙';
            }
            
            themeToggle.addEventListener('click', function() {
                body.classList.toggle('dark');
                const isDark = body.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                themeToggle.textContent = isDark ? '☀️' : '🌙';
            });
        });
    </script>
