<?php
require_once(__DIR__ . '/models/session.php');
require_once(__DIR__ . '/models/csrf.php');
authorizeRoles(['admin','profesor']);

require_once(__DIR__ . '/models/mysqlConnect.php');

class AsistenciaController {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function registrarAsistencia($id_alumno, $id_asignatura, $fecha, $estado, $observaciones = '') {
        $stmt = $this->db->preparar("
            INSERT INTO Asistencia (id_alumno, id_asignatura, fecha, estado, observaciones)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE estado = VALUES(estado), observaciones = VALUES(observaciones)
        ");
        $stmt->bind_param('iisss', $id_alumno, $id_asignatura, $fecha, $estado, $observaciones);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAsistenciaPorFecha($fecha, $id_asignatura = null) {
        $sql = "
            SELECT a.*, al.nombre as alumno_nombre, al.apellidos as alumno_apellidos, asig.nombre as asignatura_nombre
            FROM Asistencia a
            JOIN Alumno al ON al.id = a.id_alumno
            JOIN Asignatura asig ON asig.id = a.id_asignatura
            WHERE a.fecha = ?
        ";
        
        if ($id_asignatura) {
            $sql .= " AND a.id_asignatura = ?";
        }
        
        $sql .= " ORDER BY al.nombre ASC";
        
        $stmt = $this->db->preparar($sql);
        if ($id_asignatura) {
            $stmt->bind_param('si', $fecha, $id_asignatura);
        } else {
            $stmt->bind_param('s', $fecha);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $asistencias = [];
        while ($row = $result->fetch_assoc()) {
            $asistencias[] = $row;
        }
        $stmt->close();
        return $asistencias;
    }

    public function getEstadisticasAsistencia($id_alumno, $id_asignatura = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_clases,
                SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                SUM(CASE WHEN estado = 'falta' THEN 1 ELSE 0 END) as faltas,
                SUM(CASE WHEN estado = 'justificada' THEN 1 ELSE 0 END) as justificadas,
                SUM(CASE WHEN estado = 'tardanza' THEN 1 ELSE 0 END) as tardanzas
            FROM Asistencia 
            WHERE id_alumno = ?
        ";
        
        if ($id_asignatura) {
            $sql .= " AND id_asignatura = ?";
        }
        
        $stmt = $this->db->preparar($sql);
        if ($id_asignatura) {
            $stmt->bind_param('ii', $id_alumno, $id_asignatura);
        } else {
            $stmt->bind_param('i', $id_alumno);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $stmt->close();
        
        if ($stats['total_clases'] > 0) {
            $stats['porcentaje_asistencia'] = round(($stats['presentes'] / $stats['total_clases']) * 100, 2);
        } else {
            $stats['porcentaje_asistencia'] = 0;
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

    public function getAlumnosPorAsignatura($id_asignatura) {
        $stmt = $this->db->preparar("
            SELECT DISTINCT a.id, a.nombre, a.apellidos
            FROM Alumno a
            JOIN Examen e ON e.idAlumno = a.id
            WHERE e.idAsignatura = ?
            ORDER BY a.nombre ASC
        ");
        $stmt->bind_param('i', $id_asignatura);
        $stmt->execute();
        $result = $stmt->get_result();
        $alumnos = [];
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
        $stmt->close();
        return $alumnos;
    }
}

$controller = new AsistenciaController();
$fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$asignatura_filtro = isset($_GET['asignatura']) ? (int)$_GET['asignatura'] : null;
$asignaturas = $controller->getAsignaturas();
$alumnos = $controller->getAlumnos();

// Procesar registro de asistencia ANTES de incluir la cabecera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_asistencia'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id_alumno = (int)$_POST['id_alumno'];
        $id_asignatura = (int)$_POST['id_asignatura'];
        $fecha = $_POST['fecha'];
        $estado = $_POST['estado'];
        $observaciones = $_POST['observaciones'];

        if ($controller->registrarAsistencia($id_alumno, $id_asignatura, $fecha, $estado, $observaciones)) {
            header("Location: asistencia.php?mensaje=asistencia_registrada&fecha=" . urlencode($fecha));
            exit;
        }
    }
}

// Procesar registro masivo de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_asistencia_masiva'])) {
    if (validarTokenCSRF($_POST['csrf_token'])) {
        $id_asignatura = (int)$_POST['id_asignatura'];
        $fecha = $_POST['fecha'];
        
        $registros_exitosos = 0;
        $total_registros = 0;
        
        foreach ($_POST['asistencia'] as $id_alumno => $datos) {
            if (!empty($datos['estado'])) {
                $total_registros++;
                $estado = $datos['estado'];
                $observaciones = $datos['observaciones'] ?? '';
                
                if ($controller->registrarAsistencia($id_alumno, $id_asignatura, $fecha, $estado, $observaciones)) {
                    $registros_exitosos++;
                }
            }
        }
        
        if ($registros_exitosos > 0) {
            header("Location: asistencia.php?mensaje=asistencia_masiva_registrada&asignatura=" . $id_asignatura . "&fecha=" . urlencode($fecha));
            exit;
        }
    }
}

$asistencias = $controller->getAsistenciaPorFecha($fecha_actual, $asignatura_filtro);
$alumnos_asignatura = [];
$asignatura_nombre = '';

if ($asignatura_filtro) {
    $alumnos_asignatura = $controller->getAlumnosPorAsignatura($asignatura_filtro);
    // Obtener nombre de la asignatura
    foreach ($asignaturas as $asignatura) {
        if ($asignatura['id'] == $asignatura_filtro) {
            $asignatura_nombre = $asignatura['nombre'];
            break;
        }
    }
}

require("views/cabecera.php");
?>

<style>
.btn-guardar-asistencia {
    padding: 15px 30px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.btn-guardar-asistencia:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-guardar-asistencia:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<div id="contenido">
    <h1>Control de Asistencia</h1>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'asistencia_registrada'): ?>
        <div class="aviso exito">Asistencia registrada correctamente</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'asistencia_masiva_registrada'): ?>
        <div class="aviso exito">Asistencias registradas correctamente</div>
    <?php endif; ?>

    <!-- Filtros -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow);">
        <h3>Seleccionar Asignatura y Fecha</h3>
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label>Asignatura *</label>
                <select name="asignatura" required>
                    <option value="">Seleccionar asignatura</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?php echo $asignatura['id']; ?>" <?php echo $asignatura_filtro == $asignatura['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Fecha *</label>
                <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha_actual); ?>" required>
            </div>
            
