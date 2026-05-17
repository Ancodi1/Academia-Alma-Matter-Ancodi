<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>

<div id="contenido">
    <h1>Bienvenido a Refuerzo Escolar</h1>
    <div id="contenidoIndex">
        <h2>Asistencia registrada correctamente</h2>
        <p>La asistencia ha sido guardada con éxito.</p>
    </div>
    <div id="opcionesGestion">
        <div class="opcionGestion">
            <a href="gestionAsistencia.php">
                <img src="img/volver.png" alt="Volver" class="imagenGestion">
                <p>Volver al módulo de Asistencia</p>
            </a>
        </div>
    </div>
</div>

<?php require_once("views/pieDePagina.php"); ?>
</body>
</html>
