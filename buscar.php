<?php
require("views/cabecera.php");
requerirInterno();
require_once("controllers/BusquedaController.php");

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$resultados = $q !== '' ? (new BusquedaController())->buscar($q) : null;
?>

<div id="contenido">
    <h1>Búsqueda global</h1>
    <form method="GET" class="filter-bar">
        <div class="form-wide">
            <label for="q">Buscar</label>
            <input id="q" type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Alumno, asignatura o profesor">
        </div>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($resultados): ?>
        <?php
        $bloques = [
            'alumnos' => ['Alumnos', '/fichaAlumno.php?id='],
            'asignaturas' => ['Asignaturas', '/gestionAsignaturas.php'],
            'profesores' => ['Profesores', '/profesores.php'],
        ];
        foreach ($bloques as $clave => $meta):
        ?>
            <section class="panel">
                <h2><?php echo $meta[0]; ?></h2>
                <?php if ($resultados[$clave] && $resultados[$clave]->num_rows > 0): ?>
                    <ul class="listaResumen">
                        <?php while ($row = $resultados[$clave]->fetch_assoc()): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($row['titulo']); ?></strong><br>
                                <?php echo htmlspecialchars($row['detalle'] ?? ''); ?><br>
                                <a class="btn-link" href="<?php echo $clave === 'alumnos' ? $meta[1] . intval($row['id']) : $meta[1]; ?>">Abrir</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="sinClases">Sin resultados.</p>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require("views/pieDePagina.php"); ?>
</body>
</html>
