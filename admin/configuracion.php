<?php
// Iniciar sesión
 

// Incluir el header
include_once("../componentes/header.php");

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

// Obtener la configuración actual del sitio
$sql = "SELECT * FROM configuracion LIMIT 1";
$resultado = $pdo->query($sql);
$config = $resultado->fetch();

// Si no existe la configuración, crear una nueva
if (!$config) {
    $sql_insert = "INSERT INTO configuracion (nombre_sitio, logo, color_primario, descripcion, img_estudiante, img_admin) 
                    VALUES ('Mi Sitio Web', '', '#000000', '', '', '')";
    $pdo->query($sql_insert);
    $config = [
        'nombre_sitio' => 'Mi Sitio Web',
        'logo' => '',
        'color_primario' => '#000000',
        'descripcion' => '',
        'img_estudiante' => '',
        'img_admin' => ''
    ];
}


include_once("../componentes/sidebar.php");
?>

<main class="content" id="mainContent">
    <h1 class="page-title">Configuración del Sitio</h1>
    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Formulario de carga de imágenes -->
    <form action="../php/guardar_configuracion.php" method="POST" enctype="multipart/form-data" class="p-5 border rounded bg-light shadow-sm">
        <!-- Título del formulario -->
        <h3 class="mb-4 text-center" style="font-family: 'Arial', sans-serif; color: #333; font-weight: bold;">
            Configuración del Sitio</h3>

        <!-- Nombre del Sitio -->
        <div class="form-group mb-4">
            <label for="nombre_sitio" class="h5 text-dark">Nombre del Sitio:</label>
            <input type="text" id="nombre_sitio" name="nombre_sitio"
                value="<?php echo htmlspecialchars($config['nombre_sitio']); ?>"
                class="form-control form-control-lg p-3" required>
        </div>

        <!-- Logo del Sitio -->
        <div class="form-group mb-4">
            <label for="logo" class="h5 text-dark">Logo (Imagen del Sitio):</label>
            <input type="file" id="logo" name="logo" accept="image/*" class="form-control-file">
        </div>

        <!-- Imagen Estudiante -->
        <div class="form-group mb-4">
            <label for="img_estudiante" class="h5 text-dark">Imagen Estudiante:</label>
            <input type="file" id="img_estudiante" name="img_estudiante" accept="image/*" class="form-control-file">
        </div>

        <!-- Imagen Admin -->
        <div class="form-group mb-4">
            <label for="img_admin" class="h5 text-dark">Imagen Admin:</label>
            <input type="file" id="img_admin" name="img_admin" accept="image/*" class="form-control-file">
        </div>

        <!-- Color Primario -->
        <div class="form-group mb-4">
            <label for="color_primario" class="h5 text-dark">Color Primario:</label>
            <input type="color" id="color_primario" name="color_primario"
                value="<?php echo htmlspecialchars($config['color_primario']); ?>"
                class="form-control form-control-lg p-3">
        </div>

        <!-- Descripción del Sitio -->
        <div class="form-group mb-4">
            <label for="descripcion" class="h5 text-dark">Descripción del Sitio:</label>
            <textarea id="descripcion" name="descripcion" class="form-control form-control-lg p-3"
                rows="4"><?php echo htmlspecialchars($config['descripcion']); ?></textarea>
        </div>

        <!-- Botón de Enviar -->
        <div class="form-group text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm"
                style="background-color: #007bff; border-color: #0056b3; font-weight: bold;">
                <i class="bi bi-save"></i> Guardar Configuración
            </button>
        </div>
    </form>

</main>

<?php
include_once("../componentes/footer.php");
?>