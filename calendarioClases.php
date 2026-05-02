<?php require_once("views/cabecera.php"); ?>
<?php require_once("controllers/HorarioController.php"); ?>

<?php
$horarioController = new HorarioController();
$horario = $horarioController->getHorarioSemanal();
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
    <h1>Bienvenido a Alma Mater</h1>
    <h2>Calendario de Clases</h2>
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
                                <?php if (!empty($clase['profesor'])): ?>
                                    - <?php echo htmlspecialchars($clase['profesor']); ?>
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
