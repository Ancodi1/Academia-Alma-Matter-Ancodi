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

    <div style="margin: 20px 0; text-align: center;">
        <a href="nuevoHorario.php" style="display: inline-block; padding: 12px 24px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease;">
            ➕ Agregar Nueva Clase
        </a>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: var(--text-color); opacity: 0.8;">
            <em>Nota: Debes estar logueado para crear clases</em>
        </p>
    </div>

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
