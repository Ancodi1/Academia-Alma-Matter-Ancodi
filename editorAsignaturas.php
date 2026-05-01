<?php
require("views/cabecera.php");
require_once("controllers/AsignaturaController.php");
require_once("models/csrf.php");

$asignaturaController = new AsignaturaController();

// Búsqueda y paginación
$termino = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 5;

$totalAsignaturas = $asignaturaController->contarAsignaturas($termino);
$totalPaginas = ceil($totalAsignaturas / $porPagina);
$asignaturas = $asignaturaController->buscarAsignaturas($termino, $pagina, $porPagina);

function mostrarBotonModificarAsignatura($id)
{
    echo '<button type="button" class="btn-editar" title="Editar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
    echo '<button type="submit" name="modificarAsignatura" class="btn-guardar" style="display:none;" title="Guardar"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>';
    echo '<button type="button" class="btn-cancelar" style="display:none;" title="Cancelar"><i class="fa fa-times" aria-hidden="true"></i></button>';
}

function mostrarBotonEliminarAsignatura($id)
{
    echo '<button type="submit" name="eliminarAsignatura"><i class="fa fa-trash" aria-hidden="true"></i></button>';
}

?>
<!-- Divisor del Contenido -->
<div id="contenido">
    <h1>Gestor de Asignaturas</h1>
    <h3>Listado de Asignaturas</h3>
    
    <!-- Búsqueda -->
    <form method="GET" style="margin: 20px 0;">
        <input type="text" name="buscar" placeholder="Buscar por nombre o curso..." value="<?php echo htmlspecialchars($termino); ?>" style="width: 300px; padding: 8px;">
        <button type="submit">Buscar</button>
        <?php if ($termino): ?>
            <a href="editorAsignaturas.php" style="margin-left: 10px;">Limpiar</a>
        <?php endif; ?>
    </form>
    
    <?php
	if (isset($_GET["mensaje"])) {
		if ($_GET["mensaje"] === "eliminado") echo '<div class="aviso exito">Asignatura eliminada correctamente</div>';
	}
    if (isset($_GET["error"])) {
        if ($_GET["error"] === "modificar") echo '<div class="aviso error">Error al modificar la asignatura</div>';
        if ($_GET["error"] === "eliminar") echo '<div class="aviso error">Error al eliminar la asignatura</div>';
        if ($_GET["error"] === "validacion_campos") echo '<div class="aviso error">Rellena nombre y curso.</div>';
        if ($_GET["error"] === "csrf") echo '<div class="aviso error">Token de seguridad inválido. Inténtalo de nuevo.</div>';
    }
    ?>
    <table id="tablaAsignaturas">
        <tr>
            <td id="filaAsignaturas">Nombre</td>
            <td id="filaAsignaturas">Curso</td>
            <td id="filaAsignaturas">Acciones</td>
        </tr>
            <!-- Listamos todas las Asignaturas -->
            <?php
            while ($asignatura = $asignaturas->fetch_assoc()) {
                $idAsignatura = $asignatura["id"];
                echo '<form action="controllers/accionesAsignatura.php" method="post">';
                echo '<tr>';
                echo '<input type="hidden" name="id" value="' . $idAsignatura . '">';
                echo '<input type="hidden" name="csrf_token" value="' . generarTokenCSRF() . '">';
                echo '<td id="filaAsignaturas"><input type="text" name="nombre" value="' . htmlspecialchars($asignatura["nombre"]) . '" disabled></td>';
                echo '<td id="filaAsignaturas"><input type="text" name="curso" value="' . htmlspecialchars($asignatura["curso"]) . '" disabled></td>';
                echo '<td id="filaAsignaturas">';
                mostrarBotonModificarAsignatura($idAsignatura);
                mostrarBotonEliminarAsignatura($idAsignatura);
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
		<p>Los datos de la asignatura se han actualizado correctamente.</p>
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
			var p = new URLSearchParams(window.location.search);
			if (p.has('mensaje')) {
				p.delete('mensaje');
				var nuevo = window.location.pathname + (p.toString() ? ('?' + p.toString()) : '');
				window.history.replaceState({}, '', nuevo);
			}
		});
	}

	var tabla = document.getElementById('tablaAsignaturas');
	if (tabla) {
		tabla.addEventListener('click', function(e){
			var target = e.target;
			if (target.tagName === 'I') {
				target = target.closest('button');
			}
			if (!target) return;
			var fila = target.closest('tr');
			if (!fila) return;

			var btnEditar = fila.querySelector('.btn-editar');
			var btnGuardar = fila.querySelector('.btn-guardar');
			var btnCancelar = fila.querySelector('.btn-cancelar');
			var inputs = fila.querySelectorAll('input[type="text"]');

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
</script>

</html>