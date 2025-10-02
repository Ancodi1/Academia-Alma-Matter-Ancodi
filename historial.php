<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
authorizeRoles(['admin','profesor']);

require_once(__DIR__ . '/models/mysqlConnect.php');

class HistorialController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function agregarRegistro($id_alumno, $id_asignatura, $periodo, $tipo_evaluacion, $descripcion, $fecha, $nota, $peso = 1.00, $comentarios = '') {
        $stmt = $this->db->preparar("
            INSERT INTO HistorialAcademico (id_alumno, id_asignatura, periodo, tipo_evaluacion, descripcion, fecha, nota, peso, comentarios)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('iissssds', $id_alumno, $id_asignatura, $periodo, $tipo_evaluacion, $descripcion, $fecha, $nota, $peso, $comentarios);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getHistorialAlumno($id_alumno, $id_asignatura = null, $periodo = null) {
        $sql = "
            SELECT ha.*, a.nombre as asignatura_nombre, al.nombre as alumno_nombre, al.apellidos as alumno_apellidos
            FROM HistorialAcademico ha
            JOIN Asignatura a ON a.id = ha.id_asignatura
            JOIN Alumno al ON al.id = ha.id_alumno
            WHERE ha.id_alumno = ?
        ";
        
        $params = [$id_alumno];
        $types = 'i';
        
        if ($id_asignatura) {
            $sql .= " AND ha.id_asignatura = ?";
            $params[] = $id_asignatura;
            $types .= 'i';
        }
        
        if ($periodo) {
            $sql .= " AND ha.periodo = ?";
            $params[] = $periodo;
            $types .= 's';
        }
        
        $sql .= " ORDER BY ha.fecha DESC, ha.id_asignatura ASC";
        
        $stmt = $this->db->preparar($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $historial = [];
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
        $stmt->close();
        return $historial;
    }

    public function getPromedioPorAsignatura($id_alumno, $periodo = null) {
        $sql = "
            SELECT 
                ha.id_asignatura,
                a.nombre as asignatura_nombre,
                COUNT(*) as total_evaluaciones,
                AVG(ha.nota) as promedio,
                SUM(ha.nota * ha.peso) / SUM(ha.peso) as promedio_ponderado,
                MIN(ha.nota) as nota_minima,
                MAX(ha.nota) as nota_maxima
            FROM HistorialAcademico ha
            JOIN Asignatura a ON a.id = ha.id_asignatura
            WHERE ha.id_alumno = ?
        ";
        
        $params = [$id_alumno];
        $types = 'i';
        
        if ($periodo) {
            $sql .= " AND ha.periodo = ?";
            $params[] = $periodo;
            $types .= 's';
        }
        
        $sql .= " GROUP BY ha.id_asignatura, a.nombre ORDER BY a.nombre";
        
        $stmt = $this->db->preparar($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $promedios = [];
        while ($row = $result->fetch_assoc()) {
            $promedios[] = $row;
        }
        $stmt->close();
        return $promedios;
    }

    public function getEstadisticasGenerales($id_alumno) {
        $sql = "
            SELECT 
                COUNT(*) as total_evaluaciones,
                AVG(nota) as promedio_general,
                MIN(nota) as nota_minima,
                MAX(nota) as nota_maxima,
                SUM(CASE WHEN nota >= 5 THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN nota < 5 THEN 1 ELSE 0 END) as suspendidas,
                COUNT(DISTINCT periodo) as periodos_evaluados,
                COUNT(DISTINCT id_asignatura) as asignaturas_evaluadas
            FROM HistorialAcademico 
            WHERE id_alumno = ?
        ";
        
        $stmt = $this->db->preparar($sql);
        $stmt->bind_param('i', $id_alumno);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $stmt->close();
        
        if ($stats['total_evaluaciones'] > 0) {
            $stats['porcentaje_aprobadas'] = round(($stats['aprobadas'] / $stats['total_evaluaciones']) * 100, 2);
        } else {
            $stats['porcentaje_aprobadas'] = 0;
        }
        
        return $stats;
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

    public function getPeriodos() {
        $result = $this->db->realizarConsultaSQL("SELECT DISTINCT periodo FROM HistorialAcademico ORDER BY periodo DESC");
        $periodos = [];
        while ($row = $result->fetch_assoc()) {
            $periodos[] = $row['periodo'];
        }
        return $periodos;
    }
}

$controller = new HistorialController();
$alumno_seleccionado = isset($_GET['alumno']) ? (int)$_GET['alumno'] : null;
$asignatura_filtro = isset($_GET['asignatura']) ? (int)$_GET['asignatura'] : null;
$periodo_filtro = isset($_GET['periodo']) ? $_GET['periodo'] : null;
$asignaturas = $controller->getAsignaturas();
$alumnos = $controller->getAlumnos();
$periodos = $controller->getPeriodos();

// Procesar formulario de nuevo registro ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_registro'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id_alumno = (int)$_POST['id_alumno'];
        $id_asignatura = (int)$_POST['id_asignatura'];
        $periodo = $_POST['periodo'];
        $tipo_evaluacion = $_POST['tipo_evaluacion'];
        $descripcion = $_POST['descripcion'];
        $fecha = $_POST['fecha'];
        $nota = (float)$_POST['nota'];
        $peso = (float)$_POST['peso'];
        $comentarios = $_POST['comentarios'];

        if ($controller->agregarRegistro($id_alumno, $id_asignatura, $periodo, $tipo_evaluacion, $descripcion, $fecha, $nota, $peso, $comentarios)) {
            header("Location: historial.php?mensaje=registro_agregado&alumno=" . $id_alumno);
            exit;
        }
    }
}

$historial = [];
$promedios = [];
$estadisticas = [];

if ($alumno_seleccionado) {
    $historial = $controller->getHistorialAlumno($alumno_seleccionado, $asignatura_filtro, $periodo_filtro);
    $promedios = $controller->getPromedioPorAsignatura($alumno_seleccionado, $periodo_filtro);
    $estadisticas = $controller->getEstadisticasGenerales($alumno_seleccionado);
}

require("views/cabecera.php");
?>

<div id="contenido">
    <h1>Historial Académico Completo</h1>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'registro_agregado'): ?>
        <div class="aviso exito">Registro académico agregado correctamente</div>
    <?php endif; ?>

