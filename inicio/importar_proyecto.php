<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'], $_POST['delegacion'])) {
    $archivo = $_FILES['archivo']['tmp_name'];
    $tabla = $_POST['delegacion'];

    // Validar que sea una tabla permitida
    $tablasPermitidas = [
        'delegacion_antioquia',
        'delegacion_centro',
        'delegacion_norte',
        'delegacion_occidente',
        'mantenimiento_nacional'
    ];

    if (!in_array($tabla, $tablasPermitidas)) {
        die("Tabla no permitida.");
    }

    $stmt = $conexion->prepare("INSERT INTO $tabla (nombre, responsable, fecha_inicio, fecha_fin, estado, tarea_padre, fecha_finalizacion) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    if (($handle = fopen($archivo, "r")) !== false) {
        fgetcsv($handle); // Saltar encabezados

        while (($fila = fgetcsv($handle, 1000, ",")) !== false) {
            $nombre = $fila[0] ?? '';
            $responsable = $fila[1] ?? '';
            $fecha_inicio = !empty($fila[2]) ? date('YYYY-MM-d', strtotime($fila[2])) : null;
            $fecha_fin = !empty($fila[3]) ? date('YYYY-MM-d', strtotime($fila[3])) : null;
            $estado = $fila[4] ?? '';
            $tarea_padre = $fila[5] ?? '';
            $fecha_finalizacion = !empty($fila[6]) ? date('YYYY-MM-d', strtotime($fila[6])) : null;

            $stmt->bind_param("sssssss", $nombre, $responsable, $fecha_inicio, $fecha_fin, $estado, $tarea_padre, $fecha_finalizacion);
            if (!$stmt->execute()) {
                echo "Error al insertar fila: " . $stmt->error;
            }
        }

        fclose($handle);
        echo "Importación exitosa a la tabla '$tabla'.";
    } else {
        echo "No se pudo abrir el archivo.";
    }
}
