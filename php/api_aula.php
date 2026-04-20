<?php
// PASO #1: VMAOS A CREAR LAS CABECERAS HTTP
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

//paso numero 2: establecemos conexion con la base de datos
$servidor = "localhost";
$usuario  = "root";
$password = ""; 
$base_datos = "matricula"; 

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["mensaje" => "Error de conexion a la Base de Datos: " . $e->getMessage()]);
    exit();
}

//Paso 3: CAPTURAMOS EL METODO HTTP
$metodoHTTP = $_SERVER['REQUEST_METHOD'];

//Paso 4: CREAMOS LAS PETICIONES CON SWITCH
switch ($metodoHTTP) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Consulta para un solo registro
            $sql = "SELECT * FROM AULA WHERE ID_AULA = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $_GET['id']]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Consulta para obtener todas las aulas
            $sql = "SELECT * FROM AULA";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($resultado);
        break;

    case 'POST':
         // Leer el JSON que envía el cliente
         $datosJSON = json_decode(file_get_contents("php://input"));

         // Validar que lleguen los datos requeridos
         if (!empty($datosJSON->nivel) && !empty($datosJSON->grado) && !empty($datosJSON->seccion)) {
             
             $sql = "INSERT INTO AULA (NIVEL, GRADO, SECCION, VACANTES_TOTALES, VACANTES_DISPONIBLES) 
                     VALUES (:nivel, :grado, :seccion, :totales, :disponibles)";
             
             $stmt = $pdo->prepare($sql);
             
             // Ejecutar con los datos del JSON
             $exito = $stmt->execute([
                 'nivel' => $datosJSON->nivel,
                 'grado' => $datosJSON->grado,
                 'seccion' => $datosJSON->seccion,
                 'totales' => $datosJSON->vacantes_totales,
                 'disponibles' => $datosJSON->vacantes_disponibles
             ]);
 
             if ($exito) {
                 http_response_code(201); // Created
                 echo json_encode(["mensaje" => "Aula creada con éxito."]);
             } else {
                 http_response_code(503); // Service Unavailable
                 echo json_encode(["mensaje" => "No se pudo crear el aula."]);
             }
         } else {
             http_response_code(400); // Bad Request
             echo json_encode(["mensaje" => "Datos incompletos. Faltan campos requeridos."]);
         }
         break;
    case 'PUT':
        if (!empty($datosJSON->id_aula)) {
            
            $sql = "UPDATE AULA 
                    SET NIVEL = :nivel, GRADO = :grado, SECCION = :seccion, 
                        VACANTES_TOTALES = :totales, VACANTES_DISPONIBLES = :disponibles 
                    WHERE ID_AULA = :id";
            
            $stmt = $pdo->prepare($sql);
            
            $exito = $stmt->execute([
                'nivel' => $datosJSON->nivel,
                'grado' => $datosJSON->grado,
                'seccion' => $datosJSON->seccion,
                'totales' => $datosJSON->vacantes_totales,
                'disponibles' => $datosJSON->vacantes_disponibles,
                'id' => $datosJSON->id_aula
            ]);

            if ($exito) {
                http_response_code(200); // OK
                echo json_encode(["mensaje" => "Aula actualizada correctamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["mensaje" => "No se pudo actualizar el aula."]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Falta el ID del aula a actualizar."]);
        }
        break;
    case 'DELETE':
        #code...
        break;
    default:
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
        break;
}
?>