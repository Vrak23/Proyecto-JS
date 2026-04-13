<?php
session_start();
header('Content-Type: application/json');

// 1. Conexión limpia
$host = 'localhost';
$db   = 'MATRICULA';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Captura de datos del formulario
    $usuarioIngresado = isset($_POST['user']) ? trim($_POST['user']) : '';
    $passwordIngresada = isset($_POST['pass']) ? trim($_POST['pass']) : '';

    // 3. Consulta (Traemos todo de la fila para no fallar con el nombre de columna)
    $sql = "SELECT * FROM USUARIO WHERE USERNAME = :usuario LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario' => $usuarioIngresado]);
    
    // FETCH_ASSOC nos da los nombres de las columnas tal cual están en la BD
    $usuarioFila = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioFila) {
        // Buscamos la columna del hash sin importar si es mayúscula o minúscula
        // Esto soluciona problemas de configuración en el servidor (Case Sensitivity)
        $columnas = array_change_key_case($usuarioFila, CASE_UPPER);
        $hashEnBD = $columnas['PASSWORD_HASH'];

        if (password_verify($passwordIngresada, $hashEnBD)) {
            $_SESSION['usuario_id'] = $columnas['ID'];
            
            echo json_encode([
                "exito" => true,
                "mensaje" => "Acceso correcto"
            ]);
        } else {
            echo json_encode([
                "exito" => false,
                "mensaje" => "La contraseña no coincide con el registro."
            ]);
        }
    } else {
        echo json_encode([
            "exito" => false,
            "mensaje" => "El usuario '$usuarioIngresado' no existe."
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "Error de BD: " . $e->getMessage()
    ]);
}