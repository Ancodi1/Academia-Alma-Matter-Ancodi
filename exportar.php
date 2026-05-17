<?php
require_once("models/auth.php");
requerirInterno();
require_once("controllers/AlumnoController.php");

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'alumnos';
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'csv';
$controller = new AlumnoController();
$conexion = $controller->getConexion();

if ($tipo === 'reportes') {
    $titulo = "Reporte de notas";
    $result = $conexion->realizarConsultaSQL(
        "SELECT al.nombre, al.apellidos, a.nombre AS asignatura, a.curso, e.fecha, e.nota " .
        "FROM Examen e JOIN Alumno al ON al.id = e.idAlumno JOIN Asignatura a ON a.id = e.idAsignatura " .
        "ORDER BY e.fecha DESC"
    );
    $headers = ['Alumno', 'Asignatura', 'Curso', 'Fecha', 'Nota'];
    $rows = [];
    if ($result) while ($r = $result->fetch_assoc()) $rows[] = [$r['nombre'] . ' ' . $r['apellidos'], $r['asignatura'], $r['curso'], $r['fecha'], $r['nota']];
} elseif ($tipo === 'asistencia') {
    $titulo = "Reporte de asistencia";
    $result = $conexion->realizarConsultaSQL(
        "SELECT al.nombre, al.apellidos, a.nombre AS asignatura, a.curso, asi.fecha, asi.estado " .
        "FROM Asistencia asi JOIN Alumno al ON al.id = asi.idAlumno JOIN Asignatura a ON a.id = asi.idAsignatura " .
        "ORDER BY asi.fecha DESC"
    );
    $headers = ['Alumno', 'Asignatura', 'Curso', 'Fecha', 'Estado'];
    $rows = [];
    if ($result) while ($r = $result->fetch_assoc()) $rows[] = [$r['nombre'] . ' ' . $r['apellidos'], $r['asignatura'], $r['curso'], $r['fecha'], $r['estado']];
} elseif ($tipo === 'pagos') {
    $titulo = "Reporte de pagos";
    $result = $conexion->realizarConsultaSQL(
        "SELECT al.nombre, al.apellidos, p.concepto, p.importe, p.fechaVencimiento, p.fechaPago, p.estado " .
        "FROM Pago p JOIN Alumno al ON al.id = p.idAlumno ORDER BY p.fechaVencimiento DESC"
    );
    $headers = ['Alumno', 'Concepto', 'Importe', 'Vencimiento', 'Fecha pago', 'Estado'];
    $rows = [];
    if ($result) while ($r = $result->fetch_assoc()) $rows[] = [$r['nombre'] . ' ' . $r['apellidos'], $r['concepto'], $r['importe'], $r['fechaVencimiento'], $r['fechaPago'], $r['estado']];
} else {
    $titulo = "Listado de alumnos";
    $result = $controller->getTodosLosAlumnos();
    $headers = ['ID', 'Nombre', 'Apellidos', 'Edad', 'Email', 'Teléfono', 'Curso'];
    $rows = [];
    if ($result) while ($r = $result->fetch_assoc()) $rows[] = [$r['id'], $r['nombre'], $r['apellidos'], $r['edad'], $r['email'], $r['telefono'], $r['curso_actual']];
}

if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $tipo . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $row) fputcsv($out, $row);
    fclose($out);
    exit;
}

if ($formato === 'xls') {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $tipo . '.xls"');
} else {
    header('Content-Type: text/html; charset=utf-8');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        @media print { .print-actions { display: none; } }
    </style>
</head>
<body>
    <div class="print-actions">
        <?php if ($formato === 'pdf'): ?>
            <button onclick="window.print()">Guardar como PDF</button>
        <?php endif; ?>
    </div>
    <h1><?php echo htmlspecialchars($titulo); ?></h1>
    <table>
        <tr><?php foreach ($headers as $header): ?><th><?php echo htmlspecialchars($header); ?></th><?php endforeach; ?></tr>
        <?php foreach ($rows as $row): ?>
            <tr><?php foreach ($row as $cell): ?><td><?php echo htmlspecialchars((string)$cell); ?></td><?php endforeach; ?></tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
