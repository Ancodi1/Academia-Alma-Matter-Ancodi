<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AsistenciaController.php");
require_once("models/csrf.php");

$controller = new AsistenciaController();
$asignaturas = $controller->getAsignaturas();
$idAsignatura = isset($_GET['idAsignatura']) ? intval($_GET['idAsignatura']) : 0;
$fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : date('Y-m-d');
$alumnos = $idAsignatura > 0 ? $controller->getAlumnosMatriculados($idAsignatura) : false;
?>

<div id="contenido">
    <h1>Asistencia por clase</h1>
    <div id="contenidoIndex">
        <h2>Registro en bloque</h2>
        <p>Selecciona asignatura y fecha para marcar la asistencia de todo el grupo matriculado.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Asistencia guardada correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo guardar la asistencia.</div>'; ?>

    <form method="GET" class="filter-bar">
        <div>
            <label for="idAsignatura">Asignatura</label>
            <select id="idAsignatura" name="idAsignatura">
                <option value="0">Seleccione una asignatura</option>
                <?php if ($asignaturas): ?>
                    <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>
                        <option value="<?php echo intval($asignatura['id']); ?>" <?php echo $idAsignatura === intval($asignatura['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre'] . ' · ' . $asignatura['curso']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label for="fecha">Fecha</label>
            <input id="fecha" type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
        </div>
        <button type="submit">Cargar clase</button>
    </form>

    <?php if ($idAsignatura > 0): ?>
        <?php if ($alumnos && $alumnos->num_rows > 0): ?>
            <form action="controllers/asistenciaBloque.php" method="post" class="panel">
                <input type="hidden" name="idAsignatura" value="<?php echo $idAsignatura; ?>">
                <input type="hidden" name="fechaAsistencia" value="<?php echo htmlspecialchars($fecha); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <table>
                    <tr>
                        <td id="filaAlumnos">Alumno</td>
                        <td id="filaAlumnos">Estado</td>
                    </tr>
                    <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombre']); ?></td>
                            <td>
                                <select name="estado[<?php echo intval($alumno['id']); ?>]">
                                    <option value="Presente">Presente</option>
                                    <option value="Ausente">Ausente</option>
                                    <option value="Justificada">Justificada</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <button type="submit">Guardar asistencia</button>
            </form>
        <?php else: ?>
            <div class="aviso error">No hay alumnos matriculados en esta asignatura. Añádelos desde Matrículas.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
