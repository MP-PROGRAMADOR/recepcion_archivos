<?php
session_start();
require_once '../config/conexion.php';

// Validar y sanitizar datos
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$contrasena_confirmada = $_POST['contrasena_confirmada'] ?? '';

// Validaciones básicas
if ($id <= 0 || empty($nombre) || empty($email)) {
    $_SESSION['error'] = "Datos inválidos para actualizar el usuario.";
    header("Location: ../admin/editar_usuario.php?id=$id");
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Correo electrónico no válido.";
    header("Location: ../admin/editar_usuario.php?id=$id");
    exit;
}

// Validar contraseña (si aplica)
$cambiar_password = false;
if (!empty($password)) {
    if ($password !== $contrasena_confirmada) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: ../admin/editar_usuario.php?id=$id");
        exit;
    }
    $cambiar_password = true;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
}

// Actualizar en la base de datos
try {
    if ($cambiar_password) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $password_hash,
            ':id' => $id
        ]);
    } else {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':id' => $id
        ]);
    }

    $_SESSION['exito'] = "¡Usuario actualizado correctamente!";
    header("Location: ../admin/usuario.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar el usuario: " . $e->getMessage();
    header("Location: ../admin/editar_usuario.php?id=$id");
    exit;
}
