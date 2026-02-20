




 
 
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="../config/js/sweetalert2@11.js"></script>
<!-- Bootstrap JS -->
<script src="../config/js/bootstrap.bundle.min.js"></script>
<script src="../config/js/canva.js"></script>
<script src="../config/js/jquery-3.6.0.min.js"></script>
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("show");
  }
</script>

<script>
        // Función para mostrar la vista previa de la imagen seleccionada
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('foto_preview');
                output.src = reader.result;
                output.style.display = 'block'; // Mostrar la imagen cuando se cargue
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>
</html>
 
