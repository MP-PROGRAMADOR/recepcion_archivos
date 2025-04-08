<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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


  
        /* Estilo para la vista previa de la imagen */
        #foto_preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #ddd;
            margin-top: 10px;
        }
    
  </style>
</head>
<body>

