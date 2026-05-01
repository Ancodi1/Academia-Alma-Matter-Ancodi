<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
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
    <title>Academia Alma Mater</title>
    <script src="https://use.fontawesome.com/bc32f7bfed.js"></script>
</head>
<body>
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <!--Divisor de la cabezera -->
    <div id="cabecera">
		<div id="logo">
            <?php $logoVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/img/logo.png') ?: time(); ?>
			<a href="/index.php"><img src="/img/logo.png?v=<?php echo $logoVersion; ?>" alt="Academia Alma Mater"></a>
        </div>
        <div id="menu">
			<a href="/index.php">Inicio</a>
			<a href="/gestionAlumnos.php">Gestión Alumnos</a>
			<a href="/gestionAsignaturas.php">Gestión Asignaturas</a>
			<a href="/logout.php">Logout</a>
			<button id="theme-toggle" aria-label="Cambiar tema" style="background: none; border: none; cursor: pointer; font-size: 18px; margin-left: 10px;">🌙</button>
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
