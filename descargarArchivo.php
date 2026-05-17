<?php
require_once("models/auth.php");
require_once("controllers/ArchivoController.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new ArchivoController();
$archivo = $controller->getArchivo($id);

if (!$archivo) {
    http_response_code(404);
    exit("Archivo no encontrado");
}

$idAlumnoSesion = isset($_SESSION['idAlumno']) ? intval($_SESSION['idAlumno']) : 0;
if (!usuarioActualEsInterno() && intval($archivo['idAlumno']) !== $idAlumnoSesion) {
    http_response_code(403);
    exit("No autorizado");
}

$ruta = realpath(__DIR__ . "/" . $archivo['ruta']);
$baseUploads = realpath(__DIR__ . "/uploads");
if (!$ruta || !$baseUploads || strpos($ruta, $baseUploads) !== 0 || !is_file($ruta)) {
    http_response_code(404);
    exit("Archivo no encontrado");
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($ruta) ?: 'application/octet-stream';
header("Content-Type: " . $mime);
header('Content-Disposition: inline; filename="' . basename($archivo['nombre_archivo']) . '"');
header("Content-Length: " . filesize($ruta));
readfile($ruta);
exit;
?>
