<?php
session_start();
if (!isset($_SESSION['estudiante'])) {
    header("Location: index.php");
    exit();
}

$estudiante = $_SESSION['estudiante'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel del Estudiante</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS y fuentes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fb;
        }

        .navbar {
            background-color: #024c7a;
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #fff;
        }

        .navbar .nav-link:hover {
            color: #ffc107;
        }

        .panel-container {
            padding: 2rem;
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 12px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(2, 76, 122, 0.25);
            border-color: #024c7a;
        }

        table {
            font-size: 0.95rem;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">
                <i class="bi bi-person-circle me-2"></i>Panel Estudiante
            </a>
            <div class="ms-auto">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-left me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- PANEL -->
    <div class="container panel-container">

        <!-- Bienvenida -->
        <div class="mb-4">
            <h4 class="fw-semibold">Hola, <?= htmlspecialchars($estudiante['nombre_completo']) ?> 👋</h4>
            <p>País: <strong><?= htmlspecialchars($estudiante['pais_id']) ?></strong></p>
        </div>

        <!-- Formulario de pasaporte -->
        <div class="form-section border rounded p-4 shadow-sm bg-white">
            <h5 class="mb-4 text-primary">
                <i class="bi bi-passport me-2"></i>Formulario de Pasaporte
            </h5>
            <form action="guardar_pasaporte.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Número de Pasaporte -->
                    <div class="col-md-6 mb-4">
                        <label for="numero" class="form-label fw-semibold text-dark">N° de Pasaporte</label>
                        <input type="text" name="numero" id="numero" class="form-control border rounded shadow-sm"
                            required placeholder="Ej: A12345678">
                    </div>

                    <!-- Imagen del pasaporte -->
                    <div class="col-md-6 mb-4">
                        <label for="archivo" class="form-label">Selecciona archivo (PDF, JPG, PNG)</label>
                        <input type="file" name="archivo" id="archivo" class="form-control" required>

                        <small class="text-muted">Solo se permiten archivos .jpg, .jpeg o .png</small>
                    </div>

                    <!-- Fecha de emisión -->
                    <div class="col-md-6 mb-4">
                        <label for="fecha_emision" class="form-label fw-semibold text-dark">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" id="fecha_emision"
                            class="form-control border rounded shadow-sm" required>
                    </div>

                    <!-- Fecha de expiración -->
                    <div class="col-md-6 mb-4">
                        <label for="fecha_expiracion" class="form-label fw-semibold text-dark">Fecha de
                            Expiración</label>
                        <input type="date" name="fecha_expiracion" id="fecha_expiracion"
                            class="form-control border rounded shadow-sm" required>
                    </div>
                </div>

                <!-- Botón guardar -->
                <button type="submit" class="btn btn-success px-4 shadow-sm">
                    <i class="bi bi-save me-2"></i>Guardar pasaporte
                </button>
            </form>
        </div>


        <!-- Formulario de subida de archivos -->
        <div class="form-section">
            <h5 class="mb-3"><i class="bi bi-upload me-2"></i>Subir Nota</h5>
            <form action="subir_archivo.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="archivo" class="form-label">Selecciona archivo (PDF, JPG, PNG)</label>
                    <input type="file" name="archivo" id="archivo" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-cloud-arrow-up me-1"></i>Subir archivo
                </button>
            </form>
        </div>

        <!-- Archivos subidos -->
        <div class="form-section">
            <h5 class="mb-3"><i class="bi bi-folder2-open me-2"></i>Archivos subidos</h5>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí puedes usar PHP para cargar los archivos del estudiante desde la base de datos -->
                    <tr>
                        <td>pasaporte.pdf</td>
                        <td>PDF</td>
                        <td>2025-04-08</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>