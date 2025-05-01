
<?php
session_start();
require_once '../config/conexion.php'; // Asegúrate de que aquí se crea la conexión $pdo

// Verificar si los datos vienen del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id_estudiante = $_POST['id'];
    $idioma_id = $_POST['idioma_id'];
    $meses_duracion = $_POST['meses_duracion'];

    // Validar que no estén vacíos
    if (!empty($id_estudiante) && !empty($idioma_id) && !empty($meses_duracion)) {
        try {
            // Preparar la consulta SQL
            $sql = "UPDATE estudiantes SET idioma_id = :idioma_id, meses_idioma = :meses_duracion WHERE id = :id_estudiante";
            $stmt = $pdo->prepare($sql);

            // Asignar valores a los parámetros
            $stmt->bindParam(':idioma_id', $idioma_id, PDO::PARAM_INT);
            $stmt->bindParam(':meses_duracion', $meses_duracion, PDO::PARAM_INT);
            $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                header('Location: ../estudiante/panel_estudiante.php'); // Cambia esta ruta por la adecuada
                exit;
            } else {
                header('Location: ../estudiante/panel_estudiante.php'); // Cambia esta ruta por la adecuada
                exit;
            }
        } catch (PDOException $e) {
            echo "Error en la actualización: " . $e->getMessage();
        }
    } else {
        header('Location: ../estudiante/panel_estudiante.php'); // Cambia esta ruta por la adecuada
        exit;
    }
}
?>
