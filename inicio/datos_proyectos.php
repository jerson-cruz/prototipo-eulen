<?php
include("conexion.php");

$tabla = $_GET['tabla'] ?? '';
$tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla); // seguridad básica

// ✅ Verifica si la tabla existe en la base de datos
$checkTabla = $conexion->query("SHOW TABLES LIKE '$tabla'");
if ($checkTabla->num_rows === 0) {
    echo json_encode(["error" => "La tabla '$tabla' no existe en la base de datos."]);
    exit;
}

$sql = "
    SELECT 
        responsable,
        tarea_padre,
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'Completada' THEN 1 ELSE 0 END) AS completadas,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
        SUM(CASE 
            WHEN estado NOT IN ('Completada', 'Pendiente') THEN 1 
            ELSE 0 END
        ) AS vencidas
    FROM $tabla
    GROUP BY responsable, tarea_padre
    ORDER BY total DESC
";

$resultado = $conexion->query($sql);

$data = [];

$meses = [
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre'
];

while ($row = $resultado->fetch_assoc()) {
    $mes = 'Desconocido';
    $tareaPadre = strtolower($row['tarea_padre']);

    foreach ($meses as $nombreMes) {
        if (strpos($tareaPadre, strtolower($nombreMes)) !== false) {
            $mes = ucfirst($nombreMes);
            break;
        }
    }

    $data[] = [
        'responsable' => $row['responsable'],
        'mes' => $mes,
        'total' => (int)$row['total'],
        'completadas' => (int)$row['completadas'],
        'pendientes' => (int)$row['pendientes'],
        'vencidas' => (int)$row['vencidas']
    ];
}

echo json_encode($data);
