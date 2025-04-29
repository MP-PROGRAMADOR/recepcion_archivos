<?php
session_start();
include_once("../config/conexion.php");
header('Content-Type: application/json');

// Verifica que el parámetro ciudad_id esté presente en la URL
if (isset($_GET['ciudad_id'])) {
    $ciudadId = $_GET['ciudad_id'];

    // Escribe el valor de ciudad_id en el log del servidor para depuración
    error_log("ID del ciudad recibido: " . $ciudadId);  // Esto debería aparecer en el log de errores del servidor

    try {
        // Consulta las universidades asociadas al ciudad_id
        $stmt = $pdo->prepare("SELECT id, nombre FROM universidades WHERE ciudad_id = :ciudad_id ORDER BY nombre ASC");
        $stmt->bindParam(':ciudad_id', $ciudadId, PDO::PARAM_INT);
        $stmt->execute();
        $universidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no se encontraron universidades, devuelve un error
        if (empty($universidades)) {
            // Almacena el error en la sesión para que el frontend lo muestre
            $_SESSION['errores'][] = 'No se encontraron universidades para la ciudad indicada.';
            // Enviar la respuesta JSON
            echo json_encode(['error' => 'No se encontraron universidades para la ciudad indicada.']);
        } else {
            // Devuelve las universidades en formato JSON con la propiedad 'universidades'
            echo json_encode(['universidades' => $universidades]);
        }
    } catch (PDOException $e) {
        // Almacena el error en la sesión para que el frontend lo muestre
        $_SESSION['errores'][] = 'Error al obtener las universidades: ' . $e->getMessage();
        // Enviar la respuesta JSON
        echo json_encode(['error' => 'Error al obtener las universidades.']);
    }
} else {
    // Si no se recibe el parámetro ciudad_id
    $_SESSION['errores'][] = 'No se ha proporcionado una ciudad.';
    // Enviar la respuesta JSON
    echo json_encode(['error' => 'No se ha proporcionado una ciudad.']);
}
?>
