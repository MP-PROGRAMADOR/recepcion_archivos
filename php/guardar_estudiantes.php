<?php
/*
|--------------------------------------------------------------------------
| Script backend para guardar estudiantes en base de datos
|--------------------------------------------------------------------------
| Este archivo procesa el formulario de estudiantes:
| - Valida y sanitiza datos
| - Verifica que el país exista
| - Verifica la imagen (tipo, tamaño, permisos)
| - Crea la carpeta de imágenes si no existe y otorga permisos
| - Guarda los datos y la ruta relativa de la imagen
*/

require_once '../config/conexion.php'; // Conexión PDO

// Función de limpieza básica
function limpiarTexto($dato) {
    return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar que existan los campos requeridos
    if (
        isset($_POST['nombre_completo'], $_POST['fecha_nacimiento'], $_POST['pais']) &&
        isset($_FILES['foto']) && $_FILES['foto']['error'] === 0
    ) {
        // Sanitizar entradas
        $nombre_completo = limpiarTexto($_POST['nombre_completo']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $pais_id = intval($_POST['pais']);

        // Validar fecha de nacimiento con formato correcto
        if (!DateTime::createFromFormat('Y-m-d', $fecha_nacimiento)) {
            exit("⚠️ Error: Fecha inválida.");
        }

        // Validar existencia del país
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM paises WHERE id = ?");
        $stmt->execute([$pais_id]);
        if ($stmt->fetchColumn() == 0) {
            exit("⚠️ Error: El país seleccionado no existe.");
        }

        /*
        |--------------------------------------------------------------------------
        | Procesar imagen
        |--------------------------------------------------------------------------
        */
        $foto = $_FILES['foto'];
        $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $tamaño_maximo = 2 * 1024 * 1024; // 2MB en bytes

        // Validar tipo de archivo
        if (!in_array($extension, $permitidos)) {
            exit("⚠️ Formato de imagen no permitido (usa JPG, PNG, GIF o WEBP).");
        }

        // Validar tamaño
        if ($foto['size'] > $tamaño_maximo) {
            exit("⚠️ La imagen supera el límite de 2MB.");
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar y preparar carpeta /uploads
        |--------------------------------------------------------------------------
        */
        $carpeta = '../uploads/';
        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0755, true)) {
                exit("❌ Error al crear la carpeta /uploads.");
            }
        }

        // Verificar permisos de escritura
        if (!is_writable($carpeta)) {
            if (!chmod($carpeta, 0755)) {
                exit("❌ La carpeta /uploads no tiene permisos de escritura.");
            }
        }

        // Generar nombre único para la imagen
        $nombre_archivo = uniqid('estudiante_') . '.' . $extension;
        $ruta_web = 'uploads/' . $nombre_archivo; // Ruta relativa para mostrar en HTML
        $ruta_destino = $carpeta . $nombre_archivo; // Ruta física en el servidor

        // Mover archivo al servidor
        if (!move_uploaded_file($foto['tmp_name'], $ruta_destino)) {
            exit("❌ Error al guardar la imagen en el servidor.");
        }

        /*
        |--------------------------------------------------------------------------
        | Guardar datos del estudiante en la base de datos
        |--------------------------------------------------------------------------
        */
        try {
            $stmt = $pdo->prepare("
                INSERT INTO estudiantes (nombre_completo, fecha_nacimiento, pais_id, ruta_foto)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $nombre_completo,
                $fecha_nacimiento,
                $pais_id,
                $ruta_web // Guardamos ruta relativa (para mostrarla en el navegador)
            ]);

            // Redirigir con mensaje de éxito
            header("Location: ../vistas/estudiantes.php?mensaje=registro_exitoso");
            exit();

        } catch (PDOException $e) {
            echo "❌ Error en base de datos: " . $e->getMessage();
        }

    } else {
        echo "⚠️ Faltan datos obligatorios o imagen no válida.";
    }

} else {
    echo "⛔ Acceso denegado. Solo se permiten solicitudes POST.";
}
?>
