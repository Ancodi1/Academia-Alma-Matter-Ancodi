<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
authorizeRoles(['admin','profesor']);

require_once(__DIR__ . '/models/mysqlConnect.php');

class ComunicadoController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function crearComunicado($titulo, $contenido, $tipo, $destinatarios, $fecha_expiracion = null) {
        $stmt = $this->db->preparar("
            INSERT INTO Comunicado (titulo, contenido, tipo, destinatarios, fecha_expiracion, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $user_id = $_SESSION['user_id'] ?? null;
        $stmt->bind_param('sssssi', $titulo, $contenido, $tipo, $destinatarios, $fecha_expiracion, $user_id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getComunicados($filtro_tipo = null, $filtro_destinatarios = null, $solo_activos = true) {
        $sql = "
            SELECT c.*, u.nombre as creador_nombre
            FROM Comunicado c
            LEFT JOIN Usuario u ON u.id = c.created_by
            WHERE 1=1
        ";
        
        $params = [];
        $types = '';
        
        if ($solo_activos) {
            $sql .= " AND c.activo = 1";
        }
        
        if ($filtro_tipo) {
            $sql .= " AND c.tipo = ?";
            $params[] = $filtro_tipo;
            $types .= 's';
        }
        
        if ($filtro_destinatarios) {
            $sql .= " AND c.destinatarios = ?";
            $params[] = $filtro_destinatarios;
            $types .= 's';
        }
        
        $sql .= " ORDER BY c.fecha_publicacion DESC";
        
        $stmt = $this->db->preparar($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $comunicados = [];
        while ($row = $result->fetch_assoc()) {
            $comunicados[] = $row;
        }
        $stmt->close();
        return $comunicados;
    }

    public function marcarComoLeido($id_comunicado, $user_id) {
        // Obtener comunicado actual
        $stmt = $this->db->preparar("SELECT leido_por FROM Comunicado WHERE id = ?");
        $stmt->bind_param('i', $id_comunicado);
        $stmt->execute();
        $result = $stmt->get_result();
        $comunicado = $result->fetch_assoc();
        $stmt->close();
        
        $leido_por = json_decode($comunicado['leido_por'] ?? '[]', true);
        if (!in_array($user_id, $leido_por)) {
            $leido_por[] = $user_id;
        }
        
        $stmt = $this->db->preparar("UPDATE Comunicado SET leido_por = ? WHERE id = ?");
        $leido_por_json = json_encode($leido_por);
        $stmt->bind_param('si', $leido_por_json, $id_comunicado);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function desactivarComunicado($id) {
        $stmt = $this->db->preparar("UPDATE Comunicado SET activo = 0 WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getEstadisticasComunicados() {
        $sql = "
            SELECT 
                COUNT(*) as total_comunicados,
                SUM(CASE WHEN tipo = 'urgente' THEN 1 ELSE 0 END) as urgentes,
                SUM(CASE WHEN tipo = 'general' THEN 1 ELSE 0 END) as generales,
                SUM(CASE WHEN tipo = 'evento' THEN 1 ELSE 0 END) as eventos,
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos
            FROM Comunicado
        ";
        
        $result = $this->db->realizarConsultaSQL($sql);
        $stats = $result->fetch_assoc();
        return $stats;
    }
}

$controller = new ComunicadoController();
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
$filtro_destinatarios = isset($_GET['destinatarios']) ? $_GET['destinatarios'] : null;

// Procesar formulario de nuevo comunicado ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_comunicado'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        $tipo = $_POST['tipo'];
        $destinatarios = $_POST['destinatarios'];
        
        // Combinar fecha y hora de expiración (si existen)
        $fecha_expiracion = null;
        if (!empty($_POST['fecha_expiracion']) && !empty($_POST['hora_expiracion'])) {
            $fecha_expiracion = $_POST['fecha_expiracion'] . ' ' . $_POST['hora_expiracion'] . ':00';
        }

        if ($controller->crearComunicado($titulo, $contenido, $tipo, $destinatarios, $fecha_expiracion)) {
            header("Location: comunicados.php?mensaje=comunicado_creado");
            exit;
        }
    }
}

// Procesar marcar como leído ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_leido'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id_comunicado = (int)$_POST['id_comunicado'];
        $user_id = $_SESSION['user_id'];
        $controller->marcarComoLeido($id_comunicado, $user_id);
        header("Location: comunicados.php");
        exit;
    }
}

// Procesar desactivar comunicado ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desactivar'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id = (int)$_POST['id'];
        $controller->desactivarComunicado($id);
        header("Location: comunicados.php?mensaje=comunicado_desactivado");
        exit;
    }
}

