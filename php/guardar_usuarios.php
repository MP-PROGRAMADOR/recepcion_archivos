<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

include '../config/conexion.php'; // Archivo de conexión

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Datos para log
$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "CREAR";
$modulo = "Usuarios";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitize_input($_POST['nombre']);
    $email = strtolower(sanitize_input($_POST['email']));
    $contrasena = $_POST['password'];
    $contrasena_confirmada = $_POST['contrasena_confirmada'];
    $rol_id = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0;

    $errores = [];

    // Validación de campos obligatorios
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($contrasena_confirmada) || $rol_id <= 0) {
        $errores[] = 'Todos los campos son obligatorios, incluyendo el rol.';
    }

    // Validación de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del correo electrónico es inválido.';
    }

    // Contraseñas deben coincidir
    if ($contrasena !== $contrasena_confirmada) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // Validar que el rol exista
    if ($rol_id > 0) {
        try {
            $stmt_rol = $pdo->prepare("SELECT id FROM rol WHERE id = :id");
            $stmt_rol->bindParam(':id', $rol_id, PDO::PARAM_INT);
            $stmt_rol->execute();
            if ($stmt_rol->rowCount() === 0) {
                $errores[] = 'El rol seleccionado no existe.';
            }
        } catch (PDOException $e) {
            $errores[] = 'Error al verificar el rol: ' . $e->getMessage();
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $resultado = "ERROR";
        $descripcion = "Errores al validar formulario: " . implode('; ', $errores);
        registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado);
        header('Location: ../admin/registrar_usuario.php');
        exit;
    }

    try {
        // Verificar si el email ya está registrado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['errores'] = ['El correo electrónico ya está registrado.'];
            $resultado = "ERROR";
            $descripcion = "Intento de registrar usuario con email duplicado: $email";
            registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado);
            header('Location: ../admin/registrar_usuario.php');
            exit;
        }

        // Hashear la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar usuario con rol
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena, rol_id) VALUES (:nombre, :email, :contrasena, :rol_id)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
        $stmt->execute();

        $registro_id = $pdo->lastInsertId();
        $_SESSION['exito'] = 'Usuario registrado exitosamente.';

        // Registrar log de éxito
        $descripcion = "Usuario registrado: ID $registro_id, Nombre: $nombre, Email: $email, Rol ID: $rol_id";
        registrar_log($pdo, $usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado);

        header('Location: ../admin/usuario.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['errores'] = ['Error al registrar el usuario: ' . $e->getMessage()];
        $resultado = "ERROR";
        $descripcion = "Excepción al registrar usuario: " . $e->getMessage();
        registrar_log($pdo, $usuario_id, $accion, $modulo, null, $descripcion, $ip, $navegador, $resultado);
        header('Location: ../admin/registrar_usuario.php');
        exit;
    }
}
?>