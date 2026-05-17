<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("controllers/AsignaturaController.php");

$alumnoController = new AlumnoController();
$asignaturaController = new AsignaturaController();
$conexion = $alumnoController->getConexion();

$curso = isset($_GET['curso']) ? trim($_GET['curso']) : '';
$idAsignatura = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : 0;
$desde = isset($_GET['desde']) ? trim($_GET['desde']) : '';
$hasta = isset($_GET['hasta']) ? trim($_GET['hasta']) : '';

$cursos = $asignaturaController->getCursos();
$asignaturas = $asignaturaController->getAsignaturasPorCurso($curso);

$whereExamen = [];
$params = [];
$types = "";
if ($curso !== '') { $whereExamen[] = "a.curso = ?"; $params[] = $curso; $types .= "s"; }
if ($idAsignatura > 0) { $whereExamen[] = "a.id = ?"; $params[] = $idAsignatura; $types .= "i"; }
if ($desde !== '') { $whereExamen[] = "e.fecha >= ?"; $params[] = $desde; $types .= "s"; }
if ($hasta !== '') { $whereExamen[] = "e.fecha <= ?"; $params[] = $hasta; $types .= "s"; }
$whereSql = $whereExamen ? " WHERE " . implode(" AND ", $whereExamen) : "";

$sqlAsignatura = "SELECT a.nombre AS asignatura, a.curso, AVG(e.nota) AS promedio, COUNT(e.idAlumno) AS num_examenes " .
    "FROM Examen e JOIN Asignatura a ON e.idAsignatura = a.id" . $whereSql .
    " GROUP BY e.idAsignatura, a.nombre, a.curso ORDER BY promedio DESC";
$stmt = $conexion->preparar($sqlAsignatura);
if ($stmt && $types) $stmt->bind_param($types, ...$params);
if ($stmt) { $stmt->execute(); $promediosAsignatura = $stmt->get_result(); } else { $promediosAsignatura = false; }

$sqlAlumno = "SELECT al.id, al.nombre, al.apellidos, AVG(e.nota) AS promedio, COUNT(e.idAsignatura) AS num_examenes " .
    "FROM Examen e JOIN Alumno al ON e.idAlumno = al.id JOIN Asignatura a ON e.idAsignatura = a.id" . $whereSql .
    " GROUP BY e.idAlumno, al.id, al.nombre, al.apellidos ORDER BY promedio DESC";
$stmtAlumno = $conexion->preparar($sqlAlumno);
if ($stmtAlumno && $types) $stmtAlumno->bind_param($types, ...$params);
if ($stmtAlumno) { $stmtAlumno->execute(); $promediosAlumno = $stmtAlumno->get_result(); } else { $promediosAlumno = false; }

$whereAsistencia = [];
$paramsAsistencia = [];
$typesAsistencia = "";
if ($curso !== '') { $whereAsistencia[] = "a.curso = ?"; $paramsAsistencia[] = $curso; $typesAsistencia .= "s"; }
if ($idAsignatura > 0) { $whereAsistencia[] = "a.id = ?"; $paramsAsistencia[] = $idAsignatura; $typesAsistencia .= "i"; }
if ($desde !== '') { $whereAsistencia[] = "asi.fecha >= ?"; $paramsAsistencia[] = $desde; $typesAsistencia .= "s"; }
if ($hasta !== '') { $whereAsistencia[] = "asi.fecha <= ?"; $paramsAsistencia[] = $hasta; $typesAsistencia .= "s"; }
$whereAsistenciaSql = $whereAsistencia ? " WHERE " . implode(" AND ", $whereAsistencia) : "";
$sqlAsistencia = "SELECT al.id, al.nombre, al.apellidos, " .
    "SUM(CASE WHEN asi.estado = 'Presente' THEN 1 ELSE 0 END) AS presentes, " .
    "SUM(CASE WHEN asi.estado = 'Ausente' THEN 1 ELSE 0 END) AS ausentes, " .
    "SUM(CASE WHEN asi.estado = 'Justificada' THEN 1 ELSE 0 END) AS justificadas " .
    "FROM Asistencia asi JOIN Alumno al ON al.id = asi.idAlumno JOIN Asignatura a ON a.id = asi.idAsignatura" .
    $whereAsistenciaSql . " GROUP BY al.id, al.nombre, al.apellidos ORDER BY ausentes DESC, justificadas DESC";
