<?php
include("conexion.php");

$sql = "SHOW TABLES";
$result = $conexion->query($sql);
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Tabla</title>
</head>

<body>
    <form method="POST" action="#">
        <label for="tabla">Selecciona una tabla:</label>
        <select name="tabla" id="tabla" required>
            <option value="">-- Elige una tabla --</option>
            <?php foreach ($tablas as $tabla): ?>
                <option value="<?= $tabla ?>"><?= $tabla ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</body>

</html>