<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Subir Foto - Portal Estudiantes</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Tipografía elegante -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #f2f4f8;
      font-family: 'Outfit', sans-serif;
      overflow: hidden;
      position: relative;
    }

    canvas {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 0;
    }

    .form-container {
      position: relative;
      z-index: 2;
      max-width: 450px;
      width: 100%;
      background: #ffffff;
      padding: 2.2rem;
      border-radius: 1rem;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .btn-custom {
      background-color: #2e86de;
      color: #fff;
      font-weight: 600;
      transition: background 0.3s;
      border-radius: 10px;
    }

    .btn-custom:hover {
      background-color: #1b4f72;
    }

    .preview-img {
      width: 130px;
      height: 130px;
      object-fit: cover;
      border-radius: 50%;
      margin: 20px auto;
      display: none;
      border: 3px solid #dee2e6;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    label {
      font-weight: 600;
      color: #212529;
      font-size: 0.96rem;
    }

    .form-text {
      font-size: 0.85rem;
      color: #6c757d;
    }

    h2 {
      color: #1d3557;
      font-weight: 700;
      font-size: 1.6rem;
    }

    p.text-muted {
      font-size: 0.9rem;
      color: #6c757d !important;
    }



    .fade-out-up {
      opacity: 0;
      transform: translateY(-20px);
      transition: opacity 1s ease, transform 1s ease;
    }
  </style>
</head>

<body>

  <canvas id="bgCanvas"></canvas>

  <div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="form-container">

      <div class="text-center mb-4">
        <i class="bi bi-person-circle fs-1 text-primary"></i>
        <h2 class="mt-2">Subir Foto de Perfil</h2>
        <p class="text-muted">Completa tu perfil cargando una imagen.</p>
      </div>

      <div class="text-center">
        <img id="preview" class="preview-img" src="#" alt="Vista previa">
      </div>

      <form id="uploadForm" action="../php/subir_foto.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
          <label for="foto" class="form-label">
            <i class="bi bi-upload me-1"></i> Selecciona tu foto
          </label>
          <input class="form-control" type="file" id="foto" name="foto" accept="image/*" required>
          <div class="form-text">Formatos aceptados: JPG, PNG | Tamaño máximo: 2MB.</div>
          <div class="invalid-feedback">
            Por favor, selecciona una imagen válida.
          </div>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-custom">
            <i class="bi bi-cloud-upload-fill me-2"></i> Subir Foto
          </button>



          <?php if (isset($_SESSION['success'])): ?>
            <div id="alert-message" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              <?= htmlspecialchars($_SESSION['success']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
          <?php endif; ?>

          <?php if (isset($_SESSION['error'])): ?>
            <div id="alert-message" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?= htmlspecialchars($_SESSION['error']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
          <?php endif; ?>



        </div>
      </form>
    </div>
  </div>

  <!-- Validación Bootstrap y Preview -->
  <script>
    (() => {
      'use strict'
      const form = document.getElementById('uploadForm');
      const fotoInput = document.getElementById('foto');
      const preview = document.getElementById('preview');

      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false);

      fotoInput.addEventListener('change', () => {
        const file = fotoInput.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
        }
      });
    })();
  </script>

  <!-- Canvas Background -->
  <script>
    const canvas = document.getElementById('bgCanvas');
    const ctx = canvas.getContext('2d');
    let width, height;
    let circles = [];

    function resizeCanvas() {
      width = window.innerWidth;
      height = window.innerHeight;
      canvas.width = width;
      canvas.height = height;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    function createCircles() {
      circles = [];
      for (let i = 0; i < 25; i++) {
        circles.push({
          x: Math.random() * width,
          y: Math.random() * height,
          radius: Math.random() * 25 + 10,
          color: `rgba(46, 134, 222, ${Math.random() * 0.2 + 0.1})`,
          dx: (Math.random() - 0.5) * 0.4,
          dy: (Math.random() - 0.5) * 0.4,
        });
      }
    }
    createCircles();

    function animate() {
      ctx.clearRect(0, 0, width, height);
      for (let circle of circles) {
        ctx.beginPath();
        ctx.arc(circle.x, circle.y, circle.radius, 0, Math.PI * 2);
        ctx.fillStyle = circle.color;
        ctx.fill();

        circle.x += circle.dx;
        circle.y += circle.dy;

        if (circle.x < 0 || circle.x > width) circle.dx *= -1;
        if (circle.y < 0 || circle.y > height) circle.dy *= -1;
      }
      requestAnimationFrame(animate);
    }
    animate();
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const alert = document.getElementById('alert-message');
      if (alert) {
        setTimeout(() => {
          alert.classList.add('fade-out-up'); // Aplica la animación
          setTimeout(() => {
            alert.remove(); // Elimina el mensaje después de animarse
          }, 1000); // 1 segundo después de empezar el efecto
        }, 5000); // Espera 5 segundos antes de empezar
      }
    });
  </script>

</body>

</html>