<?php
require_once(__DIR__ . "/HorarioController.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idAsignatura = isset($_POST['idAsignatura']) ? intval($_POST['idAsignatura']) : 0;
    $diaSemana = isset($_POST['diaSemana']) ? trim($_POST['diaSemana']) : '';
    $horaInicio = isset($_POST['horaInicio']) ? trim($_POST['horaInicio']) : '';
    $horaFin = isset($_POST['horaFin']) ? trim($_POST['horaFin']) : '';
    $aula = isset($_POST['aula']) ? trim($_POST['aula']) : '';
    $profesor = isset($_POST['profesor']) ? trim($_POST['profesor']) : '';

    if ($idAsignatura <= 0 || empty($diaSemana) || empty($horaInicio) || empty($horaFin) || empty($aula)) {
        header("Location: ../nuevoHorario.php?error=campos_vacios");
        exit();
    }

    $horarioController = new HorarioController();
    if ($horarioController->insertarHorario($idAsignatura, $diaSemana, $horaInicio, $horaFin, $aula, $profesor)) {
        header("Location: ../altaHorarioCorrecto.php");
        exit();
    }

    header("Location: ../nuevoHorario.php?error=base_datos");
    exit();
}

header("Location: ../nuevoHorario.php");
exit();
?>