<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/ProfesorController.php");
require_once("models/csrf.php");

$controller = new ProfesorController();
$profesores = $controller->getProfesores();
?>

<div id="contenido">
    <h1>Profesores</h1>
    <div id="contenidoIndex">
        <h2>Equipo docente</h2>
        <p>Gestiona profesores para asignarlos a horarios y filtrar el calendario.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Operación realizada correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo completar la operación.</div>'; ?>

    <section class="panel">
        <h2>Nuevo profesor</h2>
        <form action="controllers/accionesProfesor.php" method="post" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <div><label>Nombre</label><input type="text" name="nombre" required></div>
            <div><label>Apellidos</label><input type="text" name="apellidos" required></div>
            <div><label>Email</label><input type="email" name="email"></div>
            <div><label>Teléfono</label><input type="text" name="telefono"></div>
            <div class="form-wide"><label>Especialidad</label><input type="text" name="especialidad"></div>
            <button type="submit" name="crearProfesor">Crear profesor</button>
        </form>
    </section>

    <section class="panel">
        <h2>Listado</h2>
        <table>
            <tr>
                <td id="filaAlumnos">Nombre</td>
                <td id="filaAlumnos">Email</td>
                <td id="filaAlumnos">Teléfono</td>
                <td id="filaAlumnos">Especialidad</td>
                <td id="filaAlumnos">Acciones</td>
            </tr>
            <?php if ($profesores): while ($p = $profesores->fetch_assoc()): ?>
                <form action="controllers/accionesProfesor.php" method="post">
                    <tr>
                        <td>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>">
                            <input type="text" name="apellidos" value="<?php echo htmlspecialchars($p['apellidos']); ?>">
                        </td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($p['email'] ?? ''); ?>"></td>
                        <td><input type="text" name="telefono" value="<?php echo htmlspecialchars($p['telefono'] ?? ''); ?>"></td>
                        <td><input type="text" name="especialidad" value="<?php echo htmlspecialchars($p['especialidad'] ?? ''); ?>"></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo intval($p['id']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <button type="submit" name="actualizarProfesor">Guardar</button>
                            <button type="submit" name="eliminarProfesor" class="btn-cancelar">Eliminar</button>
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
