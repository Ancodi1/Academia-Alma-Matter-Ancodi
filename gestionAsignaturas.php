<?php
require_once("views/cabecera.php");
requerirInterno();
require_once("controllers/AsignaturaController.php");

$asignaturaController = new AsignaturaController();
$totalAsignaturas = $asignaturaController->contarAsignaturas();
$todasAsignaturas = $asignaturaController->getTodasLasAsignaturas();
?>
		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Refuerzo Escolar </h1>
			<div id="contenidoIndex">
				<h2>Gestión de Asignaturas.</h2>
		</div>
		
		<!-- Opciones de Gestión -->
		<div id="opcionesGestion">
			<div class="opcionGestion">
				<a href="nuevoAsignatura.php">
					<img src="img/asignaturas.png" alt="Añadir Asignatura" class="imagenGestion">
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

		<!-- Listado de Asignaturas -->
		<div style="margin-top: 30px; padding: 20px; background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color);">
			<h3 style="margin: 0 0 20px 0; color: var(--text-color);">Listado de Asignaturas</h3>

			<?php if ($todasAsignaturas && $todasAsignaturas->num_rows > 0): ?>
				<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
					<?php while ($asignatura = $todasAsignaturas->fetch_assoc()): ?>
						<div style="padding: 15px; background: var(--table-row-bg); border-radius: 6px; border: 1px solid var(--border-color);">
							<h4 style="margin: 0 0 8px 0; color: var(--text-color); font-size: 16px;">
								<?php echo htmlspecialchars($asignatura['nombre']); ?>
							</h4>
							<p style="margin: 0; color: var(--text-color); opacity: 0.8; font-size: 14px;">
								<strong>Curso:</strong> <?php echo htmlspecialchars($asignatura['curso']); ?>
							</p>
							<p style="margin: 5px 0 0 0; color: var(--text-color); opacity: 0.7; font-size: 12px;">
								ID: <?php echo htmlspecialchars($asignatura['id']); ?>
							</p>
						</div>
					<?php endwhile; ?>
				</div>
			<?php else: ?>
				<p style="margin: 0; color: var(--text-color); font-style: italic;">No hay asignaturas registradas en el sistema.</p>
			<?php endif; ?>
		</div>

		<!-- Estadísticas -->
		<div style="margin-top: 30px; padding: 10px 15px; background: var(--table-row-bg); border-radius: 6px; border: 1px solid var(--border-color); text-align: center;">
			<p style="margin: 0; font-size: 14px; color: var(--text-color);">
				<strong>Total de asignaturas registradas:</strong> <?php echo $totalAsignaturas; ?>
			</p>
		</div>

		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
	<script src="/js/asignaturas.js"></script>
</html>
