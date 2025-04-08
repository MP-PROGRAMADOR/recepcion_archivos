<?php
/*
|--------------------------------------------------------------------------
| Script backend para guardar estudiantes en base de datos
|--------------------------------------------------------------------------
| Procesa el formulario de estudiantes:
| - Valida y sanitiza datos
| - Verifica que el país exista
| - Verifica la imagen (tipo, tamaño, permisos)
| - Genera un código único y descriptivo
| - Guarda los datos en la base de datos
*/

require_once '../config/conexion.php'; // Conexión PDO

function limpiarTexto($dato) {
    return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST['nombre_completo'], $_POST['fecha_nacimiento'], $_POST['pais']) &&
        isset($_FILES['foto']) && $_FILES['foto']['error'] === 0
    ) {
        $nombre_completo = limpiarTexto($_POST['nombre_completo']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $pais_id = intval($_POST['pais']);

        if (!DateTime::createFromFormat('Y-m-d', $fecha_nacimiento)) {
            exit("⚠️ Error: Fecha inválida.");
        }

        // Verificar país
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM paises WHERE id = ?");
        $stmt->execute([$pais_id]);
        if ($stmt->fetchColumn() == 0) {
            exit("⚠️ Error: El país seleccionado no existe.");
        }

        // Validar imagen
        $foto = $_FILES['foto'];
        $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $tamaño_maximo = 2 * 1024 * 1024;

        if (!in_array($extension, $permitidos)) {
            exit("⚠️ Formato de imagen no permitido (usa JPG, PNG, GIF o WEBP).");
        }

        if ($foto['size'] > $tamaño_maximo) {
            exit("⚠️ La imagen supera el límite de 2MB.");
        }

        // Carpeta uploads
        $carpeta = 'uploads/';
        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0755, true)) {
                exit("❌ Error al crear la carpeta /uploads.");
            }
        }

        if (!is_writable($carpeta)) {
            if (!chmod($carpeta, 0755)) {
                exit("❌ La carpeta /uploads no tiene permisos de escritura.");
            }
        }

        // Guardar imagen
        $nombre_archivo = uniqid('estudiante_') . '.' . $extension;
        $ruta_web = 'uploads/' . $nombre_archivo;
        $ruta_destino = $carpeta . $nombre_archivo;

        if (!move_uploaded_file($foto['tmp_name'], $ruta_destino)) {
            exit("❌ Error al guardar la imagen en el servidor.");
        }

        /*
        |--------------------------------------------------------------------------
        | Generar código único descriptivo (Ej: EST-20250408-001)
        |--------------------------------------------------------------------------
        */
        $fecha_actual = date('Ymd');
        $prefijo = 'EST-' . $fecha_actual . '-';

        // Contar cuántos estudiantes se han creado hoy
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiantes WHERE DATE(creado_en) = CURDATE()");
        $stmt->execute();
        $contador = $stmt->fetchColumn() + 1;

        $codigo_acceso = $prefijo . str_pad($contador, 3, '0', STR_PAD_LEFT);

        /*
        |--------------------------------------------------------------------------
        | Insertar en base de datos
        |--------------------------------------------------------------------------
        */
        try {
            $stmt = $pdo->prepare("
                INSERT INTO estudiantes (nombre_completo, codigo_acceso, fecha_nacimiento, pais_id, ruta_foto)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nombre_completo,
                $codigo_acceso,
                $fecha_nacimiento,
                $pais_id,
                $ruta_web
            ]);

            header("Location: ../admin/estudiantes.php?mensaje=registro_exitoso");
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
