<?php
session_start();
include_once("../config/conexion.php");

// Datos para log
$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Universidades";
$registro_id = null;
$resultado = "EXITO";
$descripcion = "";

// Función para registrar logs
function registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado) {
    $stmt_log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que los campos no estén vacíos
    if (empty($_POST['nombre']) || empty($_POST['ciudad_id'])) {
        $_SESSION['errores'][] = "Todos los campos son obligatorios.";
        header("Location: ../admin/registrar_universidad.php");
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $ciudad_id = (int) $_POST['ciudad_id'];

    // Validar longitud del nombre
    if (strlen($nombre) > 100) {
        $_SESSION['errores'][] = "El nombre de la universidad no puede exceder los 100 caracteres.";
        header("Location: ../admin/registrar_universidad.php");
        exit;
    }

    try {
        $query = "INSERT INTO universidades (nombre, ciudad_id) VALUES (:nombre, :ciudad_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':ciudad_id', $ciudad_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $registro_id = $pdo->lastInsertId();
            $_SESSION['success'] = "Universidad registrada con éxito.";

            // Registrar log
            $descripcion = "Universidad registrada: ID $registro_id, Nombre: $nombre, Ciudad ID: $ciudad_id";
            registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado);

            header("Location: ../admin/universidades.php");
            exit;
        } else {
            $resultado = "ERROR";
            $_SESSION['errores'][] = "Hubo un problema al registrar la universidad.";

            // Registrar log de error
            $descripcion = "Error al registrar universidad: Nombre: $nombre, Ciudad ID: $ciudad_id";
            registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado);

            header("Location: ../admin/registrar_universidad.php");
            exit;
        }
    } catch (PDOException $e) {
        $resultado = "ERROR";
        $_SESSION['errores'][] = "Error en la base de datos: " . $e->getMessage();

        // Registrar log de excepción
        $descripcion = "Excepción al registrar universidad: " . $e->getMessage();
        registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado);

        header("Location: ../admin/registrar_universidad.php");
        exit;
    }
}
?>