    <!-- Formulario para nuevo registro -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Agregar Registro Académico</h3>
        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
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
                <label>Período *</label>
                <input type="text" name="periodo" placeholder="Ej: 2024-1, Trimestre 1" required>
            </div>
            
            <div>
                <label>Tipo Evaluación *</label>
                <select name="tipo_evaluacion" required>
                    <option value="examen">📝 Examen</option>
                    <option value="trabajo">📋 Trabajo</option>
                    <option value="participacion">💬 Participación</option>
                    <option value="proyecto">🎯 Proyecto</option>
                    <option value="practica">🔬 Práctica</option>
                </select>
            </div>
            
            <div>
                <label>Fecha *</label>
                <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div>
                <label>Nota *</label>
                <input type="number" name="nota" min="0" max="10" step="0.1" required>
            </div>
            
            <div>
                <label>Peso</label>
                <input type="number" name="peso" min="0.1" max="5" step="0.1" value="1.0">
            </div>
            
            <div>
                <label>Descripción</label>
                <input type="text" name="descripcion" placeholder="Ej: Examen parcial, Trabajo final">
            </div>
            
            <div style="grid-column: 1 / -1;">
                <label>Comentarios</label>
                <textarea name="comentarios" rows="2" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: var(--card); color: var(--text);"></textarea>
            </div>
            
            <div>
                <button type="submit" name="agregar_registro">Agregar Registro</button>
            </div>
        </form>
    </div>

