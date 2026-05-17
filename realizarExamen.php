<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("models/csrf.php");

$alumnoController = new AlumnoController();

$idAlumno = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$asignaturas = $alumnoController->getTodasLasAsignaturas();
?>
<div id="contenido">
	<h1>Nuevo Examen</h1>
    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'campos') echo '<div class="aviso error">Completa todos los campos.</div>';
        if ($_GET['error'] === 'fecha') echo '<div class="aviso error">La fecha no es válida.</div>';
        if ($_GET['error'] === 'nota') echo '<div class="aviso error">La nota debe estar entre 0 y 10.</div>';
    }
    ?>
	<form action="controllers/realizarExamen.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
		<input type="hidden" name="idAlumno" value="<?php echo $idAlumno; ?>">
		<label>Asignatura</label><br>
		<select name="idAsignatura">
			<?php while($row = $asignaturas->fetch_assoc()) { ?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
			<?php } ?>
		</select><br>
		<label>Fecha</label><br>
		<input type="date" name="fecha"><br>
		<label>Nota</label><br>
		<input type="number" name="nota" min="0" max="10" step="0.01"><br>
		<button type="submit">Guardar Examen</button>
	</form>
</div>
<?php require("views/pieDePagina.php"); ?>
<script src="/js/asignaturas.js"></script>
</html>
