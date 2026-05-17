<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/AlumnoController.php");
require_once("models/csrf.php");

$alumnoController = new AlumnoController();

// Búsqueda y paginación
$termino = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 5;

$totalAlumnos = $alumnoController->contarAlumnos($termino);
$totalPaginas = ceil($totalAlumnos / $porPagina);
$alumnos = $alumnoController->buscarAlumnos($termino, $pagina, $porPagina);

function mostrarBotonModificar($id)
{
    echo '<button type="button" class="btn-editar" title="Editar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
    echo '<button type="submit" name="modificarAlumno" class="btn-guardar" style="display:none;" title="Guardar"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';
    echo '<button type="button" class="btn-cancelar" style="display:none;" title="Cancelar"><i class="fa fa-times" aria-hidden="true"></i></button>';
}

function mostrarRealizarExamen($id)
{
    echo '<button type="submit" name="realizarExamen"><i class="fa fa-file-text-o" aria-hidden="true"></i></button>';
}

function mostrarBotonExamenesRealizados($id)
{
    echo '<button type="submit" name="verExamenesAlumno"><i class="fa fa-eye" aria-hidden="true"></i></button>';
}

function mostrarBotonEliminarAlumno($id)
{
    echo '<button type="submit" name="eliminarAlumno"><i class="fa fa-trash" aria-hidden="true"></i></button>';
}

function mostrarBotonFichaAlumno($id)
{
    echo '<a class="btn-link" href="/fichaAlumno.php?id=' . intval($id) . '">Ficha</a>';
}

?>
<!-- Divisor del Contenido -->
<div id="contenido">
    <h1>Gestor de Alumnos</h1>
    <h3>Listado de Alumnos</h3>
    
    <!-- Búsqueda -->
    <form method="GET" style="margin: 20px 0;">
        <input type="text" name="buscar" placeholder="Buscar por nombre o apellidos..." value="<?php echo htmlspecialchars($termino); ?>" style="width: 300px; padding: 8px;">
        <button type="submit">Buscar</button>
        <?php if ($termino): ?>
            <a href="editorAlumnos.php" style="margin-left: 10px;">Limpiar</a>
        <?php endif; ?>
    </form>
    
    <?php
	if (isset($_GET["mensaje"])) {
		// Para "modificado" mostraremos un modal, no un aviso en línea
		if ($_GET["mensaje"] === "eliminado") echo '<div class="aviso exito">Alumno eliminado correctamente</div>';
	}
    if (isset($_GET["error"])) {
        if ($_GET["error"] === "modificar") echo '<div class="aviso error">Error al modificar el alumno</div>';
        if ($_GET["error"] === "eliminar") echo '<div class="aviso error">Error al eliminar el alumno</div>';
        if ($_GET["error"] === "validacion_campos") echo '<div class="aviso error">Rellena nombre, apellidos, edad y email.</div>';
        if ($_GET["error"] === "edad_invalida") echo '<div class="aviso error">La edad debe estar entre 1 y 120.</div>';
        if ($_GET["error"] === "email_invalido") echo '<div class="aviso error">Email inválido.</div>';
        if ($_GET["error"] === "csrf") echo '<div class="aviso error">Token de seguridad inválido. Inténtalo de nuevo.</div>';
    }
    ?>
    <table id="tablaAlumnos">
        <tr>
            <td id="filaAlumnos">Nombre</td>
            <td id="filaAlumnos">Apellidos</td>
            <td id="filaAlumnos">Edad</td>
            <td id="filaAlumnos">Email</td>
            <td id="filaAlumnos">Teléfono</td>
            <td id="filaAlumnos">Curso</td>
            <td id="filaAlumnos">Acciones</td>
        </tr>
            <!-- Listamos a todos los Alumnos -->
            <?php
            // Leemos todos los alumnos
            while ($alumno = $alumnos->fetch_assoc()) {
                $idAlumno = $alumno["id"];
                echo '<form action="controllers/accionesAlumno.php" method="post">';
                echo '<tr>';
                echo '<input type="hidden" name="id" value="' . $idAlumno . '">';
                echo '<input type="hidden" name="csrf_token" value="' . generarTokenCSRF() . '">';
                echo '<td id="filaAlumnos"><input type="text" name="nombre" value="' . htmlspecialchars($alumno["nombre"]) . '" disabled></td>';
                echo '<td id="filaAlumnos"><input type="text" name="apellidos" value="' . htmlspecialchars($alumno["apellidos"]) . '" disabled></td>';
                echo '<td id="filaAlumnos"><input type="text" name="edad" value="' . htmlspecialchars($alumno["edad"]) . '" disabled></td>';
                echo '<td id="filaAlumnos"><input type="email" name="email" value="' . htmlspecialchars($alumno["email"] ?? '') . '" disabled></td>';
                echo '<td id="filaAlumnos"><input type="text" name="telefono" value="' . htmlspecialchars($alumno["telefono"] ?? '') . '" disabled></td>';
                echo '<td id="filaAlumnos"><input type="text" name="curso_actual" value="' . htmlspecialchars($alumno["curso_actual"] ?? '') . '" disabled></td>';
                echo '<input type="hidden" name="direccion" value="' . htmlspecialchars($alumno["direccion"] ?? '') . '">';
                echo '<input type="hidden" name="tutor" value="' . htmlspecialchars($alumno["tutor"] ?? '') . '">';
                echo '<input type="hidden" name="contacto_emergencia" value="' . htmlspecialchars($alumno["contacto_emergencia"] ?? '') . '">';
                echo '<input type="hidden" name="centro" value="' . htmlspecialchars($alumno["centro"] ?? '') . '">';
                echo '<input type="hidden" name="fecha_alta" value="' . htmlspecialchars($alumno["fecha_alta"] ?? date('Y-m-d')) . '">';
                echo '<input type="hidden" name="observaciones" value="' . htmlspecialchars($alumno["observaciones"] ?? '') . '">';
                // Creamos los botones para las acciones
                echo '<td id="filaAlumnos">';
                mostrarBotonFichaAlumno($idAlumno);
                // Acción Modificar Alumno
                mostrarBotonModificar($idAlumno);
                // Acción Realizar Examen
                mostrarRealizarExamen($idAlumno);
                // Si tiene exámenes => los mostramos
                $numeroExamenes = $alumnoController->getNumeroExamenes($idAlumno);
                if ($numeroExamenes > 0)
                    mostrarBotonExamenesRealizados($idAlumno);
                // Acción Borrar Alumno
                mostrarBotonEliminarAlumno($idAlumno);
                echo '</td></tr>';
                echo '</form>';
            }
            ?>
    </table>
    
    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
    <div style="margin-top: 20px; text-align: center;">
        <?php if ($pagina > 1): ?>
            <a href="?buscar=<?php echo urlencode($termino); ?>&pagina=<?php echo $pagina-1; ?>">« Anterior</a>
        <?php endif; ?>
        
        <?php for ($i = max(1, $pagina-2); $i <= min($totalPaginas, $pagina+2); $i++): ?>
            <?php if ($i == $pagina): ?>
                <strong style="margin: 0 10px;"><?php echo $i; ?></strong>
            <?php else: ?>
                <a href="?buscar=<?php echo urlencode($termino); ?>&pagina=<?php echo $i; ?>" style="margin: 0 5px;"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($pagina < $totalPaginas): ?>
            <a href="?buscar=<?php echo urlencode($termino); ?>&pagina=<?php echo $pagina+1; ?>">Siguiente »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