    <!-- Filtros -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Consultar Historial</h3>
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label>Alumno *</label>
                <select name="alumno" required>
                    <option value="">Seleccionar alumno</option>
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="<?php echo $alumno['id']; ?>" <?php echo $alumno_seleccionado == $alumno['id'] ? 'selected' : ''; ?>>
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
                        <option value="<?php echo $asignatura['id']; ?>" <?php echo $asignatura_filtro == $asignatura['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Período</label>
                <select name="periodo">
                    <option value="">Todos</option>
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?php echo htmlspecialchars($periodo); ?>" <?php echo $periodo_filtro === $periodo ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($periodo); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <button type="submit">Consultar</button>
                <a href="historial.php" style="margin-left: 10px;">Limpiar</a>
            </div>
        </form>
    </div>

    <?php if ($alumno_seleccionado && !empty($historial)): ?>
        <!-- Estadísticas del alumno -->
        <div class="dashboard-grid" style="margin-bottom: 20px;">
            <div class="card-kpi">
                <h3>Total Evaluaciones</h3>
                <p class="kpi-value"><?php echo $estadisticas['total_evaluaciones']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>Promedio General</h3>
                <p class="kpi-value"><?php echo round($estadisticas['promedio_general'], 2); ?></p>
            </div>
            <div class="card-kpi">
                <h3>Aprobadas</h3>
                <p class="kpi-value"><?php echo $estadisticas['aprobadas']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>Suspendidas</h3>
                <p class="kpi-value"><?php echo $estadisticas['suspendidas']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>% Aprobadas</h3>
                <p class="kpi-value"><?php echo $estadisticas['porcentaje_aprobadas']; ?>%</p>
            </div>
            <div class="card-kpi">
                <h3>Asignaturas</h3>
                <p class="kpi-value"><?php echo $estadisticas['asignaturas_evaluadas']; ?></p>
            </div>
        </div>

        <!-- Promedios por asignatura -->
        <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
            <h3>Promedios por Asignatura</h3>
            <?php if (empty($promedios)): ?>
                <p style="text-align: center; color: var(--muted); padding: 20px;">No hay datos suficientes para calcular promedios.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; margin-top: 16px;">
                        <thead>
                            <tr style="background: var(--thead);">
                                <th style="padding: 12px; text-align: left;">Asignatura</th>
                                <th style="padding: 12px; text-align: center;">Evaluaciones</th>
                                <th style="padding: 12px; text-align: center;">Promedio</th>
                                <th style="padding: 12px; text-align: center;">Promedio Ponderado</th>
                                <th style="padding: 12px; text-align: center;">Nota Mínima</th>
                                <th style="padding: 12px; text-align: center;">Nota Máxima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($promedios as $promedio): ?>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                        <?php echo htmlspecialchars($promedio['asignatura_nombre']); ?>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <?php echo $promedio['total_evaluaciones']; ?>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <?php echo round($promedio['promedio'], 2); ?>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <?php echo round($promedio['promedio_ponderado'], 2); ?>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <?php echo $promedio['nota_minima']; ?>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <?php echo $promedio['nota_maxima']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Historial detallado -->
        <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
            <h3>Historial Detallado de <?php echo htmlspecialchars($historial[0]['alumno_nombre'] . ' ' . $historial[0]['alumno_apellidos']); ?></h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; margin-top: 16px;">
                    <thead>
                        <tr style="background: var(--thead);">
                            <th style="padding: 12px; text-align: left;">Fecha</th>
                            <th style="padding: 12px; text-align: left;">Asignatura</th>
                            <th style="padding: 12px; text-align: left;">Período</th>
                            <th style="padding: 12px; text-align: left;">Tipo</th>
                            <th style="padding: 12px; text-align: left;">Descripción</th>
                            <th style="padding: 12px; text-align: center;">Nota</th>
                            <th style="padding: 12px; text-align: center;">Peso</th>
                            <th style="padding: 12px; text-align: left;">Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $registro): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo date('d/m/Y', strtotime($registro['fecha'])); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($registro['asignatura_nombre']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($registro['periodo']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php
                                    $tipos = [
                                        'examen' => '📝 Examen',
                                        'trabajo' => '📋 Trabajo',
                                        'participacion' => '💬 Participación',
                                        'proyecto' => '🎯 Proyecto',
                                        'practica' => '🔬 Práctica'
                                    ];
                                    echo $tipos[$registro['tipo_evaluacion']] ?? $registro['tipo_evaluacion'];
                                    ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($registro['descripcion']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <strong style="color: <?php echo $registro['nota'] >= 5 ? '#10b981' : '#ef4444'; ?>;">
                                        <?php echo $registro['nota']; ?>
                                    </strong>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                    <?php echo $registro['peso']; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                    <?php echo htmlspecialchars($registro['comentarios']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($alumno_seleccionado): ?>
        <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
            <p style="text-align: center; color: var(--muted); padding: 20px;">No hay registros académicos para este alumno con los filtros seleccionados.</p>
        </div>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</html>