$comunicados = $controller->getComunicados($filtro_tipo, $filtro_destinatarios);
$estadisticas = $controller->getEstadisticasComunicados();

require("views/cabecera.php");
?>

<style>
.date-time-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.date-time-container input[type="date"] {
    flex: 1;
    padding: 10px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--card);
    color: var(--text);
    font-size: 14px;
}

.date-time-container input[type="time"] {
    flex: 1;
    padding: 10px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--card);
    color: var(--text);
    font-size: 14px;
}

.date-time-container input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
</style>

<div id="contenido">
    <h1>Comunicados y Avisos</h1>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'comunicado_creado'): ?>
        <div class="aviso exito">Comunicado creado correctamente</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'comunicado_desactivado'): ?>
        <div class="aviso exito">Comunicado desactivado correctamente</div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="dashboard-grid" style="margin-bottom: 20px;">
        <div class="card-kpi">
            <h3>Total Comunicados</h3>
            <p class="kpi-value"><?php echo $estadisticas['total_comunicados']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Urgentes</h3>
            <p class="kpi-value"><?php echo $estadisticas['urgentes']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Generales</h3>
            <p class="kpi-value"><?php echo $estadisticas['generales']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Eventos</h3>
            <p class="kpi-value"><?php echo $estadisticas['eventos']; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Activos</h3>
            <p class="kpi-value"><?php echo $estadisticas['activos']; ?></p>
        </div>
    </div>

    <!-- Formulario para nuevo comunicado -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Crear Nuevo Comunicado</h3>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
            <div>
                <label>Título *</label>
                <input type="text" name="titulo" required>
            </div>
            
            <div>
                <label>Tipo *</label>
                <select name="tipo" required>
                    <option value="general">📢 General</option>
                    <option value="urgente">🚨 Urgente</option>
                    <option value="informacion">ℹ️ Información</option>
                    <option value="evento">🎉 Evento</option>
                </select>
            </div>
            
            <div>
                <label>Destinatarios *</label>
                <select name="destinatarios" required>
                    <option value="todos">👥 Todos</option>
                    <option value="alumnos">🎓 Alumnos</option>
                    <option value="profesores">👨‍🏫 Profesores</option>
                    <option value="padres">👨‍👩‍👧‍👦 Padres</option>
                </select>
            </div>
            
            <div>
                <label>Fecha Expiración</label>
                <div class="date-time-container">
                    <input type="date" name="fecha_expiracion">
                    <input type="time" name="hora_expiracion">
                </div>
            </div>
            
            <div style="grid-column: 1 / -1;">
                <label>Contenido *</label>
                <textarea name="contenido" rows="6" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);"></textarea>
            </div>
            
            <div>
                <button type="submit" name="crear_comunicado">Publicar Comunicado</button>
            </div>
        </form>
    </div>

    <!-- Filtros -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Filtros</h3>
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    <option value="general" <?php echo $filtro_tipo === 'general' ? 'selected' : ''; ?>>📢 General</option>
                    <option value="urgente" <?php echo $filtro_tipo === 'urgente' ? 'selected' : ''; ?>>🚨 Urgente</option>
                    <option value="informacion" <?php echo $filtro_tipo === 'informacion' ? 'selected' : ''; ?>>ℹ️ Información</option>
                    <option value="evento" <?php echo $filtro_tipo === 'evento' ? 'selected' : ''; ?>>🎉 Evento</option>
                </select>
            </div>
            
            <div>
                <label>Destinatarios</label>
                <select name="destinatarios">
                    <option value="">Todos</option>
                    <option value="todos" <?php echo $filtro_destinatarios === 'todos' ? 'selected' : ''; ?>>👥 Todos</option>
                    <option value="alumnos" <?php echo $filtro_destinatarios === 'alumnos' ? 'selected' : ''; ?>>🎓 Alumnos</option>
                    <option value="profesores" <?php echo $filtro_destinatarios === 'profesores' ? 'selected' : ''; ?>>👨‍🏫 Profesores</option>
                    <option value="padres" <?php echo $filtro_destinatarios === 'padres' ? 'selected' : ''; ?>>👨‍👩‍👧‍👦 Padres</option>
                </select>
            </div>
            
            <div>
                <button type="submit">Filtrar</button>
                <a href="comunicados.php" style="margin-left: 10px;">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Lista de comunicados -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
        <h3>Comunicados</h3>
        
        <?php if (empty($comunicados)): ?>
            <p style="text-align: center; color: var(--muted); padding: 20px;">No hay comunicados que coincidan con los filtros.</p>
        <?php else: ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($comunicados as $comunicado): ?>
                    <div style="border-left: 4px solid <?php 
                        $colores = [
                            'general' => '#3b82f6',
                            'urgente' => '#ef4444',
                            'informacion' => '#10b981',
                            'evento' => '#f59e0b'
                        ];
                        echo $colores[$comunicado['tipo']] ?? '#3b82f6';
                    ?>; padding: 20px; background: var(--card-alt); border-radius: 8px;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: var(--text);"><?php echo htmlspecialchars($comunicado['titulo']); ?></h4>
                                <p style="margin: 0; color: var(--muted); font-size: 14px;">
                                    📅 <?php echo date('d/m/Y H:i', strtotime($comunicado['fecha_publicacion'])); ?>
                                    <?php if ($comunicado['creador_nombre']): ?>
                                        • 👤 <?php echo htmlspecialchars($comunicado['creador_nombre']); ?>
                                    <?php endif; ?>
                                    • 🎯 <?php echo ucfirst($comunicado['destinatarios']); ?>
                                </p>
                                <?php if ($comunicado['fecha_expiracion']): ?>
                                    <p style="margin: 5px 0 0 0; color: var(--muted); font-size: 14px;">
                                        ⏰ Expira: <?php echo date('d/m/Y H:i', strtotime($comunicado['fecha_expiracion'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <span style="background: <?php 
                                    $colores = [
                                        'general' => '#3b82f6',
                                        'urgente' => '#ef4444',
                                        'informacion' => '#10b981',
                                        'evento' => '#f59e0b'
                                    ];
                                    echo $colores[$comunicado['tipo']] ?? '#3b82f6';
                                ?>; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($comunicado['tipo']); ?>
                                </span>
                                <?php if ($comunicado['activo']): ?>
                                    <span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span style="background: #6b7280; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <p style="margin: 0; color: var(--text); line-height: 1.6;"><?php echo nl2br(htmlspecialchars($comunicado['contenido'])); ?></p>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                                <input type="hidden" name="id_comunicado" value="<?php echo $comunicado['id']; ?>">
                                <button type="submit" name="marcar_leido" style="font-size: 12px; padding: 4px 8px;">✅ Marcar como leído</button>
                            </form>
                            
                            <?php if ($comunicado['activo']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $comunicado['id']; ?>">
                                    <button type="submit" name="desactivar" style="font-size: 12px; padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px;">❌ Desactivar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require("views/pieDePagina.php"); ?>
</html>
