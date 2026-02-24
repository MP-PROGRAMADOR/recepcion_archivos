<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/conexion.php';

$errores = [];
$usuario_id = $_SESSION['usuario_id'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$navegador = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre_completo'] ?? '');
$fecha = trim($_POST['fecha_nacimiento'] ?? '');
$pais_id = intval($_POST['pais'] ?? 0);
$ciudad_id = intval($_POST['ciudad'] ?? 0);
$universidad_id = intval($_POST['universidad'] ?? 0);
$anio_inicio = intval($_POST['anio_inicio_carrera'] ?? 0);
$anio_fin = intval($_POST['anio_fin_carrera'] ?? 0);

$accion = "EDITAR";
$modulo = "Estudiantes";
$registro_id = $id;
$resultado = "EXITO";
$descripcion = "";

### 🔹 VALIDACIONES
if ($nombre === '') $errores[] = "El nombre completo es obligatorio.";

if ($fecha === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $errores[] = "La fecha de nacimiento no es válida.";
} elseif ($fecha > date('Y-m-d')) {
    $errores[] = "La fecha de nacimiento no puede ser futura.";
}

if ($pais_id <= 0) $errores[] = "Debe seleccionar un país válido.";
if ($ciudad_id <= 0) $errores[] = "Debe seleccionar una ciudad válida.";
if ($universidad_id <= 0) $errores[] = "Debe seleccionar una universidad válida.";

if ($anio_inicio < 1900 || $anio_inicio > date('Y')) {
    $errores[] = "El año de inicio no es válido.";
}

if ($anio_fin < $anio_inicio || $anio_fin > date('Y') + 10) {
    $errores[] = "El año de finalización no es válido.";
}

### 🔹 EVITAR DUPLICADOS
$nombre_normalizado = strtolower(preg_replace('/\s+/', ' ', $nombre));
$stmt = $pdo->prepare("SELECT COUNT(*) FROM estudiantes 
    WHERE LOWER(TRIM(REPLACE(nombre_completo,'  ',' '))) = ? 
    AND id != ?");
$stmt->execute([$nombre_normalizado, $id]);

if ($stmt->fetchColumn() > 0) {
    $errores[] = "Ya existe otro estudiante registrado con ese nombre.";
}

### 🔹 OBTENER CÓDIGO
$stmt = $pdo->prepare("SELECT codigo_acceso FROM estudiantes WHERE id = ?");
$stmt->execute([$id]);
$codigo = $stmt->fetchColumn();

$ruta_foto = null;
$ruta_beca = null;

### 🔹 SUBIDA FOTO PERFIL
if (!empty($_FILES['foto']['name'])) {
    $permitidos = ['image/jpeg','image/png','image/webp'];
    $foto = $_FILES['foto'];

    if (!in_array($foto['type'], $permitidos)) $errores[] = "La foto debe ser JPG, PNG o WEBP.";
    if ($foto['size'] > 2 * 1024 * 1024) $errores[] = "La foto no debe superar 2MB.";

    if (empty($errores) && is_uploaded_file($foto['tmp_name'])) {
        $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $directorio = __DIR__ . '/upload/perfil/';
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);

        $nombre_archivo = "perfil-$codigo.$extension";
        $ruta_relativa = "upload/perfil/$nombre_archivo";
        $ruta_completa = $directorio . $nombre_archivo;

        if (move_uploaded_file($foto['tmp_name'], $ruta_completa)) {
            $ruta_foto = $ruta_relativa;
        } else {
            $errores[] = "No se pudo guardar la foto.";
        }
    }
}

### 🔹 SUBIDA ARCHIVO BECA
if (!empty($_FILES['archivo_beca']['name'])) {
    $permitidos_beca = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg','image/png'
    ];

    $beca = $_FILES['archivo_beca'];

    if (!in_array($beca['type'], $permitidos_beca)) $errores[] = "El archivo de beca no es válido.";
    if ($beca['size'] > 3 * 1024 * 1024) $errores[] = "El archivo de beca no debe superar 3MB.";

    if (empty($errores) && is_uploaded_file($beca['tmp_name'])) {
        $extension = pathinfo($beca['name'], PATHINFO_EXTENSION);
        $directorio = __DIR__ . '/upload/becas/';
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);

        $nombre_beca = "beca-$codigo.$extension";
        $ruta_relativa = "upload/becas/$nombre_beca";
        $ruta_completa = $directorio . $nombre_beca;

        if (move_uploaded_file($beca['tmp_name'], $ruta_completa)) {
            $ruta_beca = $ruta_relativa;
        } else {
            $errores[] = "No se pudo guardar el archivo de beca.";
        }
    }
}

### 🔹 SI HAY ERRORES
if (!empty($errores)) {
    $resultado = "ERROR";
    $descripcion = implode(" | ", $errores);

    // Guardar en log_actividades
    $log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

    $_SESSION['errores'] = $errores;
    header("Location: ../admin/editar_estudiante.php?id=$id");
    exit;
}

### 🔹 ACTUALIZAR BD
try {
    $query = "UPDATE estudiantes SET 
        nombre_completo = ?, 
        fecha_nacimiento = ?, 
        pais_id = ?, 
        ciudad_id = ?, 
        universidad_id = ?, 
        anio_inicio_carrera = ?, 
        anio_fin_carrera = ?";

    $params = [$nombre,$fecha,$pais_id,$ciudad_id,$universidad_id,$anio_inicio,$anio_fin];

    if ($ruta_foto !== null) {
        $query .= ", ruta_foto = ?";
        $params[] = $ruta_foto;
    }

    if ($ruta_beca !== null) {
        $query .= ", archivo_beca = ?";
        $params[] = $ruta_beca;
    }

    $query .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $resultado = "EXITO";
    $descripcion = "Estudiante actualizado correctamente. ID=$id, Nombre=$nombre";

    // Guardar en log_actividades
    $log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

    $_SESSION['exito'] = "El estudiante fue actualizado correctamente.";
    header("Location: ../admin/estudiantes.php");
    exit;

} catch (PDOException $e) {
    $resultado = "ERROR";
    $descripcion = "Error al actualizar estudiante: " . $e->getMessage();

    // Guardar en log_actividades
    $log = $pdo->prepare("INSERT INTO log_actividades 
        (usuario_id, accion, modulo, registro_id, descripcion, ip_address, navegador, resultado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $log->execute([$usuario_id, $accion, $modulo, $registro_id, $descripcion, $ip, $navegador, $resultado]);

    $_SESSION['errores'] = ["Ocurrió un error al actualizar el estudiante."];
    header("Location: ../admin/editar_estudiante.php?id=$id");
    exit;
}
?>