<?php require_once("views/cabecera.php"); ?>

		<!--Divisor del Contenido-->
		<div id="contenido">
		 <h1>Bienvenido a Alma Mater </h1>
		 <h2>Nuevo Alumno </h2>
		 
		 <?php
		 // Mostrar mensajes de error del servidor
		 if (isset($_GET['error'])) {
		     $error = $_GET['error'];
		     if ($error == 'campos_vacios') {
		         echo '<div id="error" style="color: red; margin: 10px 0; padding: 10px; background-color: #ffe6e6; border: 1px solid #ff9999; border-radius: 5px;">Por favor, complete todos los campos.</div>';
		     } elseif ($error == 'edad_invalida') {
		         echo '<div id="error" style="color: red; margin: 10px 0; padding: 10px; background-color: #ffe6e6; border: 1px solid #ff9999; border-radius: 5px;">La edad debe ser un número válido entre 1 y 120.</div>';
		     } elseif ($error == 'base_datos') {
		         echo '<div id="error" style="color: red; margin: 10px 0; padding: 10px; background-color: #ffe6e6; border: 1px solid #ff9999; border-radius: 5px;">Error al guardar en la base de datos. Inténtelo de nuevo.</div>';
		     }
		 }
		 ?>
		 
			<form id="nuevoAlumno"
			action="controllers/nuevoAlumno.php" method="post" enctype="multipart/form-data">
				<label for="nombreAlumno">Nombre:</label><br>
			<input type="text"
			id="nombreAlumno" name="nombreAlumno"><br>
				<label for="apellidoAlumno">Apellidos:</label><br>
			<input type="text"
			id="apellidoAlumno" name="apellidoAlumno"><br>
				<label for="edadAlumno">Edad:</label><br>
			<input type="text"
			id="edadAlumno" name="edadAlumno"><br>
				<label for="emailAlumno">Email:</label><br>
			<input type="email"
			id="emailAlumno" name="emailAlumno"><br>
				<label for="fotoAlumno">Foto (opcional):</label><br>
			<input type="file"
			id="fotoAlumno" name="fotoAlumno" accept="image/*"><br>
			<input type="submit" onclick="return enviarFormulario()" 
			value="Dar Alta de Nuevo Alumno">
			<div id="error" style="color: red; margin-top: 10px;"></div>
		</form>
		<script>
		function enviarFormulario(){
			console.log("Enviar formulario");
			//Array con los mensajes de error
			var mensajesError=[];
			//verificamos que se envía toda la información
			if(nombreAlumno.value==null || nombreAlumno.value=="")
				mensajesError.push("Falta el nombre del alumno");
			if(apellidoAlumno.value==null || apellidoAlumno.value=="")
				mensajesError.push("Falta los apellidos del alumno");
			if(edadAlumno.value==null || edadAlumno.value=="")
				mensajesError.push("Falta la edad del alumno");
			if(emailAlumno.value==null || emailAlumno.value=="")
				mensajesError.push("Falta el email del alumno");
			
			// Si hay errores, los mostramos y no enviamos el formulario
			if(mensajesError.length > 0){
				document.getElementById('error').innerHTML = mensajesError.join(", ");
				return false;
			}
			
			// Si no hay errores, limpiamos el mensaje de error y enviamos el formulario
			document.getElementById('error').innerHTML = "";
			return true;
		}
		</script>
		</div>
		
        <?php require_once("views/pieDePagina.php"); ?>
	</body>
	<script src="js/alumnos.js"></script>
</html