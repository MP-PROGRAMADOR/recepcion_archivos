<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Recepción de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar base */
        .sidebar {
            background-color: #212529;
            color: white;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease-in-out;
        }

        /* Sidebar oculto en móvil */
        .sidebar-mobile-hidden {
            transform: translateX(-100%);
        }

        /* Sidebar visible en móvil */
        .sidebar-mobile-visible {
            transform: translateX(0);
        }

        /* Navbar superior */
        .navbar-top {
            background: #f8f9fa;
            padding: 10px 15px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar h4 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
        }

        /* Estilo para link activo */
        .sidebar .nav-link.active {
            background-color: #6c757d;
            color: #fff !important;
            border-radius: 5px;
            font-weight: bold;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 10px 15px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: #334155;
            color: #fff;
        }

        /* Navbar superior fija */
        .navbar-top {
            height: 60px;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            transition: left 0.3s ease;
        }

        .section-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 0.5rem 1rem 0.2rem;
        }

        .content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 80px 20px 20px;
            height: 100vh;
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                width: 70%;
                height: 100%;
            }

            .navbar-top {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            /* Sidebar oculta en móviles */
            .sidebar-mobile-hidden {
                transform: translateX(-100%);
            }

            /* Mostrar en móviles */
            .sidebar-mobile-visible {
                transform: translateX(0);
            }

            .navbar-top {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1050;
            }
        }

        .collapsed~.content {
            margin-left: 0;
        }

        /* Contenido principal */
        .cv-container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cv-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .cv-section {
            margin-bottom: 30px;
        }

        .cv-section h5 {
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
            margin-bottom: 20px;
            color: #0d6efd;
        }

        .cv-photo {
            max-width: 150px;
            border-radius: 10px;
            border: 2px solid #dee2e6;
        }

        .cv-label {
            font-weight: 600;
            color: #495057;
        }

        .cv-value {
            margin-bottom: 10px;
        }
    </style>

</head>

<body>

    <div class="navbar-top d-md-none">
        <button class="btn btn-outline-dark" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-semibold">Bienvenido al panel</span>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar d-md-block" id="sidebar">
        <!-- Botón cerrar en móvil -->
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom d-md-none">
            <span class="fw-bold">Menú</span>
            <button class="btn btn-sm btn-light" onclick="toggleSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <h5 class="mb-4">
            <i class="bi bi-mortarboard-fill me-2"></i> Gestión Académica
        </h5>

        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item mb-2">
                <a href="../admin/index.php" class="nav-link text-white">
                    <i class="bi bi-speedometer me-2"></i> Dashboard
                </a>
            </li>

            <!-- Sección Administración -->
            <li class="text-uppercase text-secondary small fw-bold mt-4 mb-2">Administración</li>

            <li class="nav-item mb-2">
                <a href="../admin/usuario.php" class="nav-link text-white">
                    <i class="bi bi-person-badge-fill me-2"></i> Usuarios
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="../admin/pais.php" class="nav-link text-white">
                    <i class="bi bi-geo-alt-fill me-2"></i> País
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="../admin/estudiantes.php" class="nav-link text-white">
                    <i class="bi bi-people-fill me-2"></i> Estudiantes
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="academico.php" class="nav-link text-white">
                    <i class="bi bi-calendar2-week-fill me-2"></i> Año Académico
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="../admin/pasaportes.php" class="nav-link text-white">
                    <i class="bi bi-person-vcard me-2"></i> Pasaportes
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="../admin/notas.php" class="nav-link text-white">
                    <i class="bi bi-clipboard-data me-2"></i> Notas
                </a>
            </li>

            <!-- Sección Configuración -->
            <li class="text-uppercase text-secondary small fw-bold mt-2 mb-2">Configuración</li>

            <li class="nav-item mb-2">
                <a href="configuracion.php" class="nav-link text-white">
                    <i class="bi bi-gear-fill me-2"></i> Ajustes
                </a>
            </li>

            <!-- Cierre de sesión -->
            <li class="nav-item">
                <a href="../php/logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                </a>
            </li>
        </ul>
    </aside>

    <main class="content" id="mainContent">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Resumen</h4>
            <button id="collapseSidebar" class="btn btn-outline-secondary d-none d-lg-inline"><i
                    class="bi bi-layout-sidebar-inset"></i></button>
        </div>
    </main>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-mobile-visible');
            sidebar.classList.toggle('sidebar-mobile-hidden');
        }
    </script>

</body>

</html>
