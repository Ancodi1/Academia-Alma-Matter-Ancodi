<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>

		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Refuerzo Escolar </h1>
			<div id="contenidoIndex">
				<h2>Gestión de Alumnos.</h2> 
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">
				<a href="nuevoAlumno.php">
					<img src="img/añadirAlumno.png" alt="Añadir Alumno" class="imagenGestion">
					<p>Añadir Alumnos</p>
                  <!--  <a href="https://www.flaticon.es/iconos-gratis/pictograma" title="pictograma iconos">Pictograma iconos creados por Prosymbols Premium - Flaticon</a>-->
				</a>
			</div>
			<div class="opcionGestion">
				<a href="listarAlumnos.php">
					<img src="img/estadisticas.png" alt="Listar Alumnos" class="imagenGestion">
					<p>Ver Lista de Alumnos</p>
				</a>
			</div>
			<div class="opcionGestion">
				<a href="editorAlumnos.php">
					<img src="img/editarAlumno.png" alt="Editar Alumno" class="imagenGestion">
					<p>Editar Alumnos</p>
				</a>
			</div>
			<div class="opcionGestion">
				<a href="matriculas.php">
					<img src="img/asignaturas.png" alt="Matrículas" class="imagenGestion">
					<p>Matrículas</p>
				</a>
			</div>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
</html>
