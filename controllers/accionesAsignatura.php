<?php

require_once("AsignaturaController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

$asignaturaController = new AsignaturaController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
        header("Location: ../editorAsignaturas.php?error=csrf");
        exit;
    }
    
    $id = $_POST['id'];
    
    if (isset($_POST["modificarAsignatura"])) {
        $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : '';
        $curso = isset($_POST["curso"]) ? trim($_POST["curso"]) : '';

        if ($nombre === '' || $curso === '') {
            header("Location: ../editorAsignaturas.php?error=validacion_campos");
            exit;
        }

        if ($asignaturaController->modificarAsignatura($id, $nombre, $curso)) {
            header("Location: ../editorAsignaturas.php?mensaje=modificado");
            exit;
        } else {
            header("Location: ../editorAsignaturas.php?error=modificar");
            exit;
        }
    }
    
    if (isset($_POST["eliminarAsignatura"])) {
        if ($asignaturaController->borrarAsignatura($id)) {
            header("Location: ../editorAsignaturas.php?mensaje=eliminado");
            exit;
        } else {
            header("Location: ../editorAsignaturas.php?error=eliminar");
            exit;
        }
    }
    
} else {
    header("Location: ../editorAsignaturas.php");
    exit;
}
