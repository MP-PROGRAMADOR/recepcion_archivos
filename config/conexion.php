<?php
// Configuración de la base de datos
$host = 'localhost'; // o la IP del servidor
$dbname = 'recepcion_archivo'; // nombre de la base de datos
$username = 'root'; // tu nombre de usuario de la base de datos
$password = ''; // tu contraseña de la base de datos

// Establecer opciones de conexión PDO para mejorar la seguridad
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en caso de error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Usar modo asociativo para recuperar resultados
    PDO::ATTR_EMULATE_PREPARES => false, // Deshabilitar emulación de consultas preparadas
);

try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, $options);
    // Si la conexión es exitosa, se ejecutará este bloque
    // echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje de error
    echo "Error al conectar: " . $e->getMessage();
}
?>
