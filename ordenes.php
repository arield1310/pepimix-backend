<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Conexión a PostgreSQL
$host = getenv("PGHOST");
$db   = getenv("PGDATABASE");
$user = getenv("PGUSER");
$pass = getenv("PGPASSWORD");
$port = getenv("PGPORT");

$dsn = "pgsql:host=$host;port=$port;dbname=$db;";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Leer datos enviados desde frontend
$data = json_decode(file_get_contents("php://input"));

if (!$data) {
  echo json_encode(["success" => false, "error" => "No data received"]);
  exit;
}

if (isset($data->id) && !empty($data->id)) {
    // 🔹 Actualizar orden existente
    $stmt = $pdo->prepare("UPDATE ordenes 
        SET nombre=?, telefono=?, grado_seccion=?, tipo=?, extras=?, cantidad=? 
        WHERE id=?");
    $stmt->execute([
        $data->nombre,
        $data->telefono,
        $data->grado_seccion,
        $data->tipo,
        $data->extras,
        $data->cantidad,
        $data->id
    ]);

    echo json_encode(["success" => true, "id" => $data->id]);

} else {
    // 🔹 Insertar nueva orden
    $stmt = $pdo->prepare("INSERT INTO ordenes (nombre, telefono, grado_seccion, tipo, extras, cantidad) 
        VALUES (?, ?, ?, ?, ?) RETURNING id");
    $stmt->execute([
        $data->nombre,
        $data->telefono,
        $data->grado_seccion,
        $data->tipo,
        $data->extras,
        $data->cantidad
    ]);
    $id = $stmt->fetchColumn();

    echo json_encode(["success" => true, "id" => $id]);
}
?>
