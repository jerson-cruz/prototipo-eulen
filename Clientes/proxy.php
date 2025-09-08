<?php
// Credenciales (no se exponen en el frontend)
$user = "Integracion_Radar_Colombia";
$pass = "Integracion_Radar_Colombia2025*";
$apikey = "eQyImjpWFb8SWHi1Ad2w168J7MIrNZ";
$idPlantilla = "4194";

// Tipo de API que pide el frontend
$tipo = $_GET['tipo'] ?? 'diagnosticos';

// Rutas disponibles
$endpoints = [
    "diagnosticos" => "ejecuciones/diagnosticos",
    "ejecuciones" => "ejecuciones",
    "preguntas" => "ejecuciones/preguntas",
    "entidades" => "ejecuciones/entidades",
    "cumplimiento" => "ejecuciones/obtenercumplimientodiagnostico",
    "expedientes" => "ejecuciones/expedientes",
    "campos" => "ejecuciones/campos_personalizados"
];

// Validar que el tipo exista
if (!isset($endpoints[$tipo])) {
    http_response_code(400);
    echo json_encode(["error" => "Tipo de API no válido"]);
    exit;
}

// Construir URL dinámica
$url = "https://grupoeulen.esginnova.com/rest/api/v2/radar/" .
    $endpoints[$tipo] .
    "?user=$user&password=$pass&apikey=$apikey&id_plantilla=$idPlantilla";

// Llamar con cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Responder
header('Content-Type: application/json');
http_response_code($httpcode);
echo $response;
