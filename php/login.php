<?php 
include '../config/conexion.php';
session_start();

$email = trim($_POST['email'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
$accion = "LOGIN";
$modulo = "Usuarios";
$registro_id = null;
$descripcion = "";
$resultado = "ERROR";

if (!empty($email) && !empty($contrasena)) {
    try {
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

            // ✅ LOGIN EXITOSO
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol_nombre'];

            $registro_id = $usuario['id'];
            $descripcion = "Inicio de sesión exitoso.";
            $resultado = "EXITO";

            // Guardar log SOLO si existe usuario
            $stmt_log = $pdo->prepare("
                INSERT INTO log_actividades 
                (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_log->execute([
                $usuario['id'], $accion, $modulo, $registro_id,
                $descripcion, $ip, $navegador, $resultado
            ]);

            // Redirección según rol
            $rol = strtolower($usuario['rol_nombre']);
            if ($rol === 'administrador' || $rol === 'tecnico') {
                header("Location: ../admin/index.php");
                exit;
            }

        } else {

            // ❌ LOGIN FALLIDO

            // SOLO guardar log si el usuario existe
            if ($usuario) {
                $descripcion = "Contraseña incorrecta.";
                
                $stmt_log = $pdo->prepare("
                    INSERT INTO log_actividades 
                    (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt_log->execute([
                    $usuario['id'], $accion, $modulo, $usuario['id'],
                    $descripcion, $ip, $navegador, $resultado
                ]);
            }

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
?>