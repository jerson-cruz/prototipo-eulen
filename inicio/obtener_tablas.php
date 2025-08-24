<?php
include("conexion.php");

$sql = "SHOW TABLES";
$result = $conexion->query($sql); // ✅ usa $conexion

$tablas = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $nombreTabla = $row[0];
        if (strpos($nombreTabla, 'delegacion_') === 0 || strpos($nombreTabla, 'mantenimiento_') === 0) {
            $tablas[] = $nombreTabla;
        }
    }
} else {
    die("No se pudieron obtener las tablas: " . $conexion->error);
}

$tabla = $_POST['tabla'];
