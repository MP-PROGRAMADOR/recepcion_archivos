<?php
session_start();
include '../config/conexion.php'; // Archivo de conexión

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitize_input($_POST['nombre']);
    $email = sanitize_input($_POST['email']);
    $contrasena = $_POST['password'];
    $contrasena_confirmada = $_POST['contrasena_confirmada'];

    // Validación de campos obligatorios
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($contrasena_confirmada)) {
        $_SESSION['error'] = 'Todos los campos son obligatorios.';
        header('Location: ../admin/registrar_usuario.php');
        exit;
    }

    // Validación de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El formato del correo electrónico es inválido.';
        header('Location: ../admin/registrar_usuario.php');
        exit;
    }

    // Contraseñas deben coincidir
    if ($contrasena !== $contrasena_confirmada) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: ../admin/registrar_usuario.php');
        exit;
    }

    try {
        // Verificar si el email ya está registrado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = 'El correo electrónico ya está registrado.';
            header('Location: ../admin/registrar_usuario.php');
            exit;
        }

        // Hashear la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->execute();

        $_SESSION['exito'] = 'Usuario registrado exitosamente.';
        header('Location: ../admin/usuario.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al registrar el usuario: ' . $e->getMessage();
        header('Location: ../admin/usuarios.php');
        exit;
    }
}
?>
