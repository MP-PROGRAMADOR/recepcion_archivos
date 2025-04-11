




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Registrar Usuario</h4>
        </div>
        <div class="card-body">
            <form action="guardar_usuario.php" method="POST" novalidate>
                <div class="row g-3">

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
 
                    <!-- Contraseña -->
                    <div class="col-md-6">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required minlength="6">
                    </div>

                   

                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
