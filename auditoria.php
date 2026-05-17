<?php
require("views/cabecera.php");
require_once("models/auth.php");
require_once("controllers/AuditoriaController.php");

requerirAdmin();
$eventos = (new AuditoriaController())->getEventos();
?>

<div id="contenido">
    <h1>Auditoría</h1>
    <div id="contenidoIndex">
        <h2>Registro de actividad</h2>
        <p>Consulta cambios relevantes realizados en pagos, tareas y otros módulos sensibles.</p>
    </div>
    <section class="panel">
        <table>
            <tr><td id="filaAlumnos">Fecha</td><td id="filaAlumnos">Usuario</td><td id="filaAlumnos">Acción</td><td id="filaAlumnos">Entidad</td><td id="filaAlumnos">Detalle</td><td id="filaAlumnos">IP</td></tr>
            <?php if ($eventos): while ($e = $eventos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($e['username'] ?? 'Sistema'); ?></td>
                    <td><?php echo htmlspecialchars($e['accion']); ?></td>
                    <td><?php echo htmlspecialchars($e['entidad'] . ($e['entidadId'] ? ' #' . $e['entidadId'] : '')); ?></td>
                    <td><?php echo htmlspecialchars($e['detalle']); ?></td>
                    <td><?php echo htmlspecialchars($e['ip']); ?></td>
                </tr>
            <?php endwhile; endif; ?>
        </table>
    </section>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
