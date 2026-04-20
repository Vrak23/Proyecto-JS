<?php
require_once 'conexion.php';

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS MATRICULA (
        ID_MATRICULA INT AUTO_INCREMENT PRIMARY KEY,
        ID_ALUMNO INT NOT NULL,
        ID_AULA INT NOT NULL,
        ID_CURSO INT NOT NULL,
        FECHA_MATRICULA DATE NOT NULL,
        PERIODO VARCHAR(20) DEFAULT '',
        ESTADO VARCHAR(20) DEFAULT 'Activa',
        CODIGO VARCHAR(30) UNIQUE NOT NULL,
        FECHA_REGISTRO DATETIME DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT FK_MATRICULA_ALUMNO FOREIGN KEY (ID_ALUMNO) REFERENCES ALUMNO(ID_ALUMNO) ON DELETE CASCADE,
        CONSTRAINT FK_MATRICULA_AULA FOREIGN KEY (ID_AULA) REFERENCES AULA(ID_AULA) ON DELETE CASCADE,
        CONSTRAINT FK_MATRICULA_CURSO FOREIGN KEY (ID_CURSO) REFERENCES CURSO(ID_CURSO) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $opcion = $_POST['opcion'] ?? '';

    switch ($opcion) {
        case '1':
            $codigo = 'MAT-' . date('Y') . '-' . substr(str_shuffle('0123456789'), 0, 4);
            $stmt = $pdo->prepare("INSERT INTO MATRICULA
                (ID_ALUMNO, ID_AULA, ID_CURSO, FECHA_MATRICULA, PERIODO, ESTADO, CODIGO)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['id_alumno']        ?? 0,
                $_POST['id_aula']          ?? 0,
                $_POST['id_curso']         ?? 0,
                $_POST['fecha_matricula']  ?? date('Y-m-d'),
                $_POST['periodo']          ?? '',
                $_POST['estado']           ?? 'Activa',
                $codigo
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Matrícula registrada"]);
            break;

        case '2':
            $stmt = $pdo->prepare("SELECT * FROM MATRICULA WHERE ID_MATRICULA = ?");
            $stmt->execute([$_POST['id_matricula'] ?? 0]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $fila
                ? json_encode(["exito" => true, "matricula" => $fila])
                : json_encode(["exito" => false, "mensaje" => "No encontrado"]);
            break;

        case '3':
            $stmt = $pdo->prepare("DELETE FROM MATRICULA WHERE ID_MATRICULA = ?");
            $stmt->execute([$_POST['id_matricula'] ?? 0]);
            echo json_encode(["exito" => true, "mensaje" => "Matrícula eliminada"]);
            break;

        case '4':
            $stmt = $pdo->query("SELECT M.ID_MATRICULA, M.CODIGO, M.FECHA_MATRICULA, M.PERIODO, M.ESTADO,
                CONCAT(A.NOMBRE, ' ', A.APELLIDO) AS ESTUDIANTE,
                CONCAT(B.GRADO, 'º ', B.SECCION) AS AULA,
                C.NOMBRE AS CURSO
                FROM MATRICULA M
                JOIN ALUMNO A ON A.ID_ALUMNO = M.ID_ALUMNO
                JOIN AULA B ON B.ID_AULA = M.ID_AULA
                JOIN CURSO C ON C.ID_CURSO = M.ID_CURSO
                ORDER BY M.ID_MATRICULA DESC");
            $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["exito" => true, "matriculas" => $matriculas]);
            break;

        case '5':
            $stmt = $pdo->prepare("UPDATE MATRICULA SET
                ID_ALUMNO = ?,
                ID_AULA = ?,
                ID_CURSO = ?,
                FECHA_MATRICULA = ?,
                PERIODO = ?,
                ESTADO = ?
                WHERE ID_MATRICULA = ?");
            $stmt->execute([
                $_POST['id_alumno']        ?? 0,
                $_POST['id_aula']          ?? 0,
                $_POST['id_curso']         ?? 0,
                $_POST['fecha_matricula']  ?? date('Y-m-d'),
                $_POST['periodo']          ?? '',
                $_POST['estado']           ?? 'Activa',
                $_POST['id_matricula']     ?? 0
            ]);
            echo json_encode(["exito" => true, "mensaje" => "Matrícula actualizada"]);
            break;

        case '6':
            $stmt = $pdo->query("SELECT ID_ALUMNO, CONCAT(NOMBRE, ' ', APELLIDO) AS NOMBRE_COMPLETO FROM ALUMNO ORDER BY NOMBRE, APELLIDO");
            $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $pdo->query("SELECT ID_AULA, CONCAT(GRADO, 'º ', SECCION) AS nombre_aula FROM AULA ORDER BY GRADO, SECCION");
            $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $pdo->query("SELECT ID_CURSO, NOMBRE FROM CURSO ORDER BY NOMBRE");
            $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["exito" => true, "alumnos" => $alumnos, "aulas" => $aulas, "cursos" => $cursos]);
            break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(["exito" => false, "mensaje" => "Error BD: " . $e->getMessage()]);
}
?>