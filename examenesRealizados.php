<?php
require("views/cabecera.php");
require_once("controllers/AlumnoController.php");

$alumnoController = new AlumnoController();
$idAlumno = isset($_GET['id']) ? intval($_GET['id']) : 0;
$examenes = $alumnoController->getExamenesPorAlumno($idAlumno);
?>
<div id="contenido">
	<h1>Exámenes del Alumno</h1>
	<?php
	if (isset($_GET['mensaje'])) {
		if ($_GET['mensaje'] === 'examen_actualizado') echo '<div class="aviso exito">Examen actualizado correctamente.</div>';
		if ($_GET['mensaje'] === 'examen_eliminado') echo '<div class="aviso exito">Examen eliminado correctamente.</div>';
	}
	if (isset($_GET['error'])) {
		if ($_GET['error'] === 'examen_validacion') echo '<div class="aviso error">Datos de examen inválidos.</div>';
		if ($_GET['error'] === 'examen_actualizar') echo '<div class="aviso error">No se pudo actualizar el examen.</div>';
		if ($_GET['error'] === 'examen_eliminar') echo '<div class="aviso error">No se pudo eliminar el examen.</div>';
	}
	?>
	<table>
		<tr>
			<td id="filaAlumnos">Asignatura</td>
			<td id="filaAlumnos">Fecha</td>
			<td id="filaAlumnos">Nota</td>
			<td id="filaAlumnos">Acciones</td>
		</tr>
		<?php while ($row = $examenes->fetch_assoc()) { ?>
			<form action="controllers/accionesExamen.php" method="post">
			<tr>
				<td id="filaAlumnos"><?php echo $row['asignatura'] ?: '—'; ?></td>
				<td id="filaAlumnos"><input type="date" name="nuevaFecha" value="<?php echo htmlspecialchars($row['fecha']); ?>" disabled></td>
				<td id="filaAlumnos"><input type="number" name="nuevaNota" min="0" max="10" step="0.01" value="<?php echo htmlspecialchars($row['nota']); ?>" disabled></td>
				<td id="filaAlumnos">
					<input type="hidden" name="idAlumno" value="<?php echo $idAlumno; ?>">
					<input type="hidden" name="idAsignatura" value="<?php echo htmlspecialchars($row['idAsignatura']); ?>">
					<input type="hidden" name="fecha" value="<?php echo htmlspecialchars($row['fecha']); ?>">
					<button type="button" class="btn-editar">Editar</button>
					<button type="submit" name="guardarExamen" class="btn-guardar" style="display:none;">Guardar</button>
					<button type="button" class="btn-cancelar" style="display:none;">Cancelar</button>
					<button type="submit" name="eliminarExamen" class="btn-cancelar">Eliminar</button>
				</td>
			</tr>
			</form>
		<?php } ?>
	</table>
	<p style="margin-top:20px;"><a href="/academia/editorAlumnos.php">Volver</a></p>
</div>
<?php require("views/pieDePagina.php"); ?>
<script src="/academia/js/asignaturas.js"></script>
</html>
<script>
document.addEventListener('DOMContentLoaded', function(){
	document.querySelectorAll('table form').forEach(function(form){
		var btnEditar = form.querySelector('.btn-editar');
		var btnGuardar = form.querySelector('.btn-guardar');
		var btnCancelar = form.querySelector('.btn-cancelar');
		var inputs = form.querySelectorAll('input[type="date"], input[type="number"]');
		if (btnEditar) btnEditar.addEventListener('click', function(){
			inputs.forEach(function(inp){ inp.dataset.original = inp.value; inp.disabled = false; });
			btnEditar.style.display = 'none'; if (btnGuardar) btnGuardar.style.display = ''; if (btnCancelar) btnCancelar.style.display = '';
		});
		if (btnCancelar) btnCancelar.addEventListener('click', function(e){
			e.preventDefault();
			inputs.forEach(function(inp){ if (inp.dataset.original !== undefined) inp.value = inp.dataset.original; inp.disabled = true; });
			if (btnGuardar) btnGuardar.style.display = 'none'; if (btnCancelar) btnCancelar.style.display = 'none'; if (btnEditar) btnEditar.style.display = '';
		});
	});
});
</script>
</html>


