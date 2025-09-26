<?php
header("Content-Type: application/json");

// Variables de entorno proporcionadas en Render
$host = getenv("PGHOST");
$db   = getenv("PGDATABASE");
$user = getenv("PGUSER");
$pass = getenv("PGPASSWORD");
$port = getenv("PGPORT") ?: 5432;

// Conexión a PostgreSQL
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "No se pudo conectar a la base de datos"]);
    exit;
}

// Leer datos enviados en JSON
$data = json_decode(file_get_contents("php://input"), true);

$nombre   = $data["nombre"]   ?? "";
$telefono = $data["telefono"] ?? "";
$tipo     = $data["tipo"]     ?? "";
$extras   = $data["extras"]   ?? "";
$cantidad = $data["cantidad"] ?? 1;

// Insertar en la tabla "ordenes"
$result = pg_query_params(
    $conn,
    "INSERT INTO ordenes (nombre, telefono, tipo, extras, cantidad) VALUES ($1, $2, $3, $4, $5) RETURNING id",
    [$nombre, $telefono, $tipo, $extras, $cantidad]
);

if ($row = pg_fetch_assoc($result)) {
    echo json_encode(["success" => true, "id" => $row["id"]]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo guardar la orden"]);
}
