<?php require_once("views/cabecera.php"); ?>
<?php require_once("controllers/AsistenciaController.php"); ?>

<?php
$asistenciaController = new AsistenciaController();
$alumnos = $asistenciaController->getAlumnos();
$asignaturas = $asistenciaController->getAsignaturas();
?>

<div id="contenido">
    <h1>Bienvenido a Alma Mater</h1>
    <h2>Registrar Asistencia</h2>

    <?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error === 'campos_vacios') {
            echo '<div id="error" class="alert-error">Por favor complete todos los campos.</div>';
        } elseif ($error === 'base_datos') {
            echo '<div id="error" class="alert-error">Error al guardar la asistencia. Inténtelo de nuevo.</div>';
        }
    }
    ?>

    <form id="nuevaAsistencia" action="controllers/nuevaAsistencia.php" method="post">
        <label for="idAlumno">Alumno:</label>
        <select id="idAlumno" name="idAlumno">
            <option value="">Seleccione un alumno</option>
            <?php if ($alumnos && $alumnos->num_rows > 0): ?>
                <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                    <option value="<?php echo $alumno['id']; ?>"><?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombre']); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <label for="idAsignatura">Asignatura:</label>
        <select id="idAsignatura" name="idAsignatura">
            <option value="">Seleccione una asignatura</option>
            <?php if ($asignaturas && $asignaturas->num_rows > 0): ?>
                <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>
                    <option value="<?php echo $asignatura['id']; ?>"><?php echo htmlspecialchars($asignatura['nombre'] . ' - ' . $asignatura['curso']); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <label for="fechaAsistencia">Fecha:</label>
        <input type="date" id="fechaAsistencia" name="fechaAsistencia" value="<?php echo date('Y-m-d'); ?>">

        <label for="estadoAsistencia">Estado:</label>
        <select id="estadoAsistencia" name="estadoAsistencia">
            <option value="Presente">Presente</option>
            <option value="Ausente">Ausente</option>
            <option value="Justificada">Justificada</option>
        </select>

        <input type="submit" value="Registrar asistencia">
    </form>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
