<?php
session_start();
include_once("../config/conexion.php");
header('Content-Type: application/json');

// Verifica que el parámetro pais_id esté presente en la URL
if (isset($_GET['pais_id'])) {
    $paisId = $_GET['pais_id'];

    // Escribe el valor de pais_id en el log del servidor para depuración
    error_log("ID del país recibido: " . $paisId);  // Esto debería aparecer en el log de errores del servidor

    try {
        // Consulta las ciudades asociadas al pais_id
        $stmt = $pdo->prepare("SELECT id, nombre FROM ciudades WHERE pais_id = :pais_id ORDER BY nombre ASC");
        $stmt->bindParam(':pais_id', $paisId, PDO::PARAM_INT);
        $stmt->execute();

        // Obtén todas las ciudades en formato JSON
        $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no se encontraron ciudades, devuelve un error
        if (empty($ciudades)) {
            // Almacena el error en la sesión para que el frontend lo muestre
            $_SESSION['errores'][] = 'No se encontraron ciudades para el país indicado.';
            // Enviar la respuesta JSON
            echo json_encode(['error' => 'No se encontraron ciudades para el país indicado.']);
        } else {
            // Devuelve las ciudades en formato JSON con la propiedad 'ciudades'
            echo json_encode(['ciudades' => $ciudades]);
        }
    } catch (PDOException $e) {
        // Almacena el error en la sesión para que el frontend lo muestre
        $_SESSION['errores'][] = 'Error al obtener las ciudades: ' . $e->getMessage();
        // Enviar la respuesta JSON
        echo json_encode(['error' => 'Error al obtener las ciudades.']);
    }
} else {
    // Si no se recibe el parámetro pais_id
    $_SESSION['errores'][] = 'No se ha proporcionado un país.';
    // Enviar la respuesta JSON
    echo json_encode(['error' => 'No se ha proporcionado un país.']);
}
?>
