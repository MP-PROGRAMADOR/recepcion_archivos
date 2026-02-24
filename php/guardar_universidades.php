<?php 
session_start();
include_once("../config/conexion.php");

// Validar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Datos para log
$usuario_id = $_SESSION['usuario_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Universidades";
$registro_id = null;
$resultado = "EXITO";
$descripcion = "";

// Función para registrar logs
function registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado) {
    if ($usuario_id) { // 🔒 nunca permitir usuario_id null
        $stmt_log = $pdo->prepare("INSERT INTO log_actividades 
            (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Limpiar mensajes anteriores
    unset($_SESSION['exito']);
    unset($_SESSION['error']);

    if (empty($_POST['nombre']) || empty($_POST['ciudad_id'])) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../admin/registrar_universidad.php");
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $ciudad_id = (int) $_POST['ciudad_id'];

    if (strlen($nombre) > 100) {
        $_SESSION['error'] = "El nombre no puede exceder los 100 caracteres.";
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

            // ✅ ALERTA DE ÉXITO (como espera tu frontend)
            $_SESSION['exito'] = "Universidad registrada correctamente.";

            // Registrar log
            $descripcion = "Universidad registrada: ID $registro_id, Nombre: $nombre";
            registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, "EXITO");

            header("Location: ../admin/universidades.php");
            exit;

        } else {

            $_SESSION['error'] = "Hubo un problema al registrar la universidad.";

            $descripcion = "Error al registrar universidad: $nombre";
            registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, "ERROR");

            header("Location: ../admin/registrar_universidad.php");
            exit;
        }

    } catch (PDOException $e) {

        $_SESSION['error'] = "Error en la base de datos.";

        $descripcion = "Excepción al registrar universidad: " . $e->getMessage();
        registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, "ERROR");

        header("Location: ../admin/registrar_universidad.php");
        exit;
    }
}
?>