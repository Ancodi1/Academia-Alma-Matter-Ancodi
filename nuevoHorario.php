<?php require_once("views/cabecera.php"); ?>
<?php require_once("controllers/HorarioController.php"); ?>

<?php
$horarioController = new HorarioController();
$asignaturas = $horarioController->getAsignaturas();
?>

<div id="contenido">
    <h1>Bienvenido a Alma Mater</h1>
    <h2>Crear Nuevo Horario</h2>

    <?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error === 'campos_vacios') {
            echo '<div id="error" class="alert-error">Por favor complete todos los datos del horario.</div>';
        } elseif ($error === 'base_datos') {
            echo '<div id="error" class="alert-error">Error al guardar el horario. Inténtelo de nuevo.</div>';
        }
    }
    ?>

    <form id="nuevoHorario" action="controllers/nuevoHorario.php" method="post">
        <label for="idAsignatura">Asignatura:</label>
        <select id="idAsignatura" name="idAsignatura">
            <option value="">Seleccione una asignatura</option>
            <?php if ($asignaturas && $asignaturas->num_rows > 0): ?>
                <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>
                    <option value="<?php echo $asignatura['id']; ?>"><?php echo htmlspecialchars($asignatura['nombre'] . ' - ' . $asignatura['curso']); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <label for="diaSemana">Día de la semana:</label>
        <select id="diaSemana" name="diaSemana">
            <option value="">Seleccione un día</option>
            <option value="Lunes">Lunes</option>
            <option value="Martes">Martes</option>
            <option value="Miércoles">Miércoles</option>
            <option value="Jueves">Jueves</option>
            <option value="Viernes">Viernes</option>
            <option value="Sábado">Sábado</option>
            <option value="Domingo">Domingo</option>
        </select>

        <label for="horaInicio">Hora inicio:</label>
        <input type="time" id="horaInicio" name="horaInicio">

        <label for="horaFin">Hora fin:</label>
        <input type="time" id="horaFin" name="horaFin">

        <label for="aula">Aula:</label>
        <input type="text" id="aula" name="aula" placeholder="Ej. Aula 12">

        <label for="profesor">Profesor (opcional):</label>
        <input type="text" id="profesor" name="profesor" placeholder="Nombre del profesor">

        <input type="submit" value="Guardar horario">
    </form>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
