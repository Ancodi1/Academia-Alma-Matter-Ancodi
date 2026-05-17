<?php
require_once(__DIR__ . "/ArchivoController.php");
require_once(__DIR__ . "/../models/csrf.php");
require_once(__DIR__ . "/../models/auth.php");

requerirInterno();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../editorAlumnos.php");
    exit;
}

if (!isset($_POST['csrf_token']) || !validarTokenCSRF($_POST['csrf_token'])) {
    header("Location: ../archivosAlumno.php?id=" . intval($_POST['idAlumno'] ?? 0) . "&error=csrf");
    exit;
}

$idAlumno = isset($_POST['idAlumno']) ? intval($_POST['idAlumno']) : 0;
$controller = new ArchivoController();

if (isset($_POST['eliminarArchivo'])) {
    $idArchivo = isset($_POST['idArchivo']) ? intval($_POST['idArchivo']) : 0;
    $archivo = $controller->getArchivo($idArchivo);
    if ($archivo && intval($archivo['idAlumno']) === $idAlumno && $controller->eliminarArchivo($idArchivo)) {
        $ruta = __DIR__ . "/../" . $archivo['ruta'];
        if (is_file($ruta)) @unlink($ruta);
        header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&mensaje=eliminado");
        exit;
    }
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=eliminar");
    exit;
}

if ($idAlumno <= 0 || !isset($_FILES['archivoAlumno']) || $_FILES['archivoAlumno']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=archivo");
    exit;
}

$permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
$mimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
$maxBytes = 5 * 1024 * 1024;
$original = basename($_FILES['archivoAlumno']['name']);
$extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
if (!in_array($extension, $permitidas)) {
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=tipo");
    exit;
}
if ($_FILES['archivoAlumno']['size'] > $maxBytes) {
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=tamano");
    exit;
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['archivoAlumno']['tmp_name']);
if (!in_array($mime, $mimePermitidos)) {
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=tipo");
    exit;
}

$uploadDir = __DIR__ . '/../uploads/alumnos/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$fileName = $idAlumno . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
$targetFile = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['archivoAlumno']['tmp_name'], $targetFile)) {
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'documento';
    $controller->guardarArchivo($idAlumno, $original, $tipo, 'uploads/alumnos/' . $fileName);
    header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&mensaje=subido");
    exit;
}

header("Location: ../archivosAlumno.php?id=" . $idAlumno . "&error=subida");
exit;
?>
