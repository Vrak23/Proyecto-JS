$(document).ready(function () {

  $("#formLogin").submit(function (e) {
    e.preventDefault();

    let usuario = $("#logUsuario").val().trim();
    let password = $("#logPassword").val().trim();

    if (usuario === "" || password === "") {
      $("#mensajeLogin").text("Completa todos los campos");
      return;
    }

    // LOGIN
    if (usuario === "Admin" && password === "admin") {

      sessionStorage.setItem("logueado", "true");

      $("#mensajeLogin").text("Acceso correcto");

      setTimeout(() => {
        window.location.href = "dashboard.html";
      }, 1000);

    } else {
      $("#mensajeLogin").text("Usuario o contraseña incorrectos");
    }

  });

});