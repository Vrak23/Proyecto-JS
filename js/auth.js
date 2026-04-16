function verificarLogin() {
    if (sessionStorage.getItem('logueado') !== 'true') {
        window.location.href = 'login.html';
    }
}

function redirigirSiYaLogueado() {
    if (sessionStorage.getItem('logueado') === 'true') {
        window.location.href = 'dashboard.html';
    }
}

function cerrarSesion() {
    sessionStorage.removeItem('logueado');
    window.location.href = 'login.html';
}
