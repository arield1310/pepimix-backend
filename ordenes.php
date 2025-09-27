<?php
// 🔹 Permitir CORS (necesario para que el frontend en Netlify pueda conectarse)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// 🔹 Manejar preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔹 Variables de entorno de Render
$host = getenv("PGHOST");
$db   = getenv("PGDATABASE");
$user = getenv("PGUSER");
$pass = getenv("PGPASSWORD");
$port = getenv("PGPORT") ?: 5432;

// 🔹 Conectar a PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "❌ No se pudo conectar a la base de datos"]);
    exit;
}

// 🔹 Leer datos enviados en JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "❌ No se recibió JSON válido", "raw" => $input]);
    exit;
}

$nombre   = $data["nombre"]   ?? "";
$telefono = $data["telefono"] ?? "";
$tipo     = $data["tipo"]     ?? "";
$extras   = $data["extras"]   ?? "";
$cantidad = $data["cantidad"] ?? 1;

// 🔹 Insertar en la tabla
$result = pg_query_params(
    $conn,
    "INSERT INTO ordenes (nombre, telefono, tipo, extras, cantidad) VALUES ($1, $2, $3, $4, $5) RETURNING id",
    [$nombre, $telefono, $tipo, $extras, $cantidad]
);

if ($row = pg_fetch_assoc($result)) {
    echo json_encode([
        "success" => true,
        "id" => $row["id"],
        "nombre" => $nombre,
        "telefono" => $telefono,
        "tipo" => $tipo,
        "extras" => $extras,
        "cantidad" => $cantidad
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "❌ No se pudo guardar la orden"]);
}
