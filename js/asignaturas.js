var nombreAsignatura=document.getElementById('nombreAsignatura');
var cursoAsignatura=document.getElementById('cursoAsignatura');
var error=document.getElementById('error');
error.style.color="red";
function enviarFormulario(){
    console.log("Enviando formulario");
    //Array con los mensaje de error
    var mensajesError=[];
    //verificamos que se envía toda la información
    if(nombreAsignatura.value==null || nombreAsignatura.value=="")
        mensajesError.push("Falta el nombre de la asignatura");
    if(cursoAsignatura.value==null || cursoAsignatura.value=="")
        mensajesError.push("Falta el curso de la asignatura");
    
    //Si hay errores, los mostramos y no enviamos el formulario
    if(mensajesError.length > 0){
        error.innerHTML = mensajesError.join(", ");
        return false;
    }
    
    // Si no hay errores, limpiamos el mensaje de error y enviamos el formulario
    error.innerHTML = "";
    return true;
}