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

        // ── GET: Listar todas las aulas ──────────────────
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM AULA ORDER BY ID_AULA DESC");
            $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($aulas);
            break;

        // ── POST: Crear una nueva aula ───────────────────
        case 'POST':
            $datos = json_decode(file_get_contents("php://input"));

            if (!empty($datos->nivel) && !empty($datos->grado) && !empty($datos->seccion)) {
                $sql = "INSERT INTO AULA (NIVEL, GRADO, SECCION, VACANTES_TOTALES, VACANTES_DISPONIBLES)
                        VALUES (:nivel, :grado, :seccion, :totales, :disponibles)";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'nivel'       => $datos->nivel,
                    'grado'       => $datos->grado,
                    'seccion'     => strtoupper($datos->seccion),
                    'totales'     => $datos->vacantes_totales     ?? 0,
                    'disponibles' => $datos->vacantes_disponibles ?? 0,
                ]);
                if ($exito) {
                    http_response_code(201);
                    echo json_encode(["mensaje" => "Aula creada con éxito."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo crear el aula."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Datos incompletos. Faltan campos requeridos."]);
            }
            break;

        // ── PUT: Actualizar un aula existente ────────────
        case 'PUT':
            $datos = json_decode(file_get_contents("php://input"));

            if (!empty($datos->id_aula)) {
                $sql = "UPDATE AULA
                        SET NIVEL                = :nivel,
                            GRADO                = :grado,
                            SECCION              = :seccion,
                            VACANTES_TOTALES     = :totales,
                            VACANTES_DISPONIBLES = :disponibles
                        WHERE ID_AULA = :id";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'nivel'       => $datos->nivel,
                    'grado'       => $datos->grado,
                    'seccion'     => strtoupper($datos->seccion),
                    'totales'     => $datos->vacantes_totales     ?? 0,
                    'disponibles' => $datos->vacantes_disponibles ?? 0,
                    'id'          => $datos->id_aula,
                ]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Aula actualizada correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo actualizar el aula."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del aula a actualizar."]);
            }
            break;

        // ── DELETE: Eliminar un aula ─────────────────────
        case 'DELETE':
            $datos = json_decode(file_get_contents("php://input"));

            if (!empty($datos->id_aula)) {
                $sql = "DELETE FROM AULA WHERE ID_AULA = :id";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute(['id' => $datos->id_aula]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Aula eliminada correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo eliminar el registro."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID del aula a eliminar."]);
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