            <div>
                <button type="submit">Ver Alumnos</button>
            </div>
        </form>
    </div>

    <?php if ($asignatura_filtro && !empty($alumnos_asignatura)): ?>
        <!-- Tabla de asistencia masiva -->
        <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
            <h3>Asistencia - <?php echo htmlspecialchars($asignatura_nombre); ?> - <?php echo date('d/m/Y', strtotime($fecha_actual)); ?></h3>
            
            <form method="POST" id="formAsistenciaMasiva">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="hidden" name="id_asignatura" value="<?php echo $asignatura_filtro; ?>">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_actual); ?>">
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; margin-top: 16px;">
                        <thead>
                            <tr style="background: var(--thead);">
                                <th style="padding: 12px; text-align: left;">Alumno</th>
                                <th style="padding: 12px; text-align: center;">Estado</th>
                                <th style="padding: 12px; text-align: left;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos_asignatura as $alumno): ?>
                                <?php
                                // Buscar asistencia existente para este alumno
                                $asistencia_existente = null;
                                foreach ($asistencias as $asistencia) {
                                    if ($asistencia['id_alumno'] == $alumno['id']) {
                                        $asistencia_existente = $asistencia;
                                        break;
                                    }
                                }
                                ?>
                                <tr>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                        <strong><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></strong>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border); text-align: center;">
                                        <select name="asistencia[<?php echo $alumno['id']; ?>][estado]" style="padding: 8px; border: 1px solid var(--border); border-radius: 6px; background: var(--card); color: var(--text);">
                                            <option value="">Seleccionar</option>
                                            <option value="presente" <?php echo $asistencia_existente && $asistencia_existente['estado'] === 'presente' ? 'selected' : ''; ?>>✅ Presente</option>
                                            <option value="falta" <?php echo $asistencia_existente && $asistencia_existente['estado'] === 'falta' ? 'selected' : ''; ?>>❌ Falta</option>
                                            <option value="justificada" <?php echo $asistencia_existente && $asistencia_existente['estado'] === 'justificada' ? 'selected' : ''; ?>>📝 Justificada</option>
                                            <option value="tardanza" <?php echo $asistencia_existente && $asistencia_existente['estado'] === 'tardanza' ? 'selected' : ''; ?>>⏰ Tardanza</option>
                                        </select>
                                    </td>
                                    <td style="padding: 12px; border-bottom: 1px solid var(--border);">
                                        <input type="text" name="asistencia[<?php echo $alumno['id']; ?>][observaciones]" 
                                               value="<?php echo $asistencia_existente ? htmlspecialchars($asistencia_existente['observaciones']) : ''; ?>"
                                               placeholder="Observaciones..." 
                                               style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 6px; background: var(--card); color: var(--text);">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px; text-align: center; padding: 20px; background: var(--card-alt); border-radius: 8px;">
                    <button type="submit" name="registrar_asistencia_masiva" class="btn-guardar-asistencia">
                        💾 Guardar Asistencias
                    </button>
                </div>
            </form>
        </div>
    <?php elseif ($asignatura_filtro && empty($alumnos_asignatura)): ?>
        <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
            <p style="text-align: center; color: var(--muted); padding: 20px;">
                No hay alumnos registrados en esta asignatura.
            </p>
        </div>
    <?php else: ?>
        <div style="background: var(--card); padding: 20px; border-radius: 12px; box-shadow: var(--shadow);">
            <p style="text-align: center; color: var(--muted); padding: 20px;">
                Selecciona una asignatura y fecha para ver los alumnos y registrar asistencia.
            </p>
        </div>
    <?php endif; ?>

    <!-- Estadísticas de asistencia -->
    <div style="background: var(--card); padding: 20px; border-radius: 12px; margin-top: 20px; box-shadow: var(--shadow);">
        <h3>Estadísticas de Asistencia</h3>
        <div class="dashboard-grid">
            <?php
            $stats_generales = $controller->getEstadisticasAsistencia(1); // Ejemplo con alumno ID 1
            ?>
            <div class="card-kpi">
                <h3>Total Clases</h3>
                <p class="kpi-value"><?php echo $stats_generales['total_clases']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>Presentes</h3>
                <p class="kpi-value"><?php echo $stats_generales['presentes']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>Faltas</h3>
                <p class="kpi-value"><?php echo $stats_generales['faltas']; ?></p>
            </div>
            <div class="card-kpi">
                <h3>% Asistencia</h3>
                <p class="kpi-value"><?php echo $stats_generales['porcentaje_asistencia']; ?>%</p>
            </div>
        </div>
    </div>
</div>

<?php require("views/pieDePagina.php"); ?>
</html>
