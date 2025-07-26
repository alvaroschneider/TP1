<?php
$conexion = new mysqli("localhost", "adminphp", "adminphp", "fotomascotas");

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

// Obtener el parámetro 'nombre' por GET o POST
$nombre = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Soporta JSON y application/x-www-form-urlencoded
    if (isset($_POST['nombre'])) {
        $nombre = $_POST['nombre'];
    } else {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (isset($data['nombre'])) {
            $nombre = $data['nombre'];
        }
    }
}

// Preparar la respuesta
$usuarios = [];

if (!empty($nombre)) {
    $stmt = $conexion->prepare("SELECT nombre, usuario FROM usuarios WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    $stmt->close();
} else {
    // Si no se especificó nombre, devolver todos los usuarios
    $consulta = $conexion->query("SELECT nombre, usuario FROM usuarios");
    while ($fila = $consulta->fetch_assoc()) {
        $usuarios[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($usuarios);
?>
