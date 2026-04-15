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
            $nombre = $_POST['nombre'] ?? '';
            if (!$nombre) throw new Exception("El nombre del curso es requerido");

            $stmt = $pdo->prepare("INSERT INTO CURSO (NOMBRE, DESCRIPCION, CREDITOS)
                                   VALUES (?, ?, ?)");
            $stmt->execute([
                $nombre,
                $_POST['descripcion'] ?? '',
                $_POST['creditos']    ?? 0,
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Curso registrado"]);
            break;

        // ── OBTENER PARA EDITAR ───────────────────────────
        case '2':
            $stmt = $pdo->prepare("SELECT * FROM CURSO WHERE ID_CURSO = ?");
            $stmt->execute([$_POST['id_curso'] ?? 0]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $fila
                ? json_encode($fila)
                : json_encode(["exito" => false, "mensaje" => "No encontrado"]);
            break;

        // ── ELIMINAR ──────────────────────────────────────
        case '3':
            $stmt = $pdo->prepare("DELETE FROM CURSO WHERE ID_CURSO = ?");
            $stmt->execute([$_POST['id_curso'] ?? 0]);
            echo json_encode(["exito" => true, "mensaje" => "Curso eliminado"]);
            break;

        // ── LISTAR ────────────────────────────────────────
        case '4':
            $stmt = $pdo->query("SELECT * FROM CURSO ORDER BY ID_CURSO DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── UPDATE ────────────────────────────────────────
        case '5':
            $stmt = $pdo->prepare("UPDATE CURSO SET
                NOMBRE      = ?,
                DESCRIPCION = ?,
                CREDITOS    = ?
                WHERE ID_CURSO = ?");
            $stmt->execute([
                $_POST['nombre']      ?? '',
                $_POST['descripcion'] ?? '',
                $_POST['creditos']    ?? 0,
                $_POST['id_curso']    ?? 0,
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Curso actualizado"]);
            break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(["exito" => false, "mensaje" => $e->getMessage()]);
}
?>  