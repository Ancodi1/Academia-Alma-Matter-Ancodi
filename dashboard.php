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
$promediosAsignatura = $dc->getPromedioPorAsignatura();
?>
<div id="contenido">
    <h1>Información</h1>
    <div class="dashboard-grid">
        <div class="card-kpi">
            <h3>Alumnos</h3>
            <p class="kpi-value"><?php echo $numAlumnos; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Exámenes</h3>
            <p class="kpi-value"><?php echo $numExamenes; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Promedio de notas</h3>
            <p class="kpi-value"><?php echo $promedio; ?></p>
        </div>
        <div class="card-kpi">
            <h3>Tasa de aprobación</h3>
            <p class="kpi-value"><?php echo $tasa; ?>%</p>
        </div>
    </div>
    <h2 style="margin-top:24px;">Promedio por asignatura</h2>
    <div style="max-width: 1200px; margin: 0 auto;">
        <canvas id="chartAsignaturas" height="240"></canvas>
    </div>
    <p style="margin-top:20px;"><a href="/academia/index.php">Volver al inicio</a></p>
</div>
<?php require("views/pieDePagina.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
    var ctx = document.getElementById('chartAsignaturas');
    if (!ctx) return;
    var labels = <?php echo json_encode(array_map(function($r){ return $r['asignatura']; }, $promediosAsignatura)); ?>;
    var data = <?php echo json_encode(array_map(function($r){ return $r['promedio']; }, $promediosAsignatura)); ?>;
    if (labels.length === 0) return;
    var root = document.documentElement;
    var isDark = root.getAttribute('data-theme') === 'dark';
    var axisColor = getComputedStyle(root).getPropertyValue('--muted').trim() || (isDark ? '#e6edf3' : '#1f2937');
    var gridColor = getComputedStyle(root).getPropertyValue('--border').trim() || (isDark ? '#25324a' : '#dbe2ea');
    var brand = getComputedStyle(root).getPropertyValue('--brand').trim() || '#0a58b0';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Promedio',
                data: data,
                backgroundColor: brand,
                borderRadius: 6,
                maxBarThickness: 64
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 10,
                    grid: { color: gridColor },
                    ticks: { color: axisColor, font: { size: 14 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: axisColor, font: { size: 14 } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
})();
</script>
</html>


