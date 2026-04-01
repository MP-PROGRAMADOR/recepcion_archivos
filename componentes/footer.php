




 
 
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="../config/js/sweetalert2@11.js"></script>
<!-- Bootstrap JS -->
<script src="../config/js/bootstrap.bundle.min.js"></script>
<script src="../config/js/canva.js"></script>


<!-- Librerías necesarias -->
<script src="../config/js/dataTables.buttons.min.js"></script>
<script src="../config/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


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


<script>
$(document).ready(function() {
    $('#tablaRecepcion').DataTable({
        
        // -----------------------------
        // Layout limpio (sin botones)
        // -----------------------------
        dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'l><'col-md-6 d-flex justify-content-end'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-2'<'col-md-5'i><'col-md-7 d-flex justify-content-end'p>>",

        // -----------------------------
        // Configuración general
        // -----------------------------
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        ordering: true,
        responsive: true,

        // -----------------------------
        // Idioma
        // -----------------------------
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            zeroRecords: "No se encontraron registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });
});
</script>

</body>
</html>
 
