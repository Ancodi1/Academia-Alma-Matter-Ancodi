<?php
require("views/cabecera.php");
require_once(__DIR__ . '/models/session.php');
authorizeRoles(['admin','profesor']);
require_once(__DIR__ . '/controllers/DashboardController.php');

$dc = new DashboardController();
$numAlumnos = $dc->getNumeroAlumnos();
$numExamenes = $dc->getNumeroExamenes();
$promedio = $dc->getPromedioNotas();
$tasa = $dc->getTasaAprobacion();
?>
<div id="contenido">
    <h1>Dashboard</h1>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
        <div class="card-kpi">
            <h3>Alumnos</h3>
            <p style="font-size:32px;font-weight:bold;"><?php echo $numAlumnos; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Exámenes</h3>
            <p style="font-size:32px;font-weight:bold;"><?php echo $numExamenes; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Promedio de notas</h3>
            <p style="font-size:32px;font-weight:bold;"><?php echo $promedio; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Tasa de aprobación</h3>
            <p style="font-size:32px;font-weight:bold;"><?php echo $tasa; ?>%</p>
        </div>
    </div>
    <p style="margin-top:20px;"><a href="/academia/index.php">Volver al inicio</a></p>
</div>
<?php require("views/pieDePagina.php"); ?>
<style>
.card-kpi{background:#fff;border-radius:8px;padding:16px;box-shadow:0 4px 12px rgba(0,0,0,.08)}
</style>
</html>


