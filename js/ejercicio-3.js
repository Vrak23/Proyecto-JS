// vamos a crear un OBJETO.
const estudiante = {
    // Un mapa esta compuesto de CLAVE y VALOR
    nombre : "Pepito",
    carrera: "informatica y desarrollo de aplicaciones web",
    ciclo: 3,
    //Vamos a crear METODOS (Son las acciones - verbos)
    estudiar: function(){
        console.log("Pepito esta aprendiendo JavaScript.");
    }
};

// Acceder al objeto
console.log(estudiante.nombre);
estudiante.estudiar()

// Definición del constructor
function Computadora(marca, procesador, ram){
    this.marca = marca;
    this.procesador = procesador;
    this.ram = ram;

    // Metodo para encender
    this.encender = function(){
        console.log("Iniciamos el sistema " + this.marca);
    }

    // Corregido: Acceso a this.ram y retorno correcto
    this.aumentarRam = function() {
        return this.ram + " GB"; 
    }
}
// El operador NEW se usa FUERA de la definición del constructor
const PClab1 = new Computadora("HP", "CoreI7", "32");
const PClab2 = new Computadora("Asus", "CoreI5", "16");

console.log(PClab1.marca);
console.log(PClab1.procesador);
console.log(PClab1.ram);

const mensaje = "Tipos de datos JavaScript";

console.log(mensaje.length()); //da el numero de caracteres que hay
console.log(mensaje.trim());
console.log(mensaje.toUpperCase()); //convierte a mayuscula
console.log(mensaje.includes("es")); //buscar que si dentro de la constante esta el termino indicado

const lenguajes = ["HTML", "CSS", "PHP", "JAVASCRIPT"];

lenguajes.push("JAVA"); //Se agrega al final
lenguajes.pop() //Elimina el ultimo
lenguajes.unshift("JAVA"); //para agregar el primero
lenguajes.shift(); // para quitar el primero
lenguajes.log(lenguajes.join("-"));

