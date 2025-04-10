<?php
session_start();

// Verificar sesión activa
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/conexion.php';

// Función para sanitizar texto
function limpiar($dato)
{
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// Inicializar errores
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $estudiante_id = isset($_POST['estudiante_id']) ? (int) $_POST['estudiante_id'] : 0;
    $anio_academico_id = isset($_POST['anio_academico_id']) ? (int) $_POST['anio_academico_id'] : 0;
    $observaciones = isset($_POST['observaciones']) ? limpiar($_POST['observaciones']) : '';

    // Validar campos básicos
    if ($estudiante_id <= 0)
        $errores[] = "ID de estudiante no válido.";
    if ($anio_academico_id <= 0)
        $errores[] = "Debe seleccionar un año académico válido.";

    // Verificar archivo
    if (!isset($_FILES['archivo_url']) || $_FILES['archivo_url']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debe subir un archivo PDF válido.";
    } else {
        $archivo = $_FILES['archivo_url'];
        $tipoMime = mime_content_type($archivo['tmp_name']);

        if ($tipoMime !== 'application/pdf') {
            $errores[] = "Solo se permiten archivos PDF.";
        }

        if ($archivo['size'] > 5 * 1024 * 1024) {
            $errores[] = "El archivo excede el tamaño máximo permitido de 5MB.";
        }
    }

    // Continuar si no hay errores
    if (empty($errores)) {
        try {
            // Obtener código de acceso del estudiante desde sesión
            $codigoAcceso = $_SESSION['estudiante']['codigo_acceso'];
            // Obtener años académicos
            
            $stmtAnios = $pdo->prepare("SELECT nombre FROM anios_academicos WHERE id = ?");
            $stmtAnios->execute([$anio_academico_id]);
            $anios = $stmtAnios->fetch(PDO::FETCH_ASSOC);


            // Generar nombre único con código de acceso
            //  $nombreArchivo = $codigoAcceso . '_' . uniqid('nota_', true) . '.pdf';
            $nombreArchivo = 'Notas_'.$anios['nombre'].'_'. $codigoAcceso .'.pdf';

            // Ruta destino
            $directorio = '../upload/pdf/';
            $rutaCompleta = $directorio . $nombreArchivo;

            // Crear carpeta si no existe
            if (!is_dir($directorio)) {
                if (!mkdir($directorio, 0755, true)) {
                    throw new Exception("No se pudo crear la carpeta de destino.");
                }
            }

            // Iniciar transacción
            $pdo->beginTransaction();

            // Insertar nota
            $stmt = $pdo->prepare("INSERT INTO notas (estudiante_id, anio_academico_id, observaciones, archivo_url, fecha_subida)
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $estudiante_id,
                $anio_academico_id,
                $observaciones,
                $nombreArchivo
            ]);

            // Mover archivo
            if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                $pdo->rollBack();
                throw new Exception("Error al guardar el archivo PDF.");
            }

            $pdo->commit();

            $_SESSION['mensaje_exito'] = "Nota registrada correctamente.";
            header("Location: ../estudiante/panel_estudiante.php");
            exit();

        } catch (Exception $e) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        // Mostrar errores
        echo "<div class='alert alert-danger'><strong>Errores encontrados:</strong><ul>";
        foreach ($errores as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    }
} else {
    echo "<div class='alert alert-warning'>Método no permitido.</div>";
}
?>