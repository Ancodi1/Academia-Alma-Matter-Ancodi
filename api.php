<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once("models/mysqlConnect.php");
require_once("models/auth.php");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$recurso = $_GET['recurso'] ?? 'estado';
$conexion = new mysqlConn();
$idAlumnoSesion = isset($_SESSION['idAlumno']) ? intval($_SESSION['idAlumno']) : 0;

function rows($result) {
    $data = [];
    if ($result) while ($row = $result->fetch_assoc()) $data[] = $row;
    return $data;
}

switch ($recurso) {
    case 'alumnos':
        if (!usuarioActualEsInterno()) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            break;
        }
        echo json_encode(rows($conexion->realizarConsultaSQL("SELECT id, nombre, apellidos, edad, email, telefono, curso_actual FROM Alumno ORDER BY apellidos")));
        break;
    case 'asignaturas':
        if (usuarioActualEsInterno()) {
            echo json_encode(rows($conexion->realizarConsultaSQL("SELECT id, nombre, curso FROM Asignatura ORDER BY curso, nombre")));
        } elseif ($idAlumnoSesion > 0) {
            $stmt = $conexion->preparar("SELECT a.id, a.nombre, a.curso FROM Matricula m JOIN Asignatura a ON a.id = m.idAsignatura WHERE m.idAlumno = ? ORDER BY a.curso, a.nombre");
            $stmt->bind_param("i", $idAlumnoSesion);
            $stmt->execute();
            echo json_encode(rows($stmt->get_result()));
            $stmt->close();
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
        }
        break;
    case 'horarios':
        if (usuarioActualEsInterno()) {
            echo json_encode(rows($conexion->realizarConsultaSQL("SELECT h.*, a.nombre AS asignatura, a.curso FROM Horario h JOIN Asignatura a ON a.id = h.idAsignatura ORDER BY h.diaSemana, h.horaInicio")));
        } elseif ($idAlumnoSesion > 0) {
            $stmt = $conexion->preparar(
                "SELECT h.diaSemana, h.horaInicio, h.horaFin, h.aula, a.nombre AS asignatura, a.curso " .
                "FROM Matricula m JOIN Horario h ON h.idAsignatura = m.idAsignatura JOIN Asignatura a ON a.id = h.idAsignatura " .
                "WHERE m.idAlumno = ? ORDER BY h.diaSemana, h.horaInicio"
            );
            $stmt->bind_param("i", $idAlumnoSesion);
            $stmt->execute();
            echo json_encode(rows($stmt->get_result()));
            $stmt->close();
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
        }
        break;
    case 'pagos':
        if (usuarioActualEsInterno()) {
            echo json_encode(rows($conexion->realizarConsultaSQL("SELECT p.*, al.nombre, al.apellidos FROM Pago p JOIN Alumno al ON al.id = p.idAlumno ORDER BY p.fechaVencimiento DESC")));
        } elseif ($idAlumnoSesion > 0) {
            $stmt = $conexion->preparar("SELECT p.id, p.concepto, p.importe, p.fechaVencimiento, p.fechaPago, p.estado FROM Pago p WHERE p.idAlumno = ? ORDER BY p.fechaVencimiento DESC");
            $stmt->bind_param("i", $idAlumnoSesion);
            $stmt->execute();
            echo json_encode(rows($stmt->get_result()));
            $stmt->close();
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
        }
        break;
    case 'resumen':
        if (usuarioActualEsInterno()) {
            $res = [
                'alumnos' => rows($conexion->realizarConsultaSQL("SELECT COUNT(*) AS total FROM Alumno"))[0]['total'] ?? 0,
                'asignaturas' => rows($conexion->realizarConsultaSQL("SELECT COUNT(*) AS total FROM Asignatura"))[0]['total'] ?? 0,
                'pagosPendientes' => rows($conexion->realizarConsultaSQL("SELECT COUNT(*) AS total FROM Pago WHERE estado <> 'Pagado'"))[0]['total'] ?? 0,
            ];
        } else {
            $res = ['portal' => true, 'idAlumno' => $idAlumnoSesion];
        }
        echo json_encode($res);
        break;
    default:
        echo json_encode(['ok' => true, 'recursos' => ['alumnos', 'asignaturas', 'horarios', 'pagos', 'resumen']]);
}
?>
