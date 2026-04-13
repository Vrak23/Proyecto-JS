<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$op = $_POST['opcion'] ?? '';

switch ($op) {

case '1': // INSERT
    $stmt = $pdo->prepare("INSERT INTO CURSO (NOMBRE, DESCRIPCION, CREDITOS)
    VALUES (?, ?, ?)");

    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['creditos']
    ]);

    echo json_encode(["ok"=>true]);
break;

case '2': // GET
    $stmt = $pdo->prepare("SELECT * FROM CURSO WHERE ID_CURSO=?");
    $stmt->execute([$_POST['id_curso']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
break;

case '3': // DELETE
    $stmt = $pdo->prepare("DELETE FROM CURSO WHERE ID_CURSO=?");
    $stmt->execute([$_POST['id_curso']]);
    echo json_encode(["ok"=>true]);
break;

case '4': // LIST
    $stmt = $pdo->query("SELECT * FROM CURSO");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
break;

case '5': // UPDATE
    $stmt = $pdo->prepare("UPDATE CURSO SET
        NOMBRE=?, DESCRIPCION=?, CREDITOS=?
        WHERE ID_CURSO=?");

    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['creditos'],
        $_POST['id_curso']
    ]);

    echo json_encode(["ok"=>true]);
break;
}
?>