$stmtAsistencia = $conexion->preparar($sqlAsistencia);
if ($stmtAsistencia && $typesAsistencia) $stmtAsistencia->bind_param($typesAsistencia, ...$paramsAsistencia);
if ($stmtAsistencia) { $stmtAsistencia->execute(); $asistenciaAlumno = $stmtAsistencia->get_result(); } else { $asistenciaAlumno = false; }
?>

<div id="contenido">
    <h1>Reportes y Estadísticas</h1>

    <div class="toolbar">
        <a class="btn-link" href="/exportar.php?tipo=reportes&formato=csv">Notas CSV</a>
        <a class="btn-link" href="/exportar.php?tipo=reportes&formato=xls">Notas Excel</a>
        <a class="btn-link" href="/exportar.php?tipo=reportes&formato=pdf" target="_blank">Notas PDF</a>
        <a class="btn-link" href="/exportar.php?tipo=asistencia&formato=csv">Asistencia CSV</a>
        <a class="btn-link" href="/exportar.php?tipo=alumnos&formato=xls">Alumnos Excel</a>
    </div>

    <form method="GET" class="filter-bar">
        <div>
            <label for="curso">Curso</label>
            <select id="curso" name="curso">
                <option value="">Todos</option>
                <?php if ($cursos): while ($row = $cursos->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['curso']); ?>" <?php echo $curso === $row['curso'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['curso']); ?></option>
                <?php endwhile; endif; ?>
            </select>
        </div>
        <div>
            <label for="asignatura">Asignatura</label>
            <select id="asignatura" name="asignatura">
                <option value="0">Todas</option>
                <?php if ($asignaturas): while ($row = $asignaturas->fetch_assoc()): ?>
                    <option value="<?php echo intval($row['id']); ?>" <?php echo $idAsignatura === intval($row['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nombre']); ?></option>
                <?php endwhile; endif; ?>
            </select>
        </div>
        <div><label for="desde">Desde</label><input id="desde" type="date" name="desde" value="<?php echo htmlspecialchars($desde); ?>"></div>
        <div><label for="hasta">Hasta</label><input id="hasta" type="date" name="hasta" value="<?php echo htmlspecialchars($hasta); ?>"></div>
        <button type="submit">Filtrar</button>
        <a class="btn-link" href="/reportes.php">Limpiar</a>
    </form>

    <section class="panel">
        <h2>Promedios por asignatura</h2>
        <table>
            <tr><td id="filaAlumnos">Asignatura</td><td id="filaAlumnos">Curso</td><td id="filaAlumnos">Promedio</td><td id="filaAlumnos">Exámenes</td></tr>
            <?php if ($promediosAsignatura): while ($row = $promediosAsignatura->fetch_assoc()): ?>
                <tr><td><?php echo htmlspecialchars($row['asignatura']); ?></td><td><?php echo htmlspecialchars($row['curso']); ?></td><td><?php echo number_format($row['promedio'], 2); ?></td><td><?php echo intval($row['num_examenes']); ?></td></tr>
            <?php endwhile; endif; ?>
        </table>
    </section>

    <section class="panel">
        <h2>Promedios por alumno</h2>
        <table>
            <tr><td id="filaAlumnos">Alumno</td><td id="filaAlumnos">Promedio</td><td id="filaAlumnos">Exámenes</td><td id="filaAlumnos">Ficha</td></tr>
            <?php if ($promediosAlumno): while ($row = $promediosAlumno->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></td>
                    <td><?php echo number_format($row['promedio'], 2); ?></td>
                    <td><?php echo intval($row['num_examenes']); ?></td>
                    <td><a class="btn-link" href="/fichaAlumno.php?id=<?php echo intval($row['id']); ?>">Ver ficha</a></td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </section>

    <section class="panel">
        <h2>Asistencia por alumno</h2>
        <table>
            <tr><td id="filaAlumnos">Alumno</td><td id="filaAlumnos">Presentes</td><td id="filaAlumnos">Ausentes</td><td id="filaAlumnos">Justificadas</td></tr>
            <?php if ($asistenciaAlumno): while ($row = $asistenciaAlumno->fetch_assoc()): ?>
                <tr><td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></td><td><?php echo intval($row['presentes']); ?></td><td><?php echo intval($row['ausentes']); ?></td><td><?php echo intval($row['justificadas']); ?></td></tr>
            <?php endwhile; endif; ?>
        </table>
    </section>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
