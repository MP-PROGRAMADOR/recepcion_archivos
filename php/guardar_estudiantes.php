<?php
// Mostrar todos los errores para la depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de conexión a la base de datos
require_once '../config/conexion.php';

// Función para sanitizar los datos y evitar XSS
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Función para generar un código de acceso único de 8 dígitos
function generar_codigo_acceso() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)); // Genera un código de 8 caracteres
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar las entradas del formulario
    $nombre_completo = sanitize_input($_POST['nombre_completo']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $pais = sanitize_input($_POST['pais']);

    // Subida de la foto
    $foto = $_FILES['foto'];
    $foto_nombre = $foto['name'];
    $foto_tmp = $foto['tmp_name'];
    $foto_error = $foto['error'];
    $foto_tamaño = $foto['size'];

    // Verificar si la foto se subió correctamente
    if ($foto_error !== UPLOAD_ERR_OK) {
        echo "<script>Swal.fire('Error', 'Hubo un problema al subir la foto. Código de error: " . $foto_error . "', 'error');</script>";
        exit;
    }

    // Generar automáticamente el código de acceso de 8 dígitos
    $codigo_acceso = generar_codigo_acceso();

    // Validaciones
    if (empty($nombre_completo) || empty($fecha_nacimiento) || empty($foto_nombre) || empty($pais)) {
        echo "<script>Swal.fire('Error', 'Todos los campos son obligatorios.', 'error');</script>";
        exit;
    }

    // Validar la fecha de nacimiento (que debe ser una fecha válida)
    if (!strtotime($fecha_nacimiento)) {
        echo "<script>Swal.fire('Error', 'Fecha de nacimiento no válida.', 'error');</script>";
        exit;
    }

    // Validar si el archivo de imagen es válido (puedes agregar más validaciones si lo necesitas)
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $foto_extension = strtolower(pathinfo($foto_nombre, PATHINFO_EXTENSION));

    if (!in_array($foto_extension, $extensiones_permitidas)) {
        echo "<script>Swal.fire('Error', 'Solo se permiten imágenes JPG, JPEG, PNG o GIF.', 'error');</script>";
        exit;
    }

    // Verificar si el directorio 'uploads/' existe, si no, crear el directorio
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true); // 0777: permisos de lectura, escritura y ejecución para todos
    }

    // Insertar los datos del estudiante en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO estudiantes (nombre_completo, codigo_acceso, fecha_nacimiento, pais) VALUES (:nombre_completo, :codigo_acceso, :fecha_nacimiento, :pais)");
        $stmt->bindParam(':nombre_completo', $nombre_completo);
        $stmt->bindParam(':codigo_acceso', $codigo_acceso);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':pais', $pais);
        $stmt->execute();

        // Obtener el ID del estudiante recién insertado
        $estudiante_id = $pdo->lastInsertId();

        // Construir la ruta de destino de la foto usando el ID del estudiante
        $foto_destino = 'uploads/' . $estudiante_id . '.' . $foto_extension;

        // Verificar si el archivo se movió correctamente
        if (!move_uploaded_file($foto_tmp, $foto_destino)) {
            echo "<script>Swal.fire('Error', 'Error al subir la foto del estudiante.', 'error');</script>";
            exit;
        }

        // Depuración: Verificar que la ruta de la foto se haya creado correctamente
        echo "<script>console.log('Foto guardada en: " . $foto_destino . "');</script>";

        // Actualizar el registro del estudiante con la URL de la foto
        $stmt = $pdo->prepare("UPDATE estudiantes SET ruta_foto = :ruta_foto WHERE id = :id");
        $stmt->bindParam(':ruta_foto', $foto_destino);
        $stmt->bindParam(':id', $estudiante_id);
        $stmt->execute();

        // Mostrar mensaje de éxito con SweetAlert2 y redirigir a estudiantes.php
        echo "<script>
            Swal.fire('Éxito', 'Estudiante registrado exitosamente. El código de acceso es " . $codigo_acceso . ".', 'success').then(function() {
                window.location = 'usuarios.php'; // Redirigir a la página de usuarios
            });
        </script>";
    } catch (PDOException $e) {
        // Si ocurre un error al guardar
        echo "<script>Swal.fire('Error', 'Error al registrar el estudiante: " . $e->getMessage() . "', 'error');</script>";
    }
}
?>
