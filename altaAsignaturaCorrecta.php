<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>

		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Refuerzo Escolar </h1>
			<div id="contenidoIndex">
				<h2>Gestión de Asignaturas.</h2> 
                <h2>Asignatura dada de alta correctamente</h2>
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">

            <a href="gestionAsignaturas.php">
					<img src="img/volver.png" alt="Volver" class="imagenGestion">
					<p>Volver a Gestión de Asignaturas</p>
				</a>
        </div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
</html>
