<?php
session_start();
include_once('../config/conexion.php'); // Conexión PDO

$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "LOGOUT";
$modulo = "Sesión";
$registro_id = null;
$descripcion = "El usuario cerró sesión.";
$resultado = "EXITO";

// Registrar log de logout si hay usuario
if ($usuario_id) {
    $stmt_log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
}

// Destruir la sesión
session_destroy();

// Redirigir a la página principal
header('Location: ../index.php');
exit;
?>