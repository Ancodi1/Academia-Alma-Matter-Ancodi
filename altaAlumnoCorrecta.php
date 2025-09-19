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
				<h2>Gestión de Alumnos.</h2> 
                <h2>Alumno dado de alta correctamente</h2>
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">

            <a href="gestionAlumnos.php">
					<img src="img/volver.png" alt="Volver" class="imagenGestion">
					<p>Volver a Gestión de Alumnos</p>
				</a>
        </div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
</html>  