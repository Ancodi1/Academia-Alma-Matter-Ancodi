<?php
require("views/cabecera.php");
require_once("controllers/PortalController.php");

$idAlumnoSesion = isset($_SESSION['idAlumno']) ? intval($_SESSION['idAlumno']) : 0;
$idAlumno = usuarioActualEsAdmin() && isset($_GET['idAlumno']) ? intval($_GET['idAlumno']) : $idAlumnoSesion;
$controller = new PortalController();
$alumno = $idAlumno > 0 ? $controller->getAlumnoPortal($idAlumno) : null;
?>

<div id="contenido">
    <?php if (!$alumno): ?>
        <h1>Portal</h1>
        <div class="aviso error">Este usuario no tiene un alumno vinculado.</div>
    <?php else:
        $resumen = $controller->getResumen($idAlumno);
        $matriculas = $controller->getMatriculas($idAlumno);
        $examenes = $controller->getExamenes($idAlumno);
        $asistencias = $controller->getAsistencias($idAlumno);
        $pagos = $controller->getPagos($idAlumno);
    ?>
        <h1>Portal de <?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h1>
        <div class="dashboard-grid">
            <div class="metric-card"><span>Media</span><strong><?php echo $resumen && $resumen['media'] !== null ? number_format($resumen['media'], 2) : '-'; ?></strong></div>
            <div class="metric-card"><span>Exámenes</span><strong><?php echo $resumen ? intval($resumen['examenes']) : 0; ?></strong></div>
            <div class="metric-card"><span>Ausencias</span><strong><?php echo $resumen ? intval($resumen['ausencias']) : 0; ?></strong></div>
            <div class="metric-card"><span>Justificadas</span><strong><?php echo $resumen ? intval($resumen['justificadas']) : 0; ?></strong></div>
        </div>

        <div class="panel-grid">
            <section class="panel">
                <h2>Asignaturas</h2>
                <ul class="listaResumen">
                    <?php if ($matriculas): while ($m = $matriculas->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($m['nombre'] . ' · ' . $m['curso']); ?></li>
                    <?php endwhile; endif; ?>
                </ul>
            </section>
            <section class="panel">
                <h2>Pagos</h2>
                <ul class="listaResumen">
                    <?php if ($pagos): while ($p = $pagos->fetch_assoc()): ?>
                        <li><strong><?php echo htmlspecialchars($p['estado']); ?></strong> <?php echo htmlspecialchars($p['concepto']); ?><br><?php echo number_format($p['importe'], 2); ?> € · <?php echo htmlspecialchars($p['fechaVencimiento']); ?></li>
                    <?php endwhile; endif; ?>
                </ul>
            </section>
        </div>

        <section class="panel">
            <h2>Notas</h2>
            <table>
                <tr><td id="filaAlumnos">Asignatura</td><td id="filaAlumnos">Fecha</td><td id="filaAlumnos">Nota</td></tr>
                <?php if ($examenes): while ($e = $examenes->fetch_assoc()): ?>
                    <tr><td><?php echo htmlspecialchars($e['asignatura'] ?: '-'); ?></td><td><?php echo htmlspecialchars($e['fecha']); ?></td><td><?php echo htmlspecialchars($e['nota']); ?></td></tr>
                <?php endwhile; endif; ?>
            </table>
        </section>

        <section class="panel">
            <h2>Asistencia</h2>
            <table>
                <tr><td id="filaAlumnos">Fecha</td><td id="filaAlumnos">Asignatura</td><td id="filaAlumnos">Estado</td></tr>
                <?php if ($asistencias): while ($a = $asistencias->fetch_assoc()): ?>
                    <tr><td><?php echo htmlspecialchars($a['fecha']); ?></td><td><?php echo htmlspecialchars($a['asignatura']); ?></td><td><?php echo htmlspecialchars($a['estado']); ?></td></tr>
                <?php endwhile; endif; ?>
            </table>
        </section>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
