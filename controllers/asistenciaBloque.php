<?php
require_once(__DIR__ . "/AsistenciaController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../asistenciaClase.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../asistenciaClase.php?error=csrf");
    exit;
}

$idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
$fecha = isset($_POST['fechaAsistencia']) ? trim($_POST['fechaAsistencia']) : '';
$estados = isset($_POST['estado']) && is_array($_POST['estado']) ? $_POST['estado'] : [];

if ($idAsignatura <= 0 || $fecha === '' || !$estados) {
    header("Location: ../asistenciaClase.php?idAsignatura=" . $idAsignatura . "&fecha=" . urlencode($fecha) . "&error=campos");
    exit;
}

$controller = new AsistenciaController();
if ($controller->registrarAsistenciaEnBloque($idAsignatura, $fecha, $estados)) {
    require_once(__DIR__ . "/../models/mail.php");
    $conexion = $controller->getConexion();
    foreach ($estados as $idAlumno => $estado) {
        if (!in_array($estado, ['Ausente', 'Justificada'])) continue;
        $idAlumno = intval($idAlumno);
        $stmt = $conexion->preparar(
            "SELECT al.nombre, al.apellidos, al.email, asig.nombre AS asignatura " .
            "FROM Alumno al JOIN Asignatura asig ON asig.id = ? WHERE al.id = ?"
        );
        if (!$stmt) continue;
        $stmt->bind_param("ii", $idAsignatura, $idAlumno);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row && !empty($row['email'])) {
            enviarEmail($row['email'], "Asistencia registrada", "Hola {$row['nombre']} {$row['apellidos']},<br>Se ha registrado asistencia como {$estado} en {$row['asignatura']} el {$fecha}.");
        }
    }
    header("Location: ../asistenciaClase.php?idAsignatura=" . $idAsignatura . "&fecha=" . urlencode($fecha) . "&mensaje=guardado");
    exit;
}

header("Location: ../asistenciaClase.php?idAsignatura=" . $idAsignatura . "&fecha=" . urlencode($fecha) . "&error=guardar");
exit;
?>
