<?php
require_once("views/cabecera.php");
requerirInterno();
require_once("controllers/DashboardController.php");

$dashboard = new DashboardController();
$resumen = $dashboard->getResumen();
$clasesHoy = $dashboard->getClasesHoy();
$ausencias = $dashboard->getAusenciasRecientes();
$riesgo = $dashboard->getAlumnosEnRiesgo();
?>

<div id="contenido">
    <h1>Refuerzo Escolar</h1>
    <div id="contenidoIndex">
        <h2>Panel de control</h2>
        <p>Resumen operativo de alumnos, clases, asistencia y rendimiento.</p>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'sin_permiso'): ?>
        <div class="aviso error">No tienes permisos para acceder a esa sección.</div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="metric-card">
            <span>Alumnos</span>
            <strong><?php echo $resumen['alumnos']; ?></strong>
        </div>
        <div class="metric-card">
            <span>Asignaturas</span>
            <strong><?php echo $resumen['asignaturas']; ?></strong>
        </div>
        <div class="metric-card">
            <span>Matrículas activas</span>
            <strong><?php echo $resumen['matriculas']; ?></strong>
        </div>
        <div class="metric-card">
            <span>Media general</span>
            <strong><?php echo $resumen['mediaGeneral'] !== null ? number_format($resumen['mediaGeneral'], 2) : '-'; ?></strong>
        </div>
    </div>

    <div class="panel-grid">
        <section class="panel">
            <h2>Clases de hoy</h2>
            <?php if ($clasesHoy && $clasesHoy->num_rows > 0): ?>
                <ul class="listaResumen">
                    <?php while ($clase = $clasesHoy->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars(substr($clase['horaInicio'], 0, 5) . ' - ' . substr($clase['horaFin'], 0, 5)); ?></strong>
                            <?php echo htmlspecialchars($clase['asignatura'] . ' (' . $clase['curso'] . ')'); ?><br>
                            <?php echo htmlspecialchars($clase['aula']); ?><?php echo $clase['profesor'] ? ' · ' . htmlspecialchars($clase['profesor']) : ''; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="sinClases">No hay clases programadas para hoy.</p>
            <?php endif; ?>
        </section>

        <section class="panel">
            <h2>Ausencias recientes</h2>
            <?php if ($ausencias && $ausencias->num_rows > 0): ?>
                <ul class="listaResumen">
                    <?php while ($row = $ausencias->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($row['fecha']); ?></strong>
                            <?php echo htmlspecialchars($row['apellidos'] . ', ' . $row['nombre']); ?><br>
                            <?php echo htmlspecialchars($row['asignatura'] . ' · ' . $row['estado']); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="sinClases">Sin ausencias recientes.</p>
            <?php endif; ?>
        </section>
    </div>

    <section class="panel">
        <h2>Alumnos que requieren seguimiento</h2>
        <?php if ($riesgo && $riesgo->num_rows > 0): ?>
            <table>
                <tr>
                    <td id="filaAlumnos">Alumno</td>
                    <td id="filaAlumnos">Media</td>
                    <td id="filaAlumnos">Ausencias</td>
                    <td id="filaAlumnos">Ficha</td>
                </tr>
                <?php while ($row = $riesgo->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['apellidos'] . ', ' . $row['nombre']); ?></td>
                        <td><?php echo $row['media'] !== null ? number_format($row['media'], 2) : '-'; ?></td>
                        <td><?php echo intval($row['ausencias']); ?></td>
                        <td><a class="btn-link" href="/fichaAlumno.php?id=<?php echo intval($row['id']); ?>">Ver ficha</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="sinClases">No hay alertas de rendimiento o asistencia.</p>
        <?php endif; ?>
    </section>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
