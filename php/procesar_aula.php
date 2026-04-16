<?php
require_once 'conexion.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

$opcion = $_POST['opcion'] ?? '';

try {

    switch ($opcion) {

        // ── INSERT ────────────────────────────────────────
        case '1':
            $stmt = $pdo->prepare("INSERT INTO AULA
                (NIVEL, GRADO, SECCION, VACANTES_TOTALES, VACANTES_DISPONIBLES)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nivel']                ?? '',
                $_POST['grado']                ?? 0,
                strtoupper($_POST['seccion']   ?? 'A'),
                $_POST['vacantes_totales']     ?? 0,
                $_POST['vacantes_disponibles'] ?? 0,
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Aula registrada"]);
            break;

        // ── OBTENER PARA EDITAR ───────────────────────────
        case '2':
            $stmt = $pdo->prepare("SELECT * FROM AULA WHERE ID_AULA = ?");
            $stmt->execute([$_POST['id_aula'] ?? 0]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $fila
                ? json_encode($fila)
                : json_encode(["exito" => false, "mensaje" => "No encontrado"]);
            break;

        // ── ELIMINAR ──────────────────────────────────────
        case '3':
            $stmt = $pdo->prepare("DELETE FROM AULA WHERE ID_AULA = ?");
            $stmt->execute([$_POST['id_aula'] ?? 0]);
            echo json_encode(["exito" => true, "mensaje" => "Aula eliminada"]);
            break;

        // ── LISTAR ────────────────────────────────────────
        case '4':
            $stmt = $pdo->query("SELECT * FROM AULA ORDER BY ID_AULA DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── UPDATE ────────────────────────────────────────
        case '5':
            $stmt = $pdo->prepare("UPDATE AULA SET
                NIVEL                = ?,
                GRADO                = ?,
                SECCION              = ?,
                VACANTES_TOTALES     = ?,
                VACANTES_DISPONIBLES = ?
                WHERE ID_AULA = ?");
            $stmt->execute([
                $_POST['nivel']                ?? '',
                $_POST['grado']                ?? 0,
                strtoupper($_POST['seccion']   ?? 'A'),
                $_POST['vacantes_totales']     ?? 0,
                $_POST['vacantes_disponibles'] ?? 0,
                $_POST['id_aula']              ?? 0,
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Aula actualizada"]);
            break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(["exito" => false, "mensaje" => $e->getMessage()]);
}
?>