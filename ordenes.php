<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "error" => "No data received"]);
    exit;
}

$nombre = $input['nombre'] ?? '';
$telefono = $input['telefono'] ?? '';
$tipo = $input['tipo'] ?? '';
$extras = $input['extras'] ?? '';
$cantidad = $input['cantidad'] ?? 1;

// Render envía las credenciales como variables de entorno
$host = getenv("PGHOST");
$db   = getenv("PGDATABASE");
$user = getenv("PGUSER");
$pass = getenv("PGPASSWORD");
$port = getenv("PGPORT");

$conn = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");

if (!$conn) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

$result = pg_query_params($conn,
    "INSERT INTO ordenes (nombre, telefono, tipo, extras, cantidad) 
     VALUES ($1, $2, $3, $4, $5) RETURNING id",
    [$nombre, $telefono, $tipo, $extras, $cantidad]
);

if ($result) {
    $row = pg_fetch_assoc($result);
    echo json_encode(["success" => true, "id" => $row['id']]);
} else {
    echo json_encode(["success" => false, "error" => pg_last_error($conn)]);
}

pg_close($conn);
?>
