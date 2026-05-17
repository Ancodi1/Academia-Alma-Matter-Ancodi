<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>
<?php require_once("controllers/HorarioController.php"); ?>

<?php
$horarioController = new HorarioController();
$idAsignatura = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : 0;
$idProfesor = isset($_GET['profesor']) ? intval($_GET['profesor']) : 0;
$aulaFiltro = isset($_GET['aula']) ? trim($_GET['aula']) : '';
$asignaturas = $horarioController->getAsignaturas();
$profesores = $horarioController->getProfesores();
$horario = $horarioController->getHorarioSemanal($idAsignatura, $idProfesor, $aulaFiltro);
$semanal = [
    'Lunes' => [],
    'Martes' => [],
    'Miércoles' => [],
    'Jueves' => [],
    'Viernes' => [],
    'Sábado' => [],
    'Domingo' => []
];

if ($horario && $horario->num_rows > 0) {
    while ($fila = $horario->fetch_assoc()) {
        $semanal[$fila['diaSemana']][] = $fila;
    }
}
?>

<div id="contenido">
    <h1>Bienvenido a Refuerzo Escolar</h1>
    <h2>Calendario de Clases</h2>

    <form method="GET" class="filter-bar">
        <div>
            <label for="asignatura">Asignatura</label>
            <select id="asignatura" name="asignatura">
                <option value="0">Todas</option>
                <?php if ($asignaturas): while ($row = $asignaturas->fetch_assoc()): ?>
                    <option value="<?php echo intval($row['id']); ?>" <?php echo $idAsignatura === intval($row['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nombre'] . ' · ' . $row['curso']); ?></option>
                <?php endwhile; endif; ?>
            </select>
        </div>
        <div>
            <label for="profesor">Profesor</label>
            <select id="profesor" name="profesor">
                <option value="0">Todos</option>
                <?php if ($profesores): while ($row = $profesores->fetch_assoc()): ?>
                    <option value="<?php echo intval($row['id']); ?>" <?php echo $idProfesor === intval($row['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['apellidos'] . ', ' . $row['nombre']); ?></option>
                <?php endwhile; endif; ?>
            </select>
        </div>
        <div>
            <label for="aula">Aula</label>
            <input id="aula" type="text" name="aula" value="<?php echo htmlspecialchars($aulaFiltro); ?>">
        </div>
        <button type="submit">Filtrar</button>
        <a class="btn-link" href="nuevoHorario.php">Agregar clase</a>
    </form>

    <div id="calendarioSemana">
        <?php foreach ($semanal as $dia => $clases): ?>
            <section class="diaSemana">
                <h3><?php echo $dia; ?></h3>
                <?php if (count($clases) > 0): ?>
                    <ul>
                        <?php foreach ($clases as $clase): ?>
                            <li>
                                <span class="hora"><?php echo htmlspecialchars(substr($clase['horaInicio'], 0, 5)); ?> - <?php echo htmlspecialchars(substr($clase['horaFin'], 0, 5)); ?></span>
                                <strong><?php echo htmlspecialchars($clase['asignatura']); ?></strong>
                                <span class="curso">(<?php echo htmlspecialchars($clase['curso']); ?>)</span>
                                <br>
                                Aula <?php echo htmlspecialchars($clase['aula']); ?>
                                <?php
                                $profesorNombre = trim(($clase['profesorNombre'] ?? '') . ' ' . ($clase['profesorApellidos'] ?? ''));
                                if ($profesorNombre === '') $profesorNombre = $clase['profesor'] ?? '';
                                ?>
                                <?php if (!empty($profesorNombre)): ?>
                                    - <?php echo htmlspecialchars($profesorNombre); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="sinClases">No hay clases programadas.</p>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
