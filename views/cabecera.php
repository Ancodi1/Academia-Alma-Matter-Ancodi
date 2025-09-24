<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
    <?php $cssVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/academia.css') ?: time(); ?>
    <link rel="stylesheet" type="text/css" href="/academia/academia.css?v=<?php echo $cssVersion; ?>">
    <title>Academia Alma Mater</title>
    <script src="https://use.fontawesome.com/bc32f7bfed.js"></script>
</head>
<body>
    <!--Divisor de la cabezera -->
    <?php 
        // Mostrar estado de sesión
        require_once(__DIR__ . '/../models/session.php'); 
    ?>
    <div id="cabecera">
		<div id="logo">
            <?php $logoVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/img/logo.png') ?: time(); ?>
			<a href="/academia/index.php"><img src="/academia/img/logo.png?v=<?php echo $logoVersion; ?>" alt="Academia Alma Mater"></a>
        </div>
        <div id="menu">
			<a href="/academia/index.php">Inicio</a>
            <a href="/academia/dashboard.php">Dashboard</a>
			<a href="/academia/gestionAlumnos.php">Gestión Alumnos</a>
			<a href="/academia/gestionAsignaturas.php">Gestión Asignaturas</a>
            <?php if (isAuthenticated()): ?>
                <span style="margin-left:10px;">Hola, <?php echo htmlspecialchars(currentUserName()); ?> (<?php echo htmlspecialchars(currentUserRole()); ?>)</span>
                <a href="/academia/logout.php" style="margin-left:10px;">Salir</a>
            <?php else: ?>
                <a href="/academia/login.php" style="margin-left:10px;">Entrar</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>