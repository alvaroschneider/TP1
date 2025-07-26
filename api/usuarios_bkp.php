<?php
$conexion = new mysqli("localhost", "adminphp", "adminphp", "fotomascotas");
//$conexion = new mysqli("localhost", "phpmyadmin", "sole0504", "fotomascotas");
$consulta = $conexion->query("SELECT nombre, usuario FROM usuarios");
$usuarios = [];
while ($fila = $consulta->fetch_assoc()) {
    $usuarios[] = $fila;
}
header('Content-Type: application/json');
echo json_encode($usuarios);
?>