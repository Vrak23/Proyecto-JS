//1. Paso #1: Capturamos elementos del DOM
const nombreUsuario = document.getElementById("nombreUsuario");
const btnsaludar = document.getElementById("btnsaludar");
const mensaje = document.getElementById("mensaje");

//2. Creamos la funcion
function registrar() {
    let nombre = nombreUsuario.value;
    console.log("El nombre registrado de la consola es: " + nombre);

//3. Mostrar todo en el DOM
    mensaje.textContent = "!Hola, " + nombre + " !bienvenido al curso!";
}