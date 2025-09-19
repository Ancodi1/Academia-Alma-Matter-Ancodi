<!DOCTYPE html>	
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="academia.css">
		<link href='https://fonts.googleapis.com/css?family=Actor' rel='stylesheet'>
		<meta charset="utf-8">
		<title>Academia Alma Mater </title>
	</head>
	<body>
        <?php require_once("views/cabecera.php"); ?>
		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Alma Mater </h1>
			<div id="contenidoIndex">
				Bienvenido al Gestor de Alumnos y Matrículas. ¿Qué desea hacer?
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">
				<a href="gestionAlumnos.php">
					<img src="img/logoEstudiante.png" alt="Gestión Alumnos" class="imagenGestion">
					<p>Gestión de Alumnos</p>
				</a>
			</div>
			<div class="opcionGestion">
				<a href="gestionAsignaturas.php">
					<img src="img/gestionAsignaturas.png" alt="Gestión Asignaturas" class="imagenGestion">
					<p>Gestión de Asignaturas</p>
				</a>
			</div>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
</html>
