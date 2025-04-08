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
