<?php
require_once(__DIR__ . "/AsistenciaController.php");
require_once(__DIR__ . "/../models/auth.php");
require_once(__DIR__ . "/../models/csrf.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../nuevoAsistencia.php?error=csrf");
        exit();
    }
    $idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $fecha = isset($_POST['fechaAsistencia']) ? trim($_POST['fechaAsistencia']) : '';
    $estado = isset($_POST['estadoAsistencia']) ? trim($_POST['estadoAsistencia']) : '';

    if ($idAlumno <= 0 || $idAsignatura <= 0 || empty($fecha) || empty($estado)) {
        header("Location: ../nuevoAsistencia.php?error=campos_vacios");
        exit();
    }

    $asistenciaController = new AsistenciaController();
    if ($asistenciaController->insertarAsistencia($idAlumno, $idAsignatura, $fecha, $estado)) {
        if (in_array($estado, ['Ausente', 'Justificada'])) {
            $conexion = $asistenciaController->getConexion();
            $stmt = $conexion->preparar(
                "SELECT al.nombre, al.apellidos, al.email, asig.nombre AS asignatura " .
                "FROM Alumno al JOIN Asignatura asig ON asig.id = ? WHERE al.id = ?"
            );
            if ($stmt) {
                $stmt->bind_param("ii", $idAsignatura, $idAlumno);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($row && !empty($row['email'])) {
                    require_once(__DIR__ . "/../models/mail.php");
                    enviarEmail($row['email'], "Asistencia registrada", "Hola {$row['nombre']} {$row['apellidos']},<br>Se ha registrado asistencia como {$estado} en {$row['asignatura']} el {$fecha}.");
                }
            }
        }
        header("Location: ../altaAsistenciaCorrecta.php");
        exit();
    }

    header("Location: ../nuevoAsistencia.php?error=base_datos");
    exit();
}

header("Location: ../nuevoAsistencia.php");
exit();
?>
