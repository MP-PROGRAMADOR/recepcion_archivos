<?php
include '../config/conexion.php'; // Archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];

    if (!empty($email) && !empty($contrasena)) {
        try {
            // Consulta con JOIN para obtener el nombre del rol
            $stmt = $pdo->prepare("
                SELECT u.*, r.nombre AS rol_nombre 
                FROM usuarios u 
                INNER JOIN rol r ON u.rol_id = r.id 
                WHERE u.email = :email
            ");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                // Login exitoso
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_rol'] = $usuario['rol_nombre']; // Guardamos el nombre del rol

                // Puedes hacer redirecciones según el rol si deseas:
                 if ( (strtolower($usuario['rol_nombre']) === 'administrador') || ( strtolower($usuario['rol_nombre']) === 'tecnico')) { 
                     header("Location: ../admin/index.php");
                     exit;
                    
                  } 

            } else {
                $_SESSION['error'] = 'Correo o contraseña incorrectos.';
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
