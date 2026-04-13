<?php
require_once 'conexion.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

$opcion = $_POST['opcion'] ?? '';

try {

    switch ($opcion) {

        // INSERT
        case '1':

            $dni        = $_POST['dni_alumno'] ?? '';
            $nombres    = $_POST['nombres'] ?? '';
            $estado     = $_POST['estado'] ?? 'ACTIVO';

            if (!$dni || !$nombres) {
                throw new Exception("Datos incompletos");
            }

            $sql = "INSERT INTO ALUMNO (
                DNI_ALUMNO, NOMBRES, APELLIDOS, FECHA_NACIMIENTO, EDAD, GENERO,
                DIRECCION, CELULAR, CORREO, NOMBRE_APODERADO,
                CELULAR_APODERADO, USER_NAME, PASSWORD_HASH, ESTADO
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
    $dni,
    $nombres,
    $_POST['apellidos'] ?? '',
    $_POST['fecha_nacimiento'] ?? '2001-01-01',
    $_POST['edad'] ?? 18,
    $_POST['genero'] ?? 'M',
    $_POST['direccion'] ?? '',
    $_POST['celular'] ?? '',
    $_POST['correo'] ?? '',
    $_POST['nombre_apoderado'] ?? '',
    $_POST['celular_apoderado'] ?? '',
    $dni,
    password_hash($dni, PASSWORD_DEFAULT),
    $_POST['estado'] ?? 'ACTIVO'
]);

            echo json_encode(["exito" => true, "mensaje" => "Registrado"]);
        break;

        // EDITAR
        case '2':

            $id = $_POST['id_alumno'];

            $stmt = $pdo->prepare("SELECT * FROM ALUMNO WHERE ID_ALUMNO=?");
            $stmt->execute([$id]);

            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;

        // ELIMINAR
        case '3':

            $id = $_POST['id_alumno'];

            $stmt = $pdo->prepare("DELETE FROM ALUMNO WHERE ID_ALUMNO=?");
            $stmt->execute([$id]);

            echo json_encode(["exito" => true]);
        break;

        // LISTAR
        case '4':

    $sql = "SELECT 
                ID_ALUMNO,
                DNI_ALUMNO,
                NOMBRES,
                APELLIDOS,
                CORREO,
                ESTADO
            FROM ALUMNO
            ORDER BY ID_ALUMNO DESC";

    $stmt = $pdo->query($sql);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

break;

        // UPDATE
        case '5':

            $estado = $_POST['estado'];

            $stmt = $pdo->prepare("UPDATE ALUMNO SET
                DNI_ALUMNO=?,
                NOMBRES=?,
                APELLIDOS=?,
                EDAD=?,
                GENERO=?,
                DIRECCION=?,
                CELULAR=?,
                CORREO=?,
                NOMBRE_APODERADO=?,
                CELULAR_APODERADO=?,
                ESTADO=?
                WHERE ID_ALUMNO=?");

            $stmt->execute([
                $_POST['dni_alumno'],
                $_POST['nombres'],
                $_POST['apellidos'],
                $_POST['edad'],
                $_POST['genero'],
                $_POST['direccion'],
                $_POST['celular'],
                $_POST['correo'],
                $_POST['nombre_apoderado'],
                $_POST['celular_apoderado'],
                $estado,
                $_POST['id_alumno']
            ]);

            echo json_encode(["exito" => true, "mensaje" => "Actualizado"]);
        break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
        break;
    }

} catch (Exception $e) {
    echo json_encode([
        "exito" => false,
        "mensaje" => $e->getMessage()
    ]);
}
?>