<?php require_once("views/cabecera.php"); ?>
		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Alma Mater </h1>
			<div id="contenidoIndex">
				<h2>Gestión de Asignaturas.</h2> 
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">
				<a href="nuevoAsignatura.php">
					<img src="img/añadirAlumno.png" alt="Añadir Asignatura" class="imagenGestion">
					<p>Añadir Asignaturas</p>
				</a>
			</div>
			<div class="opcionGestion">
				<a href="editorAsignaturas.php">
					<img src="img/editarAlumno.png" alt="Editar Asignatura" class="imagenGestion">
					<p>Editar Asignaturas</p>
				</a>
			</div>
		</div>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
	<script src="/js/asignaturas.js"></script>
</html>
