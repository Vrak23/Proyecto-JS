let contador = localStorage.getItem("contador");
contador = contador ? parseInt(contador) : 0;

const conteo = document.getElementById("contador");
conteo.textContent = contador;

function actualizarConteo(valor) {
    contador += valor;
    localStorage.setItem("contador", contador);
    conteo.textContent = contador;
}

function aumentar() {
    actualizarConteo(1);
}

function reducir() {
    actualizarConteo(-1);
}

function reset() {
    contador = 0;
    localStorage.removeItem("contador");
    conteo.textContent = contador;
}