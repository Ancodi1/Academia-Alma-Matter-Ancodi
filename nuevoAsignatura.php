<?php require_once("views/cabecera.php"); ?>
<?php requerirInterno(); ?>
<?php require_once("models/csrf.php"); ?>

		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Refuerzo Escolar </h1>
		 <h2>Nueva Asignatura </h2>
		 
		 <?php
		 if (isset($_GET['error'])) {
		     $error = $_GET['error'];
		     if ($error == 'campos_vacios') {
		         echo '<div id="error" style="color: red; margin: 10px 0; padding: 10px; background-color: #ffe6e6; border: 1px solid #ff9999; border-radius: 5px;">Por favor, complete todos los campos.</div>';
		     } elseif ($error == 'base_datos') {
		         echo '<div id="error" style="color: red; margin: 10px 0; padding: 10px; background-color: #ffe6e6; border: 1px solid #ff9999; border-radius: 5px;">Error al guardar en la base de datos. Inténtelo de nuevo.</div>';
		     }
		 }
		 ?>
		 
			<form id="nuevaAsignatura"
			action="controllers/nuevaAsignatura.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
				<label for="nombreAsignatura">Nombre:</label><br>
			<input type="text"
			id="nombreAsignatura" name="nombreAsignatura"><br>
				<label for="cursoAsignatura">Curso:</label><br>
			<input type="text"
			id="cursoAsignatura" name="cursoAsignatura"><br>
			<input type="submit" onclick="return enviarFormulario()" 
			value="Dar Alta de Nueva Asignatura">
			<div id="error" style="color: red; margin-top: 10px;"></div>
		</form>
		<script>
		function enviarFormulario(){
			var nombreAsignatura = document.getElementById('nombreAsignatura');
			var cursoAsignatura = document.getElementById('cursoAsignatura');
			
			var mensajesError=[];
			if(nombreAsignatura.value==null || nombreAsignatura.value=="")
				mensajesError.push("Falta el nombre de la asignatura");
			if(cursoAsignatura.value==null || cursoAsignatura.value=="")
				mensajesError.push("Falta el curso de la asignatura");
			
			if(mensajesError.length > 0){
				document.getElementById('error').innerHTML = mensajesError.join(", ");
				return false;
			}
			
			document.getElementById('error').innerHTML = "";
			return true;
		}
		</script>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
	<script src="js/asignaturas.js"></script>
</html>
