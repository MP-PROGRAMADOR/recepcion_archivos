<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Recepción de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css" rel="stylesheet">


    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .sidebar {
            background-color: #1e293b;
            color: #fff;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.hide {
            transform: translateX(-100%);
        }

        .sidebar .logo {
            padding: 20px;
            font-size: 1.4rem;
            font-weight: bold;
            background-color: #111827;
            text-align: center;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #cbd5e1;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #334155;
            color: #fff;
        }

        .sidebar a.active {
            background-color: #0d6efd;
            color: #fff;
        }

        .sidebar svg {
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .main-content.full {
            margin-left: 0;
        }

        .menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>

<body>


    <!-- Botón para mostrar el menú en móviles -->
    <button class="menu-toggle" onclick="toggleSidebar()">
        ☰
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="logo">📂 Recepción</div>
        <a href="../admin/index.php" class="active">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house"
                viewBox="0 0 16 16">
                <path
                    d="M8.354 1.146a.5.5 0 0 0-.708 0L1 7.793V14.5a.5.5 0 0 0 .5.5H6v-5h4v5h4.5a.5.5 0 0 0 .5-.5V7.793l-6.646-6.647z" />
            </svg>
            Panel Principal
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-passport"
                viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h4V1H2z" />
                <path d="M9 1v14h5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9z" />
            </svg>
            Módulo Pasaportes
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-text"
                viewBox="0 0 16 16">
                <path
                    d="M5 4a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5A.5.5 0 0 1 5 4zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5A.5.5 0 0 1 5 6zm0 2a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 5 8z" />
                <path
                    d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2A2 2 0 0 1 4 0h6.5L14 4.5zM10 0v4a1 1 0 0 0 1 1h4l-5-5z" />
            </svg>
            Módulo Notas Académicas
        </a>
        <a href="../admin/usuario.php">👨‍🎓 usuarios</a>
        <a href="#">👨‍🎓 Estudiantes</a>
        <a href="#">👤 Administradores</a>
        <a href="#">⚙️ Configuración</a>
        <a href="#">🚪 Cerrar Sesión</a>
    </nav>
    <!-- Main -->
    <div class="flex-grow-1 d-flex flex-column overflow-hidden">
        <nav class="navbar navbar-expand-lg border-bottom shadow-sm sticky-top">
            <div class="container-fluid">
                <button class="btn btn-outline-light d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-brand fw-bold"><?= $titulo ?? 'Panel de Administración' ?></span>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <span class="fw-semibold">Admin</span>
                    <a href="#" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Cerrar
                        sesión</a>
                </div>
            </div>
        </nav>



        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4"></main>




        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Resumen</h4>
            <button id="collapseSidebar" class="btn btn-outline-secondary d-none d-lg-inline"><i
                    class="bi bi-layout-sidebar-inset"></i></button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="card-title">Total de Exámenes</h6>
                        <h3><i class="bi bi-journal-text me-2"></i>245</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="card-title">Estudiantes Activos</h6>
                        <h3><i class="bi bi-people me-2"></i>132</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="card-title">Escuelas Registradas</h6>
                        <h3><i class="bi bi-building me-2"></i>12</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header"><i class="bi bi-calendar-event"></i> Calendario</div>
                    <div class="card-body">
                        <input type="date" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header"><i class="bi bi-table"></i> Últimos Exámenes</div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Estudiante</th>
                                    <th>Fecha</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Juan Pérez</td>
                                    <td>2025-04-06</td>
                                    <td>Aprobado</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>María López</td>
                                    <td>2025-04-05</td>
                                    <td>Reprobado</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Carlos Ruiz</td>
                                    <td>2025-04-04</td>
                                    <td>Aprobado</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        </main>


    </div>


    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("show");
        }

        // Activar clase 'active' en el sidebar (mejorable con JS dinámico si usas múltiples páginas)
        const links = document.querySelectorAll(".sidebar a");
        links.forEach(link => {
            link.addEventListener("click", function () {
                links.forEach(l => l.classList.remove("active"));
                this.classList.add("active");
                document.getElementById("sidebar").classList.remove("show"); // cerrar en móviles
            });
        });
    </script>
    <!-- SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>

</body>

</html>