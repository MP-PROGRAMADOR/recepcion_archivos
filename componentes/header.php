<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Recepción de Archivos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
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

    .layout {
      display: flex;
      height: 100vh;
    }

    /* Sidebar fijo */
    .sidebar {
      width: 250px;
      background-color: #1e293b;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      padding: 1rem 0.5rem;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      transition: transform 0.3s ease;
      z-index: 1050;
      overflow-y: auto;
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    .sidebar h4 {
      font-weight: bold;
      text-align: center;
      margin-bottom: 2rem;
      color: #fff;
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

    .sidebar .nav-link.active {
      background-color: #0d6efd;
      color: #fff !important;
    }

    .section-title {
      font-size: 0.8rem;
      text-transform: uppercase;
      color: #94a3b8;
      padding: 0.5rem 1rem 0.2rem;
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

    .collapsed + .navbar-top {
      left: 0;
    }

    /* Botón toggle sidebar */
    .menu-toggle {
      background: none;
      border: none;
      font-size: 1.5rem;
      color: #0d6efd;
    }

    /* Contenido principal */
    .content {
      flex-grow: 1;
      margin-left: 250px;
      padding: 80px 20px 20px;
      height: 100vh;
      overflow-y: auto;
      transition: margin-left 0.3s ease;
    }

    .collapsed ~ .content {
      margin-left: 0;
    }

<<<<<<< HEAD
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

     /* Estilo para la vista previa de la imagen */
     #foto_preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #ddd;
            margin-top: 10px;
        }

=======
>>>>>>> 8e4f530 (mejorando la vista listar usuario)
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .navbar-top {
        left: 0;
      }

      .content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
