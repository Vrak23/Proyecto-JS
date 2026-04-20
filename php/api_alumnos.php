<?php
require_once 'conexion.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$metodo = $_SERVER['REQUEST_METHOD'];

try {

    switch ($metodo) {

        // ── GET: Listar todos los alumnos ────────────────
        case 'GET':
            $stmt = $pdo->query("SELECT ID_ALUMNO, DNI_ALUMNO, NOMBRE, APELLIDO, FECHA_NACIMIENTO, EDAD, GENERO, DIRECCION, CELULAR, CORREO, ESTADO, NOMBRE_APODERADO, CELULAR_APODERADO, USER_NAME, FECHA_REGISTRO FROM ALUMNO ORDER BY ID_ALUMNO DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── POST: Crear un nuevo alumno ──────────────────
        case 'POST':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->dni_alumno) && !empty($d->nombre) && !empty($d->apellido)) {
                $sql = "INSERT INTO ALUMNO (DNI_ALUMNO, NOMBRE, APELLIDO, FECHA_NACIMIENTO, EDAD, GENERO, DIRECCION, CELULAR, CORREO, NOMBRE_APODERADO, CELULAR_APODERADO, USER_NAME, PASSWORD_HASH)
                        VALUES (:dni, :nombre, :apellido, :fnac, :edad, :genero, :dir, :cel, :correo, :nom_ap, :cel_ap, :user, :pass)";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'dni'     => $d->dni_alumno,
                    'nombre'  => $d->nombre,
                    'apellido'=> $d->apellido,
                    'fnac'    => $d->fecha_nacimiento ?? null,
                    'edad'    => $d->edad ?? 0,
                    'genero'  => $d->genero ?? 'M',
                    'dir'     => $d->direccion ?? '',
                    'cel'     => $d->celular ?? '',
                    'correo'  => $d->correo ?? '',
                    'nom_ap'  => $d->nombre_apoderado ?? '',
                    'cel_ap'  => $d->celular_apoderado ?? '',
                    'user'    => $d->user_name ?? $d->dni_alumno,
                    'pass'    => password_hash($d->password ?? '1234', PASSWORD_BCRYPT),
                ]);
                if ($exito) {
                    http_response_code(201);
                    echo json_encode(["mensaje" => "Alumno registrado con éxito."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo registrar el alumno."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Faltan campos requeridos: dni_alumno, nombre, apellido."]);
            }
            break;

        // ── PUT: Actualizar alumno ───────────────────────
        case 'PUT':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_alumno)) {
                $sql = "UPDATE ALUMNO SET
                            NOMBRE              = :nombre,
                            APELLIDO            = :apellido,
                            FECHA_NACIMIENTO    = :fnac,
                            EDAD                = :edad,
                            GENERO              = :genero,
                            DIRECCION           = :dir,
                            CELULAR             = :cel,
                            CORREO              = :correo,
                            NOMBRE_APODERADO    = :nom_ap,
                            CELULAR_APODERADO   = :cel_ap
                        WHERE ID_ALUMNO = :id";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'nombre'  => $d->nombre,
                    'apellido'=> $d->apellido,
                    'fnac'    => $d->fecha_nacimiento ?? null,
                    'edad'    => $d->edad ?? 0,
                    'genero'  => $d->genero ?? 'M',
                    'dir'     => $d->direccion ?? '',
                    'cel'     => $d->celular ?? '',
                    'correo'  => $d->correo ?? '',
                    'nom_ap'  => $d->nombre_apoderado ?? '',
                    'cel_ap'  => $d->celular_apoderado ?? '',
                    'id'      => $d->id_alumno,
                ]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Alumno actualizado correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo actualizar el alumno."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del alumno."]);
            }
            break;

        // ── DELETE: Eliminar alumno ──────────────────────
        case 'DELETE':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_alumno)) {
                $stmt = $pdo->prepare("DELETE FROM ALUMNO WHERE ID_ALUMNO = :id");
                $exito = $stmt->execute(['id' => $d->id_alumno]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Alumno eliminado correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo eliminar el alumno."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del alumno."]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["mensaje" => "Método no permitido."]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["exito" => false, "mensaje" => $e->getMessage()]);
}
?>
