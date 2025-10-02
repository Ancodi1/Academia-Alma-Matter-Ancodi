<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
authorizeRoles(['admin','profesor']);

require_once(__DIR__ . '/models/mysqlConnect.php');

class CalendarioController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function getEventos($mes = null, $año = null) {
        if (!$mes) $mes = date('m');
        if (!$año) $año = date('Y');
        
        $stmt = $this->db->preparar("
            SELECT ca.*, a.nombre as asignatura_nombre, al.nombre as alumno_nombre, al.apellidos as alumno_apellidos
            FROM CalendarioAcademico ca
            LEFT JOIN Asignatura a ON a.id = ca.id_asignatura
            LEFT JOIN Alumno al ON al.id = ca.id_alumno
            WHERE MONTH(ca.fecha_inicio) = ? AND YEAR(ca.fecha_inicio) = ?
            ORDER BY ca.fecha_inicio ASC
        ");
        $stmt->bind_param('ii', $mes, $año);
        $stmt->execute();
        $result = $stmt->get_result();
        $eventos = [];
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }
        $stmt->close();
        return $eventos;
    }

    public function crearEvento($titulo, $descripcion, $fecha_inicio, $fecha_fin, $tipo, $id_asignatura, $id_alumno, $color) {
        $stmt = $this->db->preparar("
            INSERT INTO CalendarioAcademico (titulo, descripcion, fecha_inicio, fecha_fin, tipo, id_asignatura, id_alumno, color)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('sssssiis', $titulo, $descripcion, $fecha_inicio, $fecha_fin, $tipo, $id_asignatura, $id_alumno, $color);
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
}

$controller = new CalendarioController();
$mes_actual = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$año_actual = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');

// Procesar formulario de nuevo evento ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_evento'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        
        // Combinar fecha y hora de inicio
        $fecha_inicio = $_POST['fecha_inicio'] . ' ' . $_POST['hora_inicio'] . ':00';
        
        // Combinar fecha y hora de fin (si existen)
        $fecha_fin = null;
        if (!empty($_POST['fecha_fin']) && !empty($_POST['hora_fin'])) {
            $fecha_fin = $_POST['fecha_fin'] . ' ' . $_POST['hora_fin'] . ':00';
        }
        
        $tipo = $_POST['tipo'];
        $id_asignatura = $_POST['id_asignatura'] ?: null;
        $id_alumno = $_POST['id_alumno'] ?: null;
        $color = $_POST['color'];

        if ($controller->crearEvento($titulo, $descripcion, $fecha_inicio, $fecha_fin, $tipo, $id_asignatura, $id_alumno, $color)) {
            header("Location: calendario.php?mensaje=evento_creado");
            exit;
        }
    }
}

