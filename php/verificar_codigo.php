<?php
require_once '../config/conexion.php';
session_start(); // Necesario para usar $_SESSION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE codigo_acceso = ?");
        $stmt->execute([$codigo]);
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($estudiante) {
            $_SESSION['estudiante'] = $estudiante;
            $_SESSION['id'] = $estudiante['id'];
            $_SESSION['exito'] = '¡Bienvenido, ' . htmlspecialchars($estudiante['nombre']) . '!';
            header("Location: ../estudiante/panel_estudiante.php");
            exit();
        } else {
            $_SESSION['errores'] = ['El código de acceso es inválido.'];
            header("Location: ../estudiante/index.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['errores'] = ['Ocurrió un error al procesar la solicitud: ' . $e->getMessage()];
        header("Location: ../estudiante/index.php");
        exit();
    }
}
?>
