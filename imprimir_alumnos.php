<?php
require_once(__DIR__ . '/models/session.php');
authorizeRoles(['admin','profesor']);
require_once(__DIR__ . '/controllers/AlumnoController.php');

$controller = new AlumnoController();
$termino = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$alumnos = $controller->buscarAlumnos($termino, 1, 10000);

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Imprimir alumnos</title>
        <style>
            body{font-family:Arial,Helvetica,sans-serif;margin:24px}
            h1{margin:0 0 16px 0}
            table{border-collapse:collapse;width:100%}
            th,td{border:1px solid #ddd;padding:8px}
            th{background:#f5f5f5;text-align:left}
            .meta{margin-bottom:12px;color:#555}
            @media print{.no-print{display:none}}
        </style>
    </head>
    <body>
        <button class="no-print" onclick="window.print()" style="float:right">Imprimir</button>
        <h1>Listado de alumnos</h1>
        <div class="meta">Generado: <?php echo date('Y-m-d H:i'); ?></div>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Edad</th>
            </tr>
            <?php while($row = $alumnos->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($row['edad']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </body>
</html>


