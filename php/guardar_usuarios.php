<?php
// Asumiendo que la conexión PDO ya está establecida y almacenada en la variable $pdo
// Incluir el archivo de conexión
include '../config/conexion.php'; // Ruta al archivo donde tienes la conexión

// Función para sanitizar datos y evitar XSS
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Verificar si las variables del formulario están disponibles
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar las entradas del formulario
    $nombre = sanitize_input($_POST['nombre']);
    $email = sanitize_input($_POST['email']);
    $contrasena = $_POST['password'];
    $contrasena_confirmada = $_POST['contrasena_confirmada'];

    // Validaciones
    if (empty($nombre) || empty($email) || empty($contrasena) || empty($contrasena_confirmada)) {
        echo "<script>Swal.fire('Error', 'Todos los campos son obligatorios.', 'error');</script>";
        exit;
    }

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Swal.fire('Error', 'El formato del correo electrónico es inválido.', 'error');</script>";
        exit;
    }

    // Verificar si las contraseñas coinciden
    if ($contrasena !== $contrasena_confirmada) {
        echo "<script>Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');</script>";
        exit;
    }

    // Verificar si el email ya está registrado en la base de datos
    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>Swal.fire('Error', 'El correo electrónico ya está registrado.', 'error');</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>Swal.fire('Error', 'Error al verificar el email: " . $e->getMessage() . "', 'error');</script>";
        exit;
    }

    // Hashear la contraseña para almacenarla de forma segura
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->execute();

        // Mostrar un mensaje de éxito con SweetAlert2
      

         echo "<script>
         Swal.fire('Éxito', 'Usuario registrado exitosamente.', 'success').then(function() {
             window.location = '../admin/usuarios.php'; // Redirigir después del mensaje de éxito
         });
     </script>";


    } catch (PDOException $e) {
        // Si ocurre un error al guardar
        echo "<script>Swal.fire('Error', 'Error al registrar el usuario: " . $e->getMessage() . "', 'error');</script>";
    }
}
?>
