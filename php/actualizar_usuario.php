<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once '../config/conexion.php';

// Validar y sanitizar datos
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nombre = trim($_POST['nombre'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$rol_id = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 0;
$password = $_POST['password'] ?? '';
$contrasena_confirmada = $_POST['contrasena_confirmada'] ?? '';

$errores = [];

// Validaciones básicas
if ($id <= 0 || empty($nombre) || empty($email) || $rol_id <= 0) {
    $errores[] = "Todos los campos son obligatorios.";
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Correo electrónico no válido.";
}

// Validar contraseña (si aplica)
$cambiar_password = false;
if (!empty($password)) {
    if ($password !== $contrasena_confirmada) {
        $errores[] = "Las contraseñas no coinciden.";
    } else {
        $cambiar_password = true;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }
}

// Validar que el rol exista
try {
    $stmt = $pdo->prepare("SELECT id FROM rol WHERE id = :id");
    $stmt->bindParam(':id', $rol_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $errores[] = "El rol seleccionado no existe.";
    }
} catch (PDOException $e) {
    $errores[] = "Error al verificar el rol: " . $e->getMessage();
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../admin/editar_usuario.php?id=$id");
    exit;
}

// Actualizar en la base de datos
try {
    if ($cambiar_password) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, contrasena = :password, rol_id = :rol_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password_hash,
            ':rol_id' => $rol_id,
            ':id' => $id
        ]);
    } else {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol_id = :rol_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':rol_id' => $rol_id,
            ':id' => $id
        ]);
    }

    $_SESSION['exito'] = "¡Usuario actualizado correctamente!";
    header("Location: ../admin/usuario.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['errores'] = ["Error al actualizar el usuario: " . $e->getMessage()];
    header("Location: ../admin/editar_usuario.php?id=$id");
    exit;
}
