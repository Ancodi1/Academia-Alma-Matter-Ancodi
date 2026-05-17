<?php
require_once(__DIR__ . "/AlumnoController.php");
require_once(__DIR__ . "/MatriculaController.php");
require_once(__DIR__ . "/PagoController.php");

class PortalController {
    private $alumnos;
    private $matriculas;
    private $pagos;

    public function __construct() {
        $this->alumnos = new AlumnoController();
        $this->matriculas = new MatriculaController();
        $this->pagos = new PagoController();
    }

    public function getAlumnoPortal($idAlumno) {
        return $this->alumnos->getAlumnoPorId($idAlumno);
    }

    public function getResumen($idAlumno) {
        return $this->alumnos->getResumenAlumno($idAlumno);
    }

    public function getMatriculas($idAlumno) {
        return $this->matriculas->getAsignaturasDeAlumno($idAlumno);
    }

    public function getExamenes($idAlumno) {
        return $this->alumnos->getExamenesPorAlumno($idAlumno);
    }

    public function getAsistencias($idAlumno) {
        return $this->alumnos->getAsistenciasPorAlumno($idAlumno, 20);
    }

    public function getPagos($idAlumno) {
        return $this->pagos->getPagos('', $idAlumno);
    }
}
?>
