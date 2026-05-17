<?php
require("views/cabecera.php");
require_once("models/auth.php");
require_once("models/csrf.php");
require_once("controllers/UsuarioController.php");

requerirAdmin();
$controller = new UsuarioController();
$usuarios = $controller->getUsuarios();
$alumnos = $controller->getAlumnos();
$alumnosLista = [];
if ($alumnos) {
    while ($a = $alumnos->fetch_assoc()) {
        $alumnosLista[] = $a;
    }
}
?>

<div id="contenido">
    <h1>Usuarios y roles</h1>
    <div id="contenidoIndex">
        <h2>Administración</h2>
        <p>Crea profesores, cambia roles y restablece contraseñas.</p>
    </div>

    <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Operación realizada correctamente.</div>'; ?>
    <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo completar la operación.</div>'; ?>

    <section class="panel">
        <h2>Nuevo usuario</h2>
        <form action="controllers/accionesUsuario.php" method="post" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <div>
                <label for="username">Usuario</label>
                <input id="username" type="text" name="username" required>
            </div>
            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="text" name="password" minlength="6" required>
            </div>
            <div>
                <label for="role">Rol</label>
                <select id="role" name="role">
                    <option value="teacher">Profesor</option>
                    <option value="admin">Administrador</option>
                    <option value="student">Alumno</option>
                    <option value="family">Familia</option>
                </select>
            </div>
            <div>
                <label for="idAlumno">Alumno vinculado</label>
                <select id="idAlumno" name="idAlumno">
                    <option value="0">Sin vincular</option>
                    <?php foreach ($alumnosLista as $a): ?>
                        <option value="<?php echo intval($a['id']); ?>"><?php echo htmlspecialchars($a['apellidos'] . ', ' . $a['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="crearUsuario">Crear usuario</button>
        </form>
    </section>

    <section class="panel">
        <h2>Usuarios existentes</h2>
        <table>
            <tr>
                <td id="filaAlumnos">Usuario</td>
                <td id="filaAlumnos">Rol</td>
                <td id="filaAlumnos">Alumno vinculado</td>
                <td id="filaAlumnos">Nueva contraseña</td>
                <td id="filaAlumnos">Acciones</td>
            </tr>
            <?php if ($usuarios): ?>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <form action="controllers/accionesUsuario.php" method="post">
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td>
                                <select name="role">
                                    <option value="teacher" <?php echo $usuario['role'] === 'teacher' ? 'selected' : ''; ?>>Profesor</option>
                                    <option value="admin" <?php echo $usuario['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="student" <?php echo $usuario['role'] === 'student' ? 'selected' : ''; ?>>Alumno</option>
                                    <option value="family" <?php echo $usuario['role'] === 'family' ? 'selected' : ''; ?>>Familia</option>
                                </select>
                            </td>
                            <td>
                                <select name="idAlumno">
                                    <option value="0">Sin vincular</option>
                                    <?php foreach ($alumnosLista as $a): ?>
                                        <option value="<?php echo intval($a['id']); ?>" <?php echo intval($usuario['idAlumno'] ?? 0) === intval($a['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['apellidos'] . ', ' . $a['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="password" placeholder="Dejar en blanco"></td>
                            <td>
                                <input type="hidden" name="id" value="<?php echo intval($usuario['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                                <button type="submit" name="actualizarUsuario">Guardar</button>
                                <button type="submit" name="eliminarUsuario" class="btn-cancelar">Eliminar</button>
                            </td>
                        </tr>
                    </form>
                <?php endwhile; ?>
            <?php endif; ?>
        </table>
    </section>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
