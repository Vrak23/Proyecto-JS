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

        // ── GET: Listar todas las matrículas ─────────────
        case 'GET':
            $sql = "SELECT M.ID_MATRICULA, M.CODIGO, M.FECHA_MATRICULA, M.PERIODO, M.ESTADO,
                           A.NOMBRE, A.APELLIDO, A.DNI_ALUMNO,
                           AU.NIVEL, AU.GRADO, AU.SECCION,
                           C.NOMBRE AS CURSO
                    FROM MATRICULA M
                    JOIN ALUMNO A  ON M.ID_ALUMNO = A.ID_ALUMNO
                    JOIN AULA AU   ON M.ID_AULA   = AU.ID_AULA
                    JOIN CURSO C   ON M.ID_CURSO  = C.ID_CURSO
                    ORDER BY M.ID_MATRICULA DESC";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── POST: Crear matrícula ────────────────────────
        case 'POST':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_alumno) && !empty($d->id_aula) && !empty($d->id_curso) && !empty($d->codigo)) {
                $sql = "INSERT INTO MATRICULA (ID_ALUMNO, ID_AULA, ID_CURSO, FECHA_MATRICULA, PERIODO, ESTADO, CODIGO)
                        VALUES (:alumno, :aula, :curso, :fecha, :periodo, :estado, :codigo)";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'alumno' => $d->id_alumno,
                    'aula'   => $d->id_aula,
                    'curso'  => $d->id_curso,
                    'fecha'  => $d->fecha_matricula ?? date('Y-m-d'),
                    'periodo'=> $d->periodo ?? '',
                    'estado' => $d->estado ?? 'Activa',
                    'codigo' => $d->codigo,
                ]);
                if ($exito) {
                    http_response_code(201);
                    echo json_encode(["mensaje" => "Matrícula registrada con éxito."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo registrar la matrícula."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Faltan campos: id_alumno, id_aula, id_curso, codigo."]);
            }
            break;

        // ── PUT: Actualizar matrícula ────────────────────
        case 'PUT':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_matricula)) {
                $sql = "UPDATE MATRICULA SET
                            ID_ALUMNO      = :alumno,
                            ID_AULA        = :aula,
                            ID_CURSO       = :curso,
                            FECHA_MATRICULA= :fecha,
                            PERIODO        = :periodo,
                            ESTADO         = :estado
                        WHERE ID_MATRICULA = :id";
                $stmt = $pdo->prepare($sql);
                $exito = $stmt->execute([
                    'alumno' => $d->id_alumno,
                    'aula'   => $d->id_aula,
                    'curso'  => $d->id_curso,
                    'fecha'  => $d->fecha_matricula ?? date('Y-m-d'),
                    'periodo'=> $d->periodo ?? '',
                    'estado' => $d->estado ?? 'Activa',
                    'id'     => $d->id_matricula,
                ]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Matrícula actualizada correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo actualizar la matrícula."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID de la matrícula."]);
            }
            break;

        // ── DELETE: Eliminar matrícula ───────────────────
        case 'DELETE':
            $d = json_decode(file_get_contents("php://input"));

            if (!empty($d->id_matricula)) {
                $stmt = $pdo->prepare("DELETE FROM MATRICULA WHERE ID_MATRICULA = :id");
                $exito = $stmt->execute(['id' => $d->id_matricula]);
                if ($exito) {
                    http_response_code(200);
                    echo json_encode(["mensaje" => "Matrícula eliminada correctamente."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["mensaje" => "No se pudo eliminar la matrícula."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["mensaje" => "Falta el ID de la matrícula."]);
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
