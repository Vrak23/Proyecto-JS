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

            $dni     = $_POST['dni_alumno'] ?? '';
            $nombres = $_POST['nombres']    ?? '';   // campo del form: name="nombres"
            $apells  = $_POST['apellidos']  ?? '';   // campo del form: name="apellidos"

            if (!$dni || !$nombres || !$apells) {
                throw new Exception("Datos incompletos: DNI, nombres y apellidos son requeridos");
            }

            $sql = "INSERT INTO ALUMNO (
                        DNI_ALUMNO, NOMBRE, APELLIDO,
                        FECHA_NACIMIENTO, EDAD, GENERO,
                        DIRECCION, CELULAR, CORREO,
                        NOMBRE_APODERADO, CELULAR_APODERADO,
                        USER_NAME, PASSWORD_HASH, ESTADO
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $dni,
                $nombres,
                $apells,
                $_POST['fecha_nacimiento']  ?? '2001-01-01',
                $_POST['edad']              ?? 18,
                $_POST['genero']            ?? 'M',
                $_POST['direccion']         ?? '',
                $_POST['celular']           ?? '',
                $_POST['correo']            ?? '',
                $_POST['nombre_apoderado']  ?? '',
                $_POST['celular_apoderado'] ?? '',
                $dni,                                          // USER_NAME = DNI
                password_hash($dni, PASSWORD_DEFAULT),        // PASSWORD_HASH
                $_POST['estado']            ?? 'ACTIVO'
            ]);

            echo json_encode(["exito" => true, "mensaje" => "Alumno registrado correctamente"]);
            break;

        // ── OBTENER PARA EDITAR ───────────────────────────
        case '2':

            $id   = $_POST['id_alumno'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM ALUMNO WHERE ID_ALUMNO = ?");
            $stmt->execute([$id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fila) {
                echo json_encode(["exito" => false, "mensaje" => "Alumno no encontrado"]);
            } else {
                echo json_encode($fila);
            }
            break;

        // ── ELIMINAR ──────────────────────────────────────
        case '3':

            $id   = $_POST['id_alumno'] ?? 0;
            $stmt = $pdo->prepare("DELETE FROM ALUMNO WHERE ID_ALUMNO = ?");
            $stmt->execute([$id]);

            echo json_encode(["exito" => true, "mensaje" => "Alumno eliminado"]);
            break;

        // ── LISTAR ────────────────────────────────────────
        case '4':

            $sql = "SELECT
                        ID_ALUMNO,
                        DNI_ALUMNO,
                        NOMBRE,
                        APELLIDO,
                        FECHA_NACIMIENTO,
                        CELULAR,
                        CORREO,
                        ESTADO
                    FROM ALUMNO
                    ORDER BY ID_ALUMNO DESC";

            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        // ── UPDATE ────────────────────────────────────────
        case '5':

            $stmt = $pdo->prepare("UPDATE ALUMNO SET
                        DNI_ALUMNO          = ?,
                        NOMBRE              = ?,
                        APELLIDO            = ?,
                        FECHA_NACIMIENTO    = ?,
                        EDAD                = ?,
                        GENERO              = ?,
                        DIRECCION           = ?,
                        CELULAR             = ?,
                        CORREO              = ?,
                        NOMBRE_APODERADO    = ?,
                        CELULAR_APODERADO   = ?,
                        ESTADO              = ?
                    WHERE ID_ALUMNO = ?");

            $stmt->execute([
                $_POST['dni_alumno']        ?? '',
                $_POST['nombres']           ?? '',   // campo del form: name="nombres"
                $_POST['apellidos']         ?? '',   // campo del form: name="apellidos"
                $_POST['fecha_nacimiento']  ?? '',
                $_POST['edad']              ?? 0,
                $_POST['genero']            ?? 'M',
                $_POST['direccion']         ?? '',
                $_POST['celular']           ?? '',
                $_POST['correo']            ?? '',
                $_POST['nombre_apoderado']  ?? '',
                $_POST['celular_apoderado'] ?? '',
                $_POST['estado']            ?? 'ACTIVO',
                $_POST['id_alumno']         ?? 0
            ]);

            echo json_encode(["exito" => true, "mensaje" => "Alumno actualizado correctamente"]);
            break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        "exito"   => false,
        "mensaje" => $e->getMessage()
    ]);
}
?>