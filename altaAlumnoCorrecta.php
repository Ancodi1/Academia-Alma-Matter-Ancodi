<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>

		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Refuerzo Escolar </h1>
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
