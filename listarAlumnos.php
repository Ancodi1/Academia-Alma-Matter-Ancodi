<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("controllers/AsignaturaController.php");

$alumnoController = new AlumnoController();
$asignaturaController = new AsignaturaController();

$cursoSeleccionado = isset($_GET['curso']) ? trim($_GET['curso']) : '';
$idAsignaturaSeleccionada = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : 0;

$cursos = $asignaturaController->getCursos();
$asignaturas = $asignaturaController->getAsignaturasPorCurso($cursoSeleccionado);
$todasAsignaturas = $asignaturaController->getAsignaturasPorCurso();

$alumnos = $alumnoController->getAlumnosPorFiltro($cursoSeleccionado, $idAsignaturaSeleccionada);

$asignaturasArray = [];
if ($todasAsignaturas) {
    while ($row = $todasAsignaturas->fetch_assoc()) {
        $asignaturasArray[] = $row;
    }
}
?>
<!-- Divisor del Contenido -->
<div id="contenido">
    <h1>Lista de Alumnos</h1>
    <h3>Filtrar por curso y asignatura</h3>

    <div class="toolbar">
        <a class="btn-link" href="/exportar.php?tipo=alumnos&formato=csv">Exportar CSV</a>
        <a class="btn-link" href="/exportar.php?tipo=alumnos&formato=xls">Exportar Excel</a>
        <a class="btn-link" href="/exportar.php?tipo=alumnos&formato=pdf" target="_blank">Exportar PDF</a>
    </div>

    <form method="GET" style="margin: 20px 0; display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <div>
            <label for="cursoSelect">Curso</label><br>
            <select id="cursoSelect" name="curso" style="padding: 8px; min-width: 220px;">
                <option value="">Todos los cursos</option>
                <?php while ($curso = $cursos->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($curso['curso']); ?>" <?php echo $cursoSeleccionado === $curso['curso'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($curso['curso']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label for="asignaturaSelect">Asignatura</label><br>
            <select id="asignaturaSelect" name="asignatura" style="padding: 8px; min-width: 220px;">
                <option value="0">Todas las asignaturas</option>
                <?php if ($asignaturas): ?>
                    <?php while ($asignatura = $asignaturas->fetch_assoc()): ?>
                        <option value="<?php echo intval($asignatura['id']); ?>" <?php echo $idAsignaturaSeleccionada === intval($asignatura['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <button type="submit" style="padding: 10px 16px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">Aplicar filtro</button>
        </div>

        <div>
            <a href="listarAlumnos.php" style="display: inline-block; padding: 10px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Limpiar filtros</a>
        </div>
    </form>

    <?php if ($alumnos && $alumnos->num_rows > 0): ?>
        <table id="tablaAlumnos">
            <tr>
                <td id="filaAlumnos">ID</td>
                <td id="filaAlumnos">Nombre</td>
                <td id="filaAlumnos">Apellidos</td>
                <td id="filaAlumnos">Edad</td>
                <td id="filaAlumnos">Email</td>
            </tr>
            <?php while ($alumno = $alumnos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['id']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['edad']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['email'] ?? ''); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay alumnos que coincidan con el filtro seleccionado.</p>
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <a href="gestionAlumnos.php" style="display: inline-block; padding: 10px 20px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 5px;">Volver a Gestión de Alumnos</a>
    </div>

<?php require_once("views/pieDePagina.php"); ?>

<script>
    const todasAsignaturas = <?php echo json_encode($asignaturasArray, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const cursoSelect = document.getElementById('cursoSelect');
    const asignaturaSelect = document.getElementById('asignaturaSelect');

    function actualizarAsignaturas() {
        const curso = cursoSelect.value;
        asignaturaSelect.innerHTML = '<option value="0">Todas las asignaturas</option>';
        const asignaturasFiltradas = todasAsignaturas.filter(a => !curso || a.curso === curso);
        asignaturasFiltradas.forEach(a => {
            const option = document.createElement('option');
            option.value = a.id;
            option.textContent = a.nombre;
            asignaturaSelect.appendChild(option);
        });
    }

    cursoSelect.addEventListener('change', actualizarAsignaturas);
</script>
</body>
</html>
