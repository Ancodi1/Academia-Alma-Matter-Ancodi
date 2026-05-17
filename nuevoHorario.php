<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>
<?php require_once("controllers/HorarioController.php"); ?>
<?php require_once("models/csrf.php"); ?>

<?php
$horarioController = new HorarioController();
$asignaturas = $horarioController->getAsignaturas();
$profesores = $horarioController->getProfesores();
?>

<div id="contenido">
    <h1>Bienvenido a Refuerzo Escolar</h1>
    <h2>Crear Nuevo Horario</h2>

    <?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error === 'campos_vacios') {
            echo '<div id="error" class="alert-error">Por favor complete todos los datos del horario.</div>';
        } elseif ($error === 'base_datos') {
            echo '<div id="error" class="alert-error">Error al guardar el horario. Inténtelo de nuevo.</div>';
        } elseif ($error === 'solapamiento') {
            echo '<div id="error" class="alert-error">Ya existe una clase en esa franja para el aula o profesor seleccionado.</div>';
        } elseif ($error === 'horas') {
            echo '<div id="error" class="alert-error">La hora de fin debe ser posterior a la hora de inicio.</div>';
        }
    }
    ?>

    <form id="nuevoHorario" action="controllers/nuevoHorario.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
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

        <label for="idProfesor">Profesor registrado:</label>
        <select id="idProfesor" name="idProfesor">
            <option value="0">Sin asignar</option>
            <?php if ($profesores && $profesores->num_rows > 0): ?>
                <?php while ($profesor = $profesores->fetch_assoc()): ?>
                    <option value="<?php echo intval($profesor['id']); ?>"><?php echo htmlspecialchars($profesor['apellidos'] . ', ' . $profesor['nombre']); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <input type="submit" value="Guardar horario">
    </form>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