</div>
<!-- Modal de confirmación de guardado -->
<div id="modalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
	<div id="modalBox" style="background:#fff; padding:20px; max-width:420px; width:90%; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.25); text-align:center;">
		<h2 style="margin-top:0;">Cambios guardados</h2>
		<p>Los datos del alumno se han actualizado correctamente.</p>
		<button id="modalAceptar" style="margin-top:12px; padding:8px 16px; cursor:pointer;">Aceptar</button>
	</div>
</div>
<?php require("views/pieDePagina.php"); ?>
<script src="js/asignaturas.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
	var params = new URLSearchParams(window.location.search);
	if (params.get('mensaje') === 'modificado') {
		mostrarModal();
	}

	var btn = document.getElementById('modalAceptar');
	if (btn) {
		btn.addEventListener('click', function(){
			esconderModal();
			// Limpiamos el query string para que no reaparezca al recargar
			var p = new URLSearchParams(window.location.search);
			if (p.has('mensaje')) {
				p.delete('mensaje');
				var nuevo = window.location.pathname + (p.toString() ? ('?' + p.toString()) : '');
				window.history.replaceState({}, '', nuevo);
			}
		});
	}

	// Editor: activar/desactivar por fila usando delegación (robusto con tablas)
	var tabla = document.getElementById('tablaAlumnos');
	if (tabla) {
		tabla.addEventListener('click', function(e){
			var target = e.target;
			// Si clican el <i>, subimos al botón
			if (target.tagName === 'I') {
				target = target.closest('button');
			}
			if (!target) return;
			var fila = target.closest('tr');
			if (!fila) return;

			// Controles de la fila
			var btnEditar = fila.querySelector('.btn-editar');
			var btnGuardar = fila.querySelector('.btn-guardar');
			var btnCancelar = fila.querySelector('.btn-cancelar');
			var inputs = fila.querySelectorAll('input[type="text"], input[type="email"]');

			if (target.classList.contains('btn-editar')) {
				inputs.forEach(function(inp){
					inp.dataset.original = inp.value;
					inp.disabled = false;
				});
				if (btnEditar) btnEditar.style.display = 'none';
				if (btnGuardar) btnGuardar.style.display = '';
				if (btnCancelar) btnCancelar.style.display = '';
			}

			if (target.classList.contains('btn-cancelar')) {
				e.preventDefault();
				inputs.forEach(function(inp){
					if (inp.dataset.original !== undefined) inp.value = inp.dataset.original;
					inp.disabled = true;
				});
				if (btnGuardar) btnGuardar.style.display = 'none';
				if (btnCancelar) btnCancelar.style.display = 'none';
				if (btnEditar) btnEditar.style.display = '';
			}
		});
	}
});

function mostrarModal(){
	var overlay = document.getElementById('modalOverlay');
	if (overlay) {
		overlay.style.display = 'flex';
	}
}

function esconderModal(){
	var overlay = document.getElementById('modalOverlay');
	if (overlay) {
		overlay.style.display = 'none';
	}
}

// Toast notifications
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${tipo}`;
    toast.textContent = mensaje;
    document.body.appendChild(toast);
    
    // Usar requestAnimationFrame para mejor rendimiento
    requestAnimationFrame(() => toast.classList.add('show'));
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Mostrar toast si hay mensajes
if (window.location.search.includes('mensaje=modificado')) {
    mostrarToast('Alumno modificado correctamente', 'success');
}
</script>

</html>
