<?php
header('Content-Type: application/json');
require_once 'conexion.php';

try {

    $respuesta = [];

    // ── 1. KPIs BÁSICOS ────────────────────────────────────────

    // Total de Alumnos
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM ALUMNO");
    $respuesta['kpis']['totalAlumnos'] = (int) $stmt->fetchColumn();

    // Total de Aulas y Vacantes Disponibles
    $stmt = $pdo->query("SELECT COUNT(*) AS totalAulas, SUM(VACANTES_DISPONIBLES) AS vacantesDisp FROM AULA");
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    $respuesta['kpis']['totalAulas']   = (int) ($row['totalAulas']   ?? 0);
    $respuesta['kpis']['vacantesDisp'] = (int) ($row['vacantesDisp'] ?? 0);

    // ── 2. GRÁFICO 1: Alumnos por Género (Pie / Dona) ──────────
    $stmt = $pdo->query("SELECT GENERO, COUNT(*) AS cantidad FROM ALUMNO GROUP BY GENERO");
    $respuesta['graficos']['genero'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ── 3. GRÁFICO 2: Vacantes por Nivel Educativo (Barras) ─────
    $stmt = $pdo->query("
        SELECT NIVEL,
               SUM(VACANTES_TOTALES)     AS totales,
               SUM(VACANTES_DISPONIBLES) AS disponibles
        FROM AULA
        GROUP BY NIVEL
    ");
    $respuesta['graficos']['niveles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["exito" => true, "datos" => $respuesta]);

} catch (PDOException $e) {
    echo json_encode([
        "exito"   => false,
        "mensaje" => "Error BD: " . $e->getMessage()
    ]);
}
?>