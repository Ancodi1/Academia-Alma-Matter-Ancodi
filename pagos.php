<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/PagoController.php");
require_once("models/csrf.php");

$controller = new PagoController();
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$alumnos = $controller->getAlumnos();
$pagos = $controller->getPagos($estado);
?>

<div id="contenido">
    <h1>Pagos y cuotas</h1>
    <div id="contenidoIndex">
        <h2>Mensualidades y recibos</h2>
        <p>Controla pagos pendientes, abonados y vencidos por alumno.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Operación realizada correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo guardar el pago.</div>'; ?>

    <section class="panel">
        <h2>Nuevo pago</h2>
        <form action="controllers/accionesPago.php" method="post" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <div>
                <label>Alumno</label>
                <select name="idAlumno" required>
                    <option value="">Seleccione</option>
                    <?php if ($alumnos): while ($a = $alumnos->fetch_assoc()): ?>
                        <option value="<?php echo intval($a['id']); ?>"><?php echo htmlspecialchars($a['apellidos'] . ', ' . $a['nombre']); ?></option>
                    <?php endwhile; endif; ?>
                </select>
            </div>
            <div><label>Concepto</label><input type="text" name="concepto" required></div>
            <div><label>Importe</label><input type="number" name="importe" min="0" step="0.01" required></div>
            <div><label>Vencimiento</label><input type="date" name="fechaVencimiento" value="<?php echo date('Y-m-d'); ?>" required></div>
            <div>
                <label>Estado</label>
                <select name="estado"><option>Pendiente</option><option>Pagado</option><option>Vencido</option></select>
            </div>
            <button type="submit" name="crearPago">Crear pago</button>
        </form>
    </section>

    <form method="GET" class="filter-bar">
        <div>
            <label>Estado</label>
            <select name="estado">
                <option value="">Todos</option>
                <?php foreach (['Pendiente','Pagado','Vencido'] as $op): ?>
                    <option value="<?php echo $op; ?>" <?php echo $estado === $op ? 'selected' : ''; ?>><?php echo $op; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Filtrar</button>
        <a class="btn-link" href="/exportar.php?tipo=pagos&formato=csv">Exportar CSV</a>
    </form>

    <section class="panel">
        <h2>Listado</h2>
        <table>
            <tr><td id="filaAlumnos">Alumno</td><td id="filaAlumnos">Concepto</td><td id="filaAlumnos">Importe</td><td id="filaAlumnos">Vencimiento</td><td id="filaAlumnos">Estado</td><td id="filaAlumnos">Acción</td></tr>
            <?php if ($pagos): while ($p = $pagos->fetch_assoc()): ?>
                <form action="controllers/accionesPago.php" method="post">
                    <tr>
                        <td><?php echo htmlspecialchars($p['apellidos'] . ', ' . $p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['concepto']); ?></td>
                        <td><?php echo number_format($p['importe'], 2); ?> €</td>
                        <td><?php echo htmlspecialchars($p['fechaVencimiento']); ?></td>
                        <td><select name="estado"><option <?php echo $p['estado']==='Pendiente'?'selected':''; ?>>Pendiente</option><option <?php echo $p['estado']==='Pagado'?'selected':''; ?>>Pagado</option><option <?php echo $p['estado']==='Vencido'?'selected':''; ?>>Vencido</option></select></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo intval($p['id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <button type="submit" name="actualizarPago">Guardar</button>
                        </td>
                    </tr>
                </form>
            <?php endwhile; endif; ?>
        </table>
    </section>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
