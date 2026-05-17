<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/TareaController.php");
require_once("models/csrf.php");

$controller = new TareaController();
$idAsignatura = isset($_GET['asignatura']) ? intval($_GET['asignatura']) : 0;
$idTarea = isset($_GET['idTarea']) ? intval($_GET['idTarea']) : 0;
$asignaturas = $controller->getAsignaturas();
$tareas = $controller->getTareas($idAsignatura);
$entregas = $idTarea > 0 ? $controller->getEntregas($idTarea) : false;
?>

<div id="contenido">
    <h1>Tareas y deberes</h1>
    <div id="contenidoIndex">
        <h2>Seguimiento de entregas</h2>
        <p>Crea tareas por asignatura y registra el estado de entrega de los alumnos matriculados.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Cambios guardados correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo completar la operación.</div>'; ?>

    <section class="panel">
        <h2>Nueva tarea</h2>
        <form action="controllers/accionesTarea.php" method="post" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <div>
                <label>Asignatura</label>
                <select name="idAsignatura" required>
                    <option value="">Seleccione</option>
                    <?php if ($asignaturas): while ($a = $asignaturas->fetch_assoc()): ?>
                        <option value="<?php echo intval($a['id']); ?>"><?php echo htmlspecialchars($a['nombre'] . ' · ' . $a['curso']); ?></option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div><label>Título</label><input type="text" name="titulo" required></div>
            <div><label>Fecha entrega</label><input type="date" name="fechaEntrega" required></div>
            <div class="form-wide"><label>Descripción</label><textarea name="descripcion"></textarea></div>
            <button type="submit" name="crearTarea">Crear tarea</button>
        </form>
    </section>

    <section class="panel">
        <h2>Tareas</h2>
        <table>
            <tr><td id="filaAlumnos">Tarea</td><td id="filaAlumnos">Asignatura</td><td id="filaAlumnos">Entrega</td><td id="filaAlumnos">Seguimiento</td></tr>
            <?php if ($tareas): while ($t = $tareas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($t['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($t['asignatura'] . ' · ' . $t['curso']); ?></td>
                    <td><?php echo htmlspecialchars($t['fechaEntrega']); ?></td>
                    <td><a class="btn-link" href="/tareas.php?idTarea=<?php echo intval($t['id']); ?>">Ver entregas</a></td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </section>

    <?php if ($idTarea > 0): ?>
        <section class="panel">
            <h2>Entregas</h2>
            <form action="controllers/accionesTarea.php" method="post">
                <input type="hidden" name="idTarea" value="<?php echo $idTarea; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <table>
                    <tr><td id="filaAlumnos">Alumno</td><td id="filaAlumnos">Estado</td><td id="filaAlumnos">Comentario</td></tr>
                    <?php if ($entregas): while ($e = $entregas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['apellidos'] . ', ' . $e['nombre']); ?></td>
                            <td><select name="estado[<?php echo intval($e['id']); ?>]"><option <?php echo ($e['estado'] ?? '')==='Pendiente'?'selected':''; ?>>Pendiente</option><option <?php echo ($e['estado'] ?? '')==='Entregada'?'selected':''; ?>>Entregada</option><option <?php echo ($e['estado'] ?? '')==='Revisada'?'selected':''; ?>>Revisada</option></select></td>
                            <td><input type="text" name="comentario[<?php echo intval($e['id']); ?>]" value="<?php echo htmlspecialchars($e['comentario'] ?? ''); ?>"></td>
                        </tr>
                    <?php endwhile; endif; ?>
                </table>
                <button type="submit" name="guardarEntregas">Guardar entregas</button>
            </form>
        </section>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
