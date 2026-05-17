<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("controllers/ArchivoController.php");
require_once("models/csrf.php");

$idAlumno = isset($_GET['id']) ? intval($_GET['id']) : 0;
$alumnoController = new AlumnoController();
$archivoController = new ArchivoController();
$alumno = $alumnoController->getAlumnoPorId($idAlumno);
$archivos = $idAlumno > 0 ? $archivoController->getArchivosAlumno($idAlumno) : false;
?>

<div id="contenido">
    <?php if (!$alumno): ?>
        <div class="aviso error">Alumno no encontrado.</div>
    <?php else: ?>
        <h1>Archivos de <?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h1>

        <?php if (isset($_GET['mensaje'])) echo '<div class="aviso exito">Archivo actualizado correctamente.</div>'; ?>
        <?php if (isset($_GET['error'])) echo '<div class="aviso error">No se pudo procesar el archivo.</div>'; ?>

        <section class="panel">
            <h2>Subir archivo</h2>
            <form action="controllers/accionesArchivo.php" method="post" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="idAlumno" value="<?php echo $idAlumno; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <div>
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="foto">Foto</option>
                        <option value="autorizacion">Autorización</option>
                        <option value="informe">Informe</option>
                        <option value="justificante">Justificante</option>
                        <option value="documento">Documento</option>
                    </select>
                </div>
                <div>
                    <label>Archivo</label>
                    <input type="file" name="archivoAlumno" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                </div>
                <button type="submit">Subir archivo</button>
            </form>
        </section>

        <section class="panel">
            <h2>Archivos</h2>
            <table>
                <tr><td id="filaAlumnos">Nombre</td><td id="filaAlumnos">Tipo</td><td id="filaAlumnos">Fecha</td><td id="filaAlumnos">Acciones</td></tr>
                <?php if ($archivos): while ($archivo = $archivos->fetch_assoc()): ?>
                    <form action="controllers/accionesArchivo.php" method="post">
                        <tr>
                            <td><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></td>
                            <td><?php echo htmlspecialchars($archivo['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($archivo['fecha_subida']); ?></td>
                            <td>
                                <a class="btn-link" href="/descargarArchivo.php?id=<?php echo intval($archivo['id']); ?>" target="_blank">Ver</a>
                                <input type="hidden" name="idAlumno" value="<?php echo $idAlumno; ?>">
                                <input type="hidden" name="idArchivo" value="<?php echo intval($archivo['id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                                <button type="submit" name="eliminarArchivo" class="btn-cancelar">Eliminar</button>
                            </td>
                        </tr>
                    </form>
                <?php endwhile; endif; ?>
            </table>
        </section>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
