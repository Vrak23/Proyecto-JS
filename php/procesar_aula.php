<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$op = $_POST['opcion'] ?? '';

switch ($op) {

case '1': // INSERT
    $stmt = $pdo->prepare("INSERT INTO AULA (NIVEL, GRADO, SECCION, VACANTES_TOTALES, VACANTES_DISPONIBLES)
    VALUES (?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['nivel'],
        $_POST['grado'],
        $_POST['seccion'],
        $_POST['vacantes_totales'],
        $_POST['vacantes_disponibles']
    ]);

    echo json_encode(["ok"=>true]);
break;

case '2': // GET
    $stmt = $pdo->prepare("SELECT * FROM AULA WHERE ID_AULA=?");
    $stmt->execute([$_POST['id_aula']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
break;

case '3': // DELETE
    $stmt = $pdo->prepare("DELETE FROM AULA WHERE ID_AULA=?");
    $stmt->execute([$_POST['id_aula']]);
    echo json_encode(["ok"=>true]);
break;

case '4': // LIST
    $stmt = $pdo->query("SELECT * FROM AULA");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
break;

case '5': // UPDATE
    $stmt = $pdo->prepare("UPDATE AULA SET
        NIVEL=?, GRADO=?, SECCION=?, VACANTES_TOTALES=?, VACANTES_DISPONIBLES=?
        WHERE ID_AULA=?");

    $stmt->execute([
        $_POST['nivel'],
        $_POST['grado'],
        $_POST['seccion'],
        $_POST['vacantes_totales'],
        $_POST['vacantes_disponibles'],
        $_POST['id_aula']
    ]);

    echo json_encode(["ok"=>true]);
break;
}
?>