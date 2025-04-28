<?php
session_start();
include '../config/conexion.php'; // Archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];

    if (!empty($email) && !empty($contrasena)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                // Login exitoso
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // Verificar si tiene foto de perfil
                if (!empty($usuario['foto_perfil'])) {
                    // Si tiene foto de perfil, redirigir a la página principal
                    header("Location: ../admin/panel_estudiante.php");
                } else {
                    // Si no tiene foto de perfil, redirigir a la página de perfil
                    header("Location: ../estudiante/perfil.php");
                }
                exit;
            } else {
                $_SESSION['error'] = 'Error de Email o Contraseña incorrectos.';
                header('Location: ../index.php');
                exit;
            }
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}
?>

