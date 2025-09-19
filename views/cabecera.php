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
    <div id="cabecera">
		<div id="logo">
            <?php $logoVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/img/logo.png') ?: time(); ?>
			<a href="/academia/index.php"><img src="/academia/img/logo.png?v=<?php echo $logoVersion; ?>" alt="Academia Alma Mater"></a>
        </div>
        <div id="menu">
			<a href="/academia/index.php">Inicio</a>
			<a href="/academia/gestionAlumnos.php">Gestión Alumnos</a>
			<a href="/academia/gestionAsignaturas.php">Gestión Asignaturas</a>
        </div>
    </div>
</body>
</html>