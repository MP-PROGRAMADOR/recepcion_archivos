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
</body>
</html>
