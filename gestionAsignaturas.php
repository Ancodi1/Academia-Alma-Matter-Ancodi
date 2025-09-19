<!DOCTYPE html>
<html>
	<head>
		<?php $cssVersion = @filemtime($_SERVER['DOCUMENT_ROOT'] . '/academia/academia.css') ?: time(); ?>
		<link rel="stylesheet" type="text/css" href="/academia/academia.css?v=<?php echo $cssVersion; ?>">
		<link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
		<meta charset="utf-8">
		<title>Academia Alma Mater </title>
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
		<a href="/academia/gestionAlumnos.php">Gestión Alumnos </a>
		<a href="/academia/gestionAsignaturas.php">Gestión Asignaturas </a>
		</div>
		</div>
		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Alma Mater </h1>
		 <h2>Nueva Asignatura </h2>
			<form id="nuevaAsignatura" action="controllers/nuevaAsignatura.php"
			method="post">
				<label for="nombreAsignatura">Nombre:</label> <br>
				<input type="text"
				id="nombreAsignatura" name="nombreAsignatura"> <br>
				<label for="cursoAsignatura">Curso:</label> <br>
				<input type="text"
				id="cursoAsignatura" name="cursoAsignatura"> <br>
				<input type="submit" onclick="return enviarFormulario()" 
				value="Dar Alta de Nueva Asignatura">
				<div id="error" style="color: red; margin-top: 10px;"></div>
			</form>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
	<script src="/academia/js/asignaturas.js"></script>
</html>
