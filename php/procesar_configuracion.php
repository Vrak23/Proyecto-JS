<?php
// Configuración de cabeceras para JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');

$opcion = $_POST['opcion'] ?? '';

// DATOS ESTÁTICOS PARA MODO VISUAL
$configuracionesFicticias = [
    [
        "ID_CONFIGURACION" => 1,
        "CLAVE" => "institucion_nombre",
        "VALOR" => "SENATI - CFP Independencia",
        "DESCRIPCION" => "Nombre oficial para documentos y reportes."
    ],
    [
        "ID_CONFIGURACION" => 2,
        "CLAVE" => "periodo_lectivo",
        "VALOR" => "2026-I",
        "DESCRIPCION" => "Ciclo académico vigente para matrículas."
    ],
    [
        "ID_CONFIGURACION" => 3,
        "CLAVE" => "max_alumnos_aula",
        "VALOR" => "30",
        "DESCRIPCION" => "Límite predeterminado de alumnos por aula."
    ],
    [
        "ID_CONFIGURACION" => 4,
        "CLAVE" => "monto_matricula",
        "VALOR" => "S/. 250.00",
        "DESCRIPCION" => "Costo del derecho de matrícula del ciclo."
    ]
];

try {
    switch ($opcion) {
        // ── LISTAR (OPCIÓN 4) ─────────────────────────────
        case '4':
            // Devuelve el array de datos estáticos
            echo json_encode($configuracionesFicticias);
            break;

        // ── OBTENER PARA EDITAR (OPCIÓN 2) ────────────────
        case '2':
            $id = $_POST['id_configuracion'] ?? 0;
            // Busca el elemento en el array estático
            $encontrado = null;
            foreach ($configuracionesFicticias as $c) {
                if ($c['ID_CONFIGURACION'] == $id) {
                    $encontrado = $c;
                    break;
                }
            }
            echo $encontrado 
                ? json_encode($encontrado) 
                : json_encode(["exito" => false, "mensaje" => "No encontrado"]);
            break;

        // ── SIMULACIÓN DE INSERT / DELETE / UPDATE ────────
        case '1':
        case '3':
        case '5':
            echo json_encode(["exito" => true, "mensaje" => "Simulación exitosa (Modo Visual)"]);
            break;

        default:
            echo json_encode(["exito" => false, "mensaje" => "Opción no válida"]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(["exito" => false, "mensaje" => $e->getMessage()]);
}
?>