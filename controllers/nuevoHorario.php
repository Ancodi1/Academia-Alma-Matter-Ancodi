<?php
require_once(__DIR__ . "/HorarioController.php");
require_once(__DIR__ . "/../models/auth.php");
require_once(__DIR__ . "/../models/csrf.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../nuevoHorario.php?error=csrf");
        exit();
    }
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $diaSemana = isset($_POST['diaSemana']) ? trim($_POST['diaSemana']) : '';
    $horaInicio = isset($_POST['horaInicio']) ? trim($_POST['horaInicio']) : '';
    $horaFin = isset($_POST['horaFin']) ? trim($_POST['horaFin']) : '';
    $aula = isset($_POST['aula']) ? trim($_POST['aula']) : '';
    $profesor = isset($_POST['profesor']) ? trim($_POST['profesor']) : '';
    $idProfesor = isset($_POST['idProfesor']) ? intval($_POST['idProfesor']) : 0;

    if ($idAsignatura <= 0 || empty($diaSemana) || empty($horaInicio) || empty($horaFin) || empty($aula)) {
        header("Location: ../nuevoHorario.php?error=campos_vacios");
        exit();
    }

    $horarioController = new HorarioController();
    if ($horaFin <= $horaInicio) {
        header("Location: ../nuevoHorario.php?error=horas");
        exit();
    }
    if ($horarioController->tieneSolapamiento($diaSemana, $horaInicio, $horaFin, $aula, $idProfesor)) {
        header("Location: ../nuevoHorario.php?error=solapamiento");
        exit();
    }

    if ($horarioController->insertarHorario($idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor, $idProfesor)) {
        header("Location: ../altaHorarioCorrecto.php");
        exit();
    }

    header("Location: ../nuevoHorario.php?error=base_datos");
    exit();
}

header("Location: ../nuevoHorario.php");
exit();
?>
