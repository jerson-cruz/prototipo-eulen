<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "prototipo_eulen";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// ===== 1. Recibir datos =====
$zona        = $_POST['id_combo_1_id_entidad_asociada_chosen'] ?? '';
$fecha       = $_POST['fechalimite'] ?? '';
$tipoReporte = $_POST['idtt_498_idc_34'] ?? '';
$cliente     = $_POST['idtt_498_idc_31'] ?? '';
$sede        = $_POST['idtt_498_idc_30'] ?? '';
$tipoServicio= $_POST['idtt_498_idc_36'] ?? '';
$nombreCargo = $_POST['idtt_498_idc_33'] ?? '';
$descripcion = $_POST['idtt_498_idc_10'] ?? '';
$correo      = $_POST['idtt_498_idc_23'] ?? '';

// ===== 2. Debug: Verifica lo que llega =====
if (empty($_POST)) {
    die("No llegaron datos desde el formulario original.");
}

// ===== 3. Guardar en tu BD =====
$sql = "INSERT INTO formulario 
        (zona, fecha, tipo_reporte, cliente, sede, tipo_servicio, nombre_cargo, descripcion, correo) 
        VALUES ('$zona', '$fecha', '$tipoReporte', '$cliente', '$sede', '$tipoServicio', '$nombreCargo', '$descripcion', '$correo')";

if ($conexion->query($sql) === TRUE) {
    echo "Guardado en la base local.<br>";
} else {
    die("Error al guardar en BD: " . $conexion->error . "<br>");
}
$conexion->close();
?>

<!-- ===== 4. Autosubmit hacia ISOTools ===== -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Redirigiendo...</title>
</head>
<body>
  <p>Redirigiendo al formulario oficial de ISOTools...</p>

  <form id="contenedor_total" 
        action="https://grupoeulen.esginnova.com/wip/wip2015/registro.cfm?token=MEMwNUM2RkRCRjI3QzJGRUZGMEZFRUJGRjNERDc5NUIxNUNEOERCQTgwOTQ0MUFGOTY5ODlCQTAzODAyNkU1MTE4NTBBQzU1N0VGNjlFQ0E=" 
        method="POST">
    <input type="hidden" name="id_combo_1_id_entidad_asociada_chosen" value="<?= htmlspecialchars($zona) ?>">
    <input type="hidden" name="fechalimite" value="<?= htmlspecialchars($fecha) ?>">
    <input type="hidden" name="idtt_498_idc_34" value="<?= htmlspecialchars($tipoReporte) ?>">
    <input type="hidden" name="idtt_498_idc_31" value="<?= htmlspecialchars($cliente) ?>">
    <input type="hidden" name="idtt_498_idc_30" value="<?= htmlspecialchars($sede) ?>">
    <input type="hidden" name="idtt_498_idc_36" value="<?= htmlspecialchars($tipoServicio) ?>">
    <input type="hidden" name="idtt_498_idc_33" value="<?= htmlspecialchars($nombreCargo) ?>">
    <input type="hidden" name="idtt_498_idc_10" value="<?= htmlspecialchars($descripcion) ?>">
    <input type="hidden" name="idtt_498_idc_23" value="<?= htmlspecialchars($correo) ?>">
  </form>

  <script>
    document.getElementById("contenedor_total").submit();
  </script>
</body>
</html>