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

    .collapsed+.navbar-top {
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

    .collapsed~.content {
      margin-left: 0;
    }

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