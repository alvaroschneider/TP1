<?php
$conexion = new mysqli("localhost", "adminphp", "adminphp", "fotomascotas");

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

$nombre = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nombre = $_GET['nombre'] ?? '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'])) {
        $nombre = $_POST['nombre'];
    } else {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $nombre = $data['nombre'] ?? '';
    }
}

// Consulta base con JOIN
$sql = "
    SELECT p.nombre AS producto, p.precio, p.stock, c.nombre_categoria
    FROM productos p
    JOIN categorias c ON p.id_categoria = c.id_categoria
";

$parametros = [];
$tipos = "";

// Filtro si viene nombre
if (!empty($nombre)) {
    $sql .= " WHERE p.nombre LIKE ? OR c.nombre_categoria LIKE ?";
    $parametros[] = "%$nombre%";
    $parametros[] = "%$nombre%";
    $tipos = "ss";
}

$stmt = $conexion->prepare($sql);

if (!empty($parametros)) {
    $stmt->bind_param($tipos, ...$parametros);
}

$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
while ($fila = $resultado->fetch_assoc()) {
    $productos[] = $fila;
}

$stmt->close();
$conexion->close();

header('Content-Type: application/json');
echo json_encode($productos);
