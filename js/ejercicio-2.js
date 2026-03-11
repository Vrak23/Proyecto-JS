//1. paso #1: declarar un array con 5 lenguajes de programacion
const lenguajes = ["Python", "Java", "JavaScript", "PHP", "C#"];

//paso #2: capturar el elemento del DOM
const lista = document.getElementById("lista");
let elementos = "";

//paso 3#: recorrer el bucle FOR para recorrer un Array
for(let i = 0; i < lenguajes.length; i++) {
    if (lenguajes[i] === "JavaScript") {
        alert("JavaScript sirve para el frontend y el backend");
    }
    // PASO #4: Acumulamos cada lenguaje dentro de las etiquetas <li>.
    elementos += "<li>" + lenguajes[i] + "</li>";
}
// paso #5: capturamos y mostramos toda la lista en pantalla
lista.innerHTML = elementos;