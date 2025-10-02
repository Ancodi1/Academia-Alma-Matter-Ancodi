<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
authorizeRoles(['admin','profesor']);

require_once(__DIR__ . '/models/mysqlConnect.php');

class TrabajoController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function crearTrabajo($titulo, $descripcion, $id_asignatura, $id_alumno, $fecha_asignacion, $fecha_entrega) {
        $stmt = $this->db->preparar("
            INSERT INTO Trabajo (titulo, descripcion, id_asignatura, id_alumno, fecha_asignacion, fecha_entrega)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiiss', $titulo, $descripcion, $id_asignatura, $id_alumno, $fecha_asignacion, $fecha_entrega);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getTrabajos($filtro_alumno = null, $filtro_asignatura = null, $filtro_estado = null) {
        $sql = "
            SELECT t.*, a.nombre as asignatura_nombre, al.nombre as alumno_nombre, al.apellidos as alumno_apellidos
            FROM Trabajo t
            JOIN Asignatura a ON a.id = t.id_asignatura
            JOIN Alumno al ON al.id = t.id_alumno
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        if ($filtro_alumno) {
            $sql .= " AND t.id_alumno = ?";
            $params[] = $filtro_alumno;
            $types .= 'i';
        }
        
        if ($filtro_asignatura) {
            $sql .= " AND t.id_asignatura = ?";
            $params[] = $filtro_asignatura;
            $types .= 'i';
        }
        
        if ($filtro_estado) {
            $sql .= " AND t.estado = ?";
            $params[] = $filtro_estado;
            $types .= 's';
        }
        
        $sql .= " ORDER BY t.fecha_entrega ASC";
        
        $stmt = $this->db->preparar($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $trabajos = [];
        while ($row = $result->fetch_assoc()) {
            $trabajos[] = $row;
        }
        $stmt->close();
        return $trabajos;
    }

    public function actualizarTrabajo($id, $nota = null, $comentarios = null, $estado = null) {
        $sql = "UPDATE Trabajo SET ";
        $params = [];
        $types = '';
        
        if ($nota !== null) {
            $sql .= "nota = ?, ";
            $params[] = $nota;
            $types .= 'd';
        }
        
        if ($comentarios !== null) {
            $sql .= "comentarios = ?, ";
            $params[] = $comentarios;
            $types .= 's';
        }
        
        if ($estado !== null) {
            $sql .= "estado = ?, ";
            $params[] = $estado;
            $types .= 's';
        }
        
        $sql = rtrim($sql, ', ') . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';
        
        $stmt = $this->db->preparar($sql);
        $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAsignaturas() {
        $result = $this->db->realizarConsultaSQL("SELECT id, nombre FROM Asignatura ORDER BY nombre ASC");
        $asignaturas = [];
        while ($row = $result->fetch_assoc()) {
            $asignaturas[] = $row;
        }
        return $asignaturas;
    }

    public function getAlumnos() {
        $result = $this->db->realizarConsultaSQL("SELECT id, nombre, apellidos FROM Alumno ORDER BY nombre ASC");
        $alumnos = [];
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
        return $alumnos;
    }

    public function getEstadisticasTrabajos() {
        $sql = "
            SELECT 
                COUNT(*) as total_trabajos,
                SUM(CASE WHEN estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN estado = 'calificado' THEN 1 ELSE 0 END) as calificados,
                SUM(CASE WHEN estado = 'atrasado' THEN 1 ELSE 0 END) as atrasados,
                AVG(nota) as promedio_notas
            FROM Trabajo
        ";
        
        $result = $this->db->realizarConsultaSQL($sql);
        $stats = $result->fetch_assoc();
        return $stats;
    }
}

$controller = new TrabajoController();
$filtro_alumno = isset($_GET['alumno']) ? (int)$_GET['alumno'] : null;
$filtro_asignatura = isset($_GET['asignatura']) ? (int)$_GET['asignatura'] : null;
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$asignaturas = $controller->getAsignaturas();
$alumnos = $controller->getAlumnos();

// Procesar formulario de nuevo trabajo ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_trabajo'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $id_asignatura = (int)$_POST['id_asignatura'];
        $id_alumno = (int)$_POST['id_alumno'];
        $fecha_asignacion = $_POST['fecha_asignacion'];
        $fecha_entrega = $_POST['fecha_entrega'];

        if ($controller->crearTrabajo($titulo, $descripcion, $id_asignatura, $id_alumno, $fecha_asignacion, $fecha_entrega)) {
            header("Location: trabajos.php?mensaje=trabajo_creado");
            exit;
        }
    }
}

