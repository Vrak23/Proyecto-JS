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

        // ── GET: Listar todos los cursos ─────────────────
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM CURSO ORDER BY ID_CURSO DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── POST: Crear un nuevo curso ───────────────────
        case 'POST':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->nombre) && !empty($d->creditos)) {
                $sql = "INSERT INTO CURSO (NOMBRE, DESCRIPCION, CREDITOS)
                        VALUES (:nombre, :desc, :creditos)";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'nombre'  => $d->nombre,
                    'desc'    => $d->descripcion ?? '',
                    'creditos'=> $d->creditos,
                ]);
                if ($exito) {
                    http_response_code(201);
                    echo json_encode(["mensaje" => "Curso creado con éxito."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo crear el curso."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Faltan campos requeridos: nombre, creditos."]);
            }
            break;

        // ── PUT: Actualizar curso ────────────────────────
        case 'PUT':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_curso)) {
                $sql = "UPDATE CURSO SET
                            NOMBRE      = :nombre,
                            DESCRIPCION = :desc,
                            CREDITOS    = :creditos
                        WHERE ID_CURSO = :id";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'nombre'  => $d->nombre,
                    'desc'    => $d->descripcion ?? '',
                    'creditos'=> $d->creditos,
                    'id'      => $d->id_curso,
                ]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Curso actualizado correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo actualizar el curso."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del curso."]);
            }
            break;

        // ── DELETE: Eliminar curso ───────────────────────
        case 'DELETE':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_curso)) {
                $stmt = $pdo->prepare("DELETE FROM CURSO WHERE ID_CURSO = :id");
                $exito = $stmt->execute(['id' => $d->id_curso]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Curso eliminado correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo eliminar el curso."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del curso."]);
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
