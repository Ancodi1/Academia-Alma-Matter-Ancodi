<?php require_once("views/cabecera.php"); ?>
<?php require_once("controllers/HorarioController.php"); ?>

<?php
$horarioController = new HorarioController();
$proximasClases = $horarioController->getProximasClases(4);
?>

<div id="contenido">
    <h1>Bienvenido a Alma Mater</h1>
    <div id="contenidoIndex">
        <h2>Gestión de Asistencia y Horarios</h2>
        <p>Añade control de asistencia, crea horarios de clase y revisa el calendario semanal.</p>
    </div>

    <div id="opcionesGestion">
        <div class="opcionGestion">
            <a href="nuevoAsistencia.php">
                <img src="img/estadisticas.png" alt="Añadir Asistencia" class="imagenGestion">
                <p>Añadir Asistencia</p>
            </a>
        </div>
        <div class="opcionGestion">
            <a href="nuevoHorario.php">
                <img src="img/estadisticas.png" alt="Nuevo Horario" class="imagenGestion">
                <p>Crear Horario</p>
            </a>
        </div>
        <div class="opcionGestion">
            <a href="calendarioClases.php">
                <img src="img/estadisticas.png" alt="Calendario" class="imagenGestion">
                <p>Calendario de Clases</p>
            </a>
        </div>
    </div>

    <div id="resumenModulo">
        <h3>Próximas clases</h3>
        <?php if ($proximasClases === false): ?>
            <div class="alert-error">No se puede cargar el módulo de horarios. Compruebe que las tablas `Horario` y `Asignatura` existen en la base de datos.</div>
        <?php elseif ($proximasClases && $proximasClases->num_rows > 0): ?>
            <ul class="listaResumen">
                <?php while ($clase = $proximasClases->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($clase['diaSemana']); ?></strong> -
                        <?php echo htmlspecialchars($clase['asignatura']); ?>
                        (<?php echo htmlspecialchars($clase['curso']); ?>) <br>
                        <?php echo htmlspecialchars(substr($clase['horaInicio'], 0, 5)); ?>
                        a <?php echo htmlspecialchars(substr($clase['horaFin'], 0, 5)); ?>
                        en <?php echo htmlspecialchars($clase['aula']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay clases programadas aún. Añade un horario para empezar.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
