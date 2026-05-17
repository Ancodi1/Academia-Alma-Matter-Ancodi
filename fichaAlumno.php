<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("controllers/MatriculaController.php");
require_once("models/csrf.php");

$idAlumno = isset($_GET['id']) ? intval($_GET['id']) : 0;
$alumnoController = new AlumnoController();
$matriculaController = new MatriculaController();
$alumno = $alumnoController->getAlumnoPorId($idAlumno);
$resumen = $alumno ? $alumnoController->getResumenAlumno($idAlumno) : null;
$matriculas = $alumno ? $matriculaController->getAsignaturasDeAlumno($idAlumno) : false;
$examenes = $alumno ? $alumnoController->getExamenesPorAlumno($idAlumno) : false;
$asistencias = $alumno ? $alumnoController->getAsistenciasPorAlumno($idAlumno) : false;
?>

<div id="contenido">
    <?php if (!$alumno): ?>
        <div class="aviso error">Alumno no encontrado.</div>
        <p><a href="/editorAlumnos.php">Volver al listado</a></p>
    <?php else: ?>
        <h1><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h1>
        <div class="dashboard-grid">
            <div class="metric-card"><span>Media</span><strong><?php echo $resumen && $resumen['media'] !== null ? number_format($resumen['media'], 2) : '-'; ?></strong></div>
            <div class="metric-card"><span>Exámenes</span><strong><?php echo $resumen ? intval($resumen['examenes']) : 0; ?></strong></div>
            <div class="metric-card"><span>Ausencias</span><strong><?php echo $resumen ? intval($resumen['ausencias']) : 0; ?></strong></div>
            <div class="metric-card"><span>Justificadas</span><strong><?php echo $resumen ? intval($resumen['justificadas']) : 0; ?></strong></div>
        </div>

        <section class="panel">
            <h2>Ficha del alumno</h2>
            <form action="controllers/accionesAlumno.php" method="post" class="form-grid">
                <input type="hidden" name="id" value="<?php echo $idAlumno; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="hidden" name="origen" value="ficha">
                <div><label>Nombre</label><input type="text" name="nombre" value="<?php echo htmlspecialchars($alumno['nombre']); ?>" required></div>
                <div><label>Apellidos</label><input type="text" name="apellidos" value="<?php echo htmlspecialchars($alumno['apellidos']); ?>" required></div>
                <div><label>Edad</label><input type="number" name="edad" min="1" max="120" value="<?php echo htmlspecialchars($alumno['edad']); ?>" required></div>
                <div><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($alumno['email'] ?? ''); ?>" required></div>
                <div><label>Teléfono</label><input type="text" name="telefono" value="<?php echo htmlspecialchars($alumno['telefono'] ?? ''); ?>"></div>
                <div><label>Curso actual</label><input type="text" name="curso_actual" value="<?php echo htmlspecialchars($alumno['curso_actual'] ?? ''); ?>"></div>
                <div><label>Centro escolar</label><input type="text" name="centro" value="<?php echo htmlspecialchars($alumno['centro'] ?? ''); ?>"></div>
                <div><label>Tutor legal</label><input type="text" name="tutor" value="<?php echo htmlspecialchars($alumno['tutor'] ?? ''); ?>"></div>
                <div><label>Contacto de emergencia</label><input type="text" name="contacto_emergencia" value="<?php echo htmlspecialchars($alumno['contacto_emergencia'] ?? ''); ?>"></div>
                <div><label>Fecha de alta</label><input type="date" name="fecha_alta" value="<?php echo htmlspecialchars($alumno['fecha_alta'] ?? date('Y-m-d')); ?>"></div>
                <div class="form-wide"><label>Dirección</label><input type="text" name="direccion" value="<?php echo htmlspecialchars($alumno['direccion'] ?? ''); ?>"></div>
                <div class="form-wide"><label>Observaciones</label><textarea name="observaciones"><?php echo htmlspecialchars($alumno['observaciones'] ?? ''); ?></textarea></div>
                <button type="submit" name="modificarAlumno">Guardar ficha</button>
            </form>
        </section>

        <div class="panel-grid">
            <section class="panel">
                <h2>Matrículas</h2>
                <?php if ($matriculas && $matriculas->num_rows > 0): ?>
                    <ul class="listaResumen">
                        <?php while ($m = $matriculas->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($m['nombre'] . ' · ' . $m['curso']); ?><br><small><?php echo htmlspecialchars($m['estado'] . ' desde ' . $m['fechaAlta']); ?></small></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="sinClases">Sin matrículas activas.</p>
                <?php endif; ?>
                <p><a class="btn-link" href="/matriculas.php?idAlumno=<?php echo $idAlumno; ?>">Editar matrículas</a></p>
                <p><a class="btn-link" href="/archivosAlumno.php?id=<?php echo $idAlumno; ?>">Archivos del alumno</a></p>
            </section>

            <section class="panel">
                <h2>Últimas asistencias</h2>
                <?php if ($asistencias && $asistencias->num_rows > 0): ?>
                    <ul class="listaResumen">
                        <?php while ($a = $asistencias->fetch_assoc()): ?>
                            <li><strong><?php echo htmlspecialchars($a['fecha']); ?></strong> <?php echo htmlspecialchars($a['estado']); ?><br><?php echo htmlspecialchars($a['asignatura'] . ' · ' . $a['curso']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="sinClases">Sin asistencias registradas.</p>
                <?php endif; ?>
            </section>
        </div>

        <section class="panel">
            <h2>Exámenes recientes</h2>
            <?php if ($examenes && $examenes->num_rows > 0): ?>
                <table>
                    <tr><td id="filaAlumnos">Asignatura</td><td id="filaAlumnos">Fecha</td><td id="filaAlumnos">Nota</td></tr>
                    <?php while ($e = $examenes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['asignatura'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($e['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($e['nota']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p class="sinClases">Sin exámenes registrados.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
