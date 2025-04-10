<?php
// Conexión a la base de datos
include_once("config/conexion.php");

// FUNCIONES AUXILIARES
function limpiar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// VALIDACIÓN DE ENVÍO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Capturar y limpiar datos
    $nombre     = limpiar($_POST["nombre"] ?? '');
    $correo     = limpiar($_POST["correo"] ?? ''); 
    $contrasena = $_POST["contrasena"] ?? ''; 

    // 2. Validar que no estén vacíos
    if (empty($nombre) || empty($correo)  || empty($contrasena)  ) {
        die("Error: Todos los campos son obligatorios.");
    }

    // 3. Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Error: El correo electrónico no es válido.");
    }

    // 4. Validar longitud mínima de contraseña
    if (strlen($contrasena) < 6) {
        die("Error: La contraseña debe tener al menos 6 caracteres.");
    }

 
 

    // 7. Encriptar la contraseña
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // 8. Insertar el usuario en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email,  contrasena, creado_en) 
                               VALUES (:nombre, :email,  :contrasena, NOW())");

        $stmt->execute([
            ":nombre"     => $nombre,
            ":email"     => $correo, 
            ":contrasena" => $hash, 
        ]);

        echo "Usuario registrado correctamente.";

    } catch (PDOException $e) {
        die("Error al guardar el usuario: " . $e->getMessage());
    }

} else {
    die("Acceso no permitido.");
}
?>
