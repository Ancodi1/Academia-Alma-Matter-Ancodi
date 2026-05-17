<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/MatriculaController.php");
require_once("models/csrf.php");

$controller = new MatriculaController();
$alumnos = $controller->getAlumnos();
$asignaturas = $controller->getAsignaturas();
$idAlumno = isset($_GET['idAlumno']) ? intval($_GET['idAlumno']) : 0;
$matriculasActuales = [];

if ($idAlumno > 0) {
    $actuales = $controller->getAsignaturasDeAlumno($idAlumno);
    if ($actuales) {
        while ($row = $actuales->fetch_assoc()) {
            $matriculasActuales[] = intval($row['idAsignatura']);
        }
    }
}
?>

<div id="contenido">
    <h1>Matrículas</h1>
    <div id="contenidoIndex">
        <h2>Asignación alumno-asignatura</h2>
        <p>Define las asignaturas activas de cada alumno para usar listados, asistencia por clase y reportes con datos fiables.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Matrículas guardadas correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudieron guardar las matrículas.</div>'; ?>

    <form method="GET" class="filter-bar">
        <label for="idAlumno">Alumno</label>
        <select id="idAlumno" name="idAlumno" onchange="this.form.submit()">
            <option value="0">Seleccione un alumno</option>
            <?php if ($alumnos): ?>
                <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                    <option value="<?php echo intval($alumno['id']); ?>" <?php echo $idAlumno === intval($alumno['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
    </form>

    <?php if ($idAlumno > 0): ?>
        <form action="controllers/accionesMatricula.php" method="post" class="panel">
            <input type="hidden" name="idAlumno" value="<?php echo $idAlumno; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <h2>Asignaturas</h2>
            <div class="checkbox-grid">
                <?php if ($asignaturas): ?>
                    <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>
                        <label>
                            <input type="checkbox" name="asignaturas[]" value="<?php echo intval($asignatura['id']); ?>" <?php echo in_array(intval($asignatura['id']), $matriculasActuales) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre'] . ' · ' . $asignatura['curso']); ?>
                        </label>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <button type="submit">Guardar matrículas</button>
        </form>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