$eventos = $controller->getEventos($mes_actual, $año_actual);
$asignaturas = $controller->getAsignaturas();
$alumnos = $controller->getAlumnos();

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
    <h1>Calendario Académico</h1>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'evento_creado'): ?>
        <div class="aviso exito">Evento creado correctamente</div>
    <?php endif; ?>

    <!-- Navegación de meses -->
    <div style="margin: 20px 0; text-align: center;">
        <a href="?mes=<?php echo $mes_actual == 1 ? 12 : $mes_actual - 1; ?>&año=<?php echo $mes_actual == 1 ? $año_actual - 1 : $año_actual; ?>">« Anterior</a>
        <h2 style="display: inline-block; margin: 0 20px;">
            <?php echo date('F Y', mktime(0, 0, 0, $mes_actual, 1, $año_actual)); ?>
        </h2>
        <a href="?mes=<?php echo $mes_actual == 12 ? 1 : $mes_actual + 1; ?>&año=<?php echo $mes_actual == 12 ? $año_actual + 1 : $año_actual; ?>">Siguiente »</a>
    </div>

    <!-- Formulario para nuevo evento -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Nuevo Evento</h3>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
            <div>
                <label>Título *</label>
                <input type="text" name="titulo" required>
            </div>
            
            <div>
                <label>Tipo</label>
                <select name="tipo">
                    <option value="examen">Examen</option>
                    <option value="entrega">Entrega</option>
                    <option value="evento">Evento</option>
                    <option value="vacaciones">Vacaciones</option>
                    <option value="reunion">Reunión</option>
                </select>
            </div>
            
            <div>
                <label>Fecha inicio *</label>
                <div class="date-time-container">
                    <input type="date" name="fecha_inicio" required>
                    <input type="time" name="hora_inicio" value="09:00" required>
                </div>
            </div>
            
            <div>
                <label>Fecha fin</label>
                <div class="date-time-container">
                    <input type="date" name="fecha_fin">
                    <input type="time" name="hora_fin">
                </div>
            </div>
            
            <div>
                <label>Asignatura</label>
                <select name="id_asignatura">
                    <option value="">Todas</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?php echo $asignatura['id']; ?>"><?php echo htmlspecialchars($asignatura['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Alumno</label>
                <select name="id_alumno">
                    <option value="">Todos</option>
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="<?php echo $alumno['id']; ?>"><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Color</label>
                <select name="color" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);">
                    <?php
                    $colores = [
                        '#3b82f6' => '🔵 Azul',
                        '#ef4444' => '🔴 Rojo',
                        '#10b981' => '🟢 Verde',
                        '#f59e0b' => '🟡 Amarillo',
                        '#8b5cf6' => '🟣 Púrpura',
                        '#06b6d4' => '🔵 Cian',
                        '#f97316' => '🟠 Naranja',
                        '#84cc16' => '🟢 Lima',
                        '#e91e63' => '🩷 Rosa',
                        '#6b7280' => '⚫ Gris',
                        '#14b8a6' => '🔵 Turquesa',
                        '#dc2626' => '🔴 Rojo Oscuro'
                    ];
                    foreach ($colores as $color => $nombre): ?>
                        <option value="<?php echo $color; ?>" <?php echo $color === '#3b82f6' ? 'selected' : ''; ?>><?php echo $nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="grid-column: 1 / -1;">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);"></textarea>
            </div>
            
            <div style="grid-column: 1 / -1;">
                <button type="submit" name="crear_evento">Crear Evento</button>
            </div>
        </form>
    </div>

    <!-- Lista de eventos del mes -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
        <h3>Eventos de <?php echo date('F Y', mktime(0, 0, 0, $mes_actual, 1, $año_actual)); ?></h3>
        
        <?php if (empty($eventos)): ?>
            <p style="text-align: center; color: var(--muted); padding: 20px;">No hay eventos programados para este mes.</p>
        <?php else: ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($eventos as $evento): ?>
                    <div style="border-left: 4px solid <?php echo htmlspecialchars($evento['color']); ?>; padding: 15px; background: var(--card-alt); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: var(--text);"><?php echo htmlspecialchars($evento['titulo']); ?></h4>
                                <p style="margin: 0; color: var(--muted); font-size: 14px;">
                                    📅 <strong><?php echo date('d/m/Y', strtotime($evento['fecha_inicio'])); ?></strong> a las <strong><?php echo date('H:i', strtotime($evento['fecha_inicio'])); ?></strong>
                                    <?php if ($evento['fecha_fin']): ?>
                                        <br>⏰ Hasta: <strong><?php echo date('d/m/Y', strtotime($evento['fecha_fin'])); ?></strong> a las <strong><?php echo date('H:i', strtotime($evento['fecha_fin'])); ?></strong>
                                    <?php endif; ?>
                                </p>
                                <?php if ($evento['asignatura_nombre']): ?>
                                    <p style="margin: 5px 0 0 0; color: var(--muted); font-size: 14px;">
                                        📚 <?php echo htmlspecialchars($evento['asignatura_nombre']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($evento['alumno_nombre']): ?>
                                    <p style="margin: 5px 0 0 0; color: var(--muted); font-size: 14px;">
                                        👤 <?php echo htmlspecialchars($evento['alumno_nombre'] . ' ' . $evento['alumno_apellidos']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($evento['descripcion']): ?>
                                    <p style="margin: 10px 0 0 0; color: var(--text);"><?php echo nl2br(htmlspecialchars($evento['descripcion'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <span style="background: <?php echo htmlspecialchars($evento['color']); ?>; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; text-transform: uppercase;">
                                <?php echo htmlspecialchars($evento['tipo']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require("views/pieDePagina.php"); ?>
</html>