// Procesar actualización de trabajo ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_trabajo'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id = (int)$_POST['id'];
        $nota = $_POST['nota'] ? (float)$_POST['nota'] : null;
        $comentarios = $_POST['comentarios'] ?: null;
        $estado = $_POST['estado'] ?: null;

        if ($controller->actualizarTrabajo($id, $nota, $comentarios, $estado)) {
            header("Location: trabajos.php?mensaje=trabajo_actualizado");
            exit;
        }
    }
}

$trabajos = $controller->getTrabajos($filtro_alumno, $filtro_asignatura, $filtro_estado);
$estadisticas = $controller->getEstadisticasTrabajos();

require("views/cabecera.php");
?>

<div id="contenido">
    <h1>Gestión de Trabajos y Proyectos</h1>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'trabajo_creado'): ?>
        <div class="aviso exito">Trabajo creado correctamente</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'trabajo_actualizado'): ?>
        <div class="aviso exito">Trabajo actualizado correctamente</div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="dashboard-grid" style="margin-bottom: 20px;">
        <div class="card-kpi">
            <h3>Total Trabajos</h3>
            <p class="kpi-value"><?php echo $estadisticas['total_trabajos']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Asignados</h3>
            <p class="kpi-value"><?php echo $estadisticas['asignados']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Entregados</h3>
            <p class="kpi-value"><?php echo $estadisticas['entregados']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Calificados</h3>
            <p class="kpi-value"><?php echo $estadisticas['calificados']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Atrasados</h3>
            <p class="kpi-value"><?php echo $estadisticas['atrasados']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Promedio</h3>
            <p class="kpi-value"><?php echo $estadisticas['promedio_notas'] ? round($estadisticas['promedio_notas'], 2) : 'N/A'; ?></p>
        </div>
    </div>

    <!-- Formulario para nuevo trabajo -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Asignar Nuevo Trabajo</h3>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
            <div>
                <label>Título *</label>
                <input type="text" name="titulo" required>
            </div>
            
            <div>
                <label>Alumno *</label>
                <select name="id_alumno" required>
                    <option value="">Seleccionar alumno</option>
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="<?php echo $alumno['id']; ?>">
                            <?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Asignatura *</label>
                <select name="id_asignatura" required>
                    <option value="">Seleccionar asignatura</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?php echo $asignatura['id']; ?>">
                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Fecha Asignación *</label>
                <input type="date" name="fecha_asignacion" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div>
                <label>Fecha Entrega *</label>
                <input type="date" name="fecha_entrega" required>
            </div>
            
            <div style="grid-column: 1 / -1;">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);"></textarea>
            </div>
            
            <div>
                <button type="submit" name="crear_trabajo">Asignar Trabajo</button>
            </div>
        </form>
    </div>

    <!-- Filtros -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Filtros</h3>
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label>Alumno</label>
                <select name="alumno">
                    <option value="">Todos</option>
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="<?php echo $alumno['id']; ?>" <?php echo $filtro_alumno == $alumno['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Asignatura</label>
                <select name="asignatura">
                    <option value="">Todas</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?php echo $asignatura['id']; ?>" <?php echo $filtro_asignatura == $asignatura['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="asignado" <?php echo $filtro_estado === 'asignado' ? 'selected' : ''; ?>>Asignado</option>
                    <option value="entregado" <?php echo $filtro_estado === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                    <option value="calificado" <?php echo $filtro_estado === 'calificado' ? 'selected' : ''; ?>>Calificado</option>
                    <option value="atrasado" <?php echo $filtro_estado === 'atrasado' ? 'selected' : ''; ?>>Atrasado</option>
                </select>
            </div>
            
            <div>
                <button type="submit">Filtrar</button>
                <a href="trabajos.php" style="margin-left: 10px;">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Lista de trabajos -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
        <h3>Lista de Trabajos</h3>
        
        <?php if (empty($trabajos)): ?>
            <p style="text-align: center; color: var(--muted); padding: 20px;">No hay trabajos que coincidan con los filtros.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; margin-top: 16px;">
                    <thead>
                        <tr style="background: var(--thead);">
                            <th style="padding: 12px; text-align: left;">Título</th>
                            <th style="padding: 12px; text-align: left;">Alumno</th>
                            <th style="padding: 12px; text-align: left;">Asignatura</th>
                            <th style="padding: 12px; text-align: center;">Fecha Entrega</th>
                            <th style="padding: 12px; text-align: center;">Estado</th>
                            <th style="padding: 12px; text-align: center;">Nota</th>
                            <th style="padding: 12px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajos as $trabajo): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <strong><?php echo htmlspecialchars($trabajo['titulo']); ?></strong>
                                    <?php if ($trabajo['descripcion']): ?>
                                        <br><small style="color: var(--muted);"><?php echo htmlspecialchars(substr($trabajo['descripcion'], 0, 50)) . (strlen($trabajo['descripcion']) > 50 ? '...' : ''); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($trabajo['alumno_nombre'] . ' ' . $trabajo['alumno_apellidos']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($trabajo['asignatura_nombre']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <?php echo date('d/m/Y', strtotime($trabajo['fecha_entrega'])); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <?php
                                    $estados = [
                                        'asignado' => '📋 Asignado',
                                        'entregado' => '📤 Entregado',
                                        'calificado' => '✅ Calificado',
                                        'atrasado' => '⚠️ Atrasado'
                                    ];
                                    echo $estados[$trabajo['estado']] ?? $trabajo['estado'];
                                    ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <?php echo $trabajo['nota'] ? $trabajo['nota'] : '-'; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <button onclick="editarTrabajo(<?php echo $trabajo['id']; ?>, '<?php echo htmlspecialchars($trabajo['titulo']); ?>', <?php echo $trabajo['nota'] ?: 'null'; ?>, '<?php echo htmlspecialchars($trabajo['comentarios']); ?>', '<?php echo $trabajo['estado']; ?>')" style="font-size: 12px; padding: 4px 8px;">Editar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para editar trabajo -->
<div id="modalEditar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--card); padding:20px; max-width:500px; width:90%; border-radius:8px; box-shadow:var(--shadow);">
        <h3>Editar Trabajo</h3>
        <form method="POST" id="formEditar">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <input type="hidden" name="id" id="edit_id">
            
            <div style="margin-bottom: 15px;">
                <label>Título</label>
                <input type="text" id="edit_titulo" readonly style="background: var(--card-alt);">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Nota</label>
                <input type="number" name="nota" id="edit_nota" min="0" max="10" step="0.1">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Estado</label>
                <select name="estado" id="edit_estado">
                    <option value="asignado">📋 Asignado</option>
                    <option value="entregado">📤 Entregado</option>
                    <option value="calificado">✅ Calificado</option>
                    <option value="atrasado">⚠️ Atrasado</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label>Comentarios</label>
                <textarea name="comentarios" id="edit_comentarios" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);"></textarea>
            </div>
            
            <div style="text-align: right;">
                <button type="button" onclick="cerrarModal()" style="margin-right: 10px;">Cancelar</button>
                <button type="submit" name="actualizar_trabajo">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarTrabajo(id, titulo, nota, comentarios, estado) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_titulo').value = titulo;
    document.getElementById('edit_nota').value = nota || '';
    document.getElementById('edit_comentarios').value = comentarios || '';
    document.getElementById('edit_estado').value = estado;
    document.getElementById('modalEditar').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalEditar').style.display = 'none';
}
</script>

<?php require("views/pieDePagina.php"); ?>
</html>
