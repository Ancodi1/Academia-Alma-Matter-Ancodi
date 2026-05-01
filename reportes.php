<?php
require("views/cabecera.php");
require_once("controllers/AlumnoController.php");

$alumnoController = new AlumnoController();

// Obtener promedios por asignatura
$promediosAsignatura = $alumnoController->conexion->realizarConsultaSQL("
    SELECT a.nombre AS asignatura, AVG(e.nota) AS promedio, COUNT(e.idAlumno) AS num_examenes
    FROM Examen e
    JOIN Asignatura a ON e.idAsignatura = a.id
    GROUP BY e.idAsignatura, a.nombre
    ORDER BY promedio DESC
");

// Obtener promedios por alumno
$promediosAlumno = $alumnoController->conexion->realizarConsultaSQL("
    SELECT al.nombre, al.apellidos, AVG(e.nota) AS promedio, COUNT(e.idAsignatura) AS num_examenes
    FROM Examen e
    JOIN Alumno al ON e.idAlumno = al.id
    GROUP BY e.idAlumno, al.nombre, al.apellidos
    ORDER BY promedio DESC
");

?>
<!-- Divisor del Contenido -->
<div id="contenido">
    <h1>Reportes y Estadísticas</h1>
    
    <h2>Promedios por Asignatura</h2>
    <table id="tablaReportes">
        <tr>
            <td>Asignatura</td>
            <td>Promedio</td>
            <td>Número de Exámenes</td>
        </tr>
        <?php while ($row = $promediosAsignatura->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['asignatura']); ?></td>
            <td><?php echo number_format($row['promedio'], 2); ?></td>
            <td><?php echo $row['num_examenes']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <h2>Promedios por Alumno</h2>
    <table id="tablaReportes">
        <tr>
            <td>Alumno</td>
            <td>Promedio</td>
            <td>Número de Exámenes</td>
        </tr>
        <?php while ($row = $promediosAlumno->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></td>
            <td><?php echo number_format($row['promedio'], 2); ?></td>
            <td><?php echo $row['num_examenes']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require("views/pieDePagina.php"); ?>

</html>