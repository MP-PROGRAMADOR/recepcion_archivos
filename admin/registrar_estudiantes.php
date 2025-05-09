<?php

include_once("../componentes/header.php");
include_once("../componentes/sidebar.php");

// Consultas a la base de datos
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM paises ORDER BY nombre ASC");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>Swal.fire('Error', 'Error al cargar los países: " . $e->getMessage() . "', 'error');</script>";
    $paises = [];
}
?>
<main class="content" id="mainContentGuin">
    <canvas id="bgCanvas" style="position: fixed; top: 0; left: 0; z-index: -1;"></canvas>
    <div class="container mt-4">
        <?php include_once("../componentes/alerta.php"); ?>
        <div class="card shadow rounded-4">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-2"></i>
                <h5 class="mb-0">Formulario de Registro de Estudiante</h5>
            </div>
            <div class="card-body">

                <form action="../php/guardar_estudiantes.php" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    <div class="row g-3">
                        <!-- Nombre Completo -->
                        <div class="col-md-6 mb-2">
                            <label for="nombre_completo" class="form-label fw-bold">Nombre Completo</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control"
                                    required placeholder="Ej: María López">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa tu nombre completo.</div>
                            </div>
                        </div>
                        <!-- Fecha de Nacimiento -->
                        <div class="col-md-6 mb-2">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                    required>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona tu fecha de nacimiento.</div>
                            </div>
                        </div>
                        <!-- Año de Inicio de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="anio_inicio_carrera" class="form-label fw-bold">Año de Inicio de Carrera</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                                <input type="number" id="anio_inicio_carrera" name="anio_inicio_carrera"
                                    class="form-control" required placeholder="Ej: 2025">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa el año de inicio de carrera.</div>
                            </div>
                        </div>
                        <!-- Año de fin de Carrera -->
                        <div class="col-md-6 mb-2">
                            <label for="anio_fin_carrera" class="form-label fw-bold">Año de fin de Carrera</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                                <input type="number" id="anio_fin_carrera" name="anio_fin_carrera" class="form-control"
                                    required placeholder="Ej: 2030">
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, ingresa el año de fin de carrera.</div>
                            </div>
                        </div>
                        <!-- País de Estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="pais" class="form-label fw-bold">País de Estudios</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <select id="pais" name="pais" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu país</option>
                                    <?php foreach ($paises as $pais): ?>
                                        <option value="<?= htmlspecialchars($pais['id']) ?>">
                                            <?= htmlspecialchars($pais['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona un país.</div>
                            </div>
                        </div>
                        <!-- Ciudad de Estudios -->
                        <div class="col-md-6 mb-2">
                            <label for="ciudad" class="form-label fw-bold">Ciudad de Estudios</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                                <select id="ciudad" name="ciudad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu ciudad</option>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una ciudad.</div>
                            </div>
                        </div>
                        <!-- Universidad -->
                        <div class="col-md-6 mb-2">
                            <label for="universidad" class="form-label fw-bold">Universidad</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select id="universidad" name="universidad" class="form-select" required>
                                    <option value="" disabled selected>Selecciona tu universidad</option>
                                </select>
                                <div class="valid-feedback">¡Correcto!</div>
                                <div class="invalid-feedback">Por favor, selecciona una universidad.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <!-- Sección de Cuenta Bancaria -->
                        <hr class="my-4">
                        <h5 class="mb-3 text-success"><i class="bi bi-credit-card-2-front-fill me-2"></i>Información de
                            Cuenta Bancaria</h5>

                        <div class="row g-3">
                            <!-- Tipo de Cuenta -->
                            <div class="col-md-6 mb-2">
                                <label for="tipo_cuenta" class="form-label fw-bold">Tipo de Cuenta</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                                    <input type="text" id="tipo_cuenta" name="tipo_cuenta" class="form-control" required
                                        placeholder="Ej: Ahorros, Corriente">
                                    <div class="valid-feedback">¡Correcto!</div>
                                    <div class="invalid-feedback">Por favor, indica el tipo de cuenta.</div>
                                </div>
                            </div>

                            <!-- Banco -->
                            <div class="col-md-6 mb-2">
                                <label for="banco" class="form-label fw-bold">Banco</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-bank2"></i></span>
                                    <input type="text" id="banco" name="banco" class="form-control" required
                                        placeholder="Ej: Banco Nacional">
                                    <div class="valid-feedback">¡Correcto!</div>
                                    <div class="invalid-feedback">Por favor, ingresa el nombre del banco.</div>
                                </div>
                            </div>

                            <!-- Número de Cuenta -->
                            <div class="col-md-6 mb-2">
                                <label for="numero_cuenta" class="form-label fw-bold">Número de Cuenta</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control"
                                        required placeholder="Ej: 1234567890">
                                    <div class="valid-feedback">¡Correcto!</div>
                                    <div class="invalid-feedback">Por favor, ingresa el número de cuenta.</div>
                                </div>
                            </div>

                            <!-- Tarjeta VISA -->
                            <div class="col-md-6 mb-2">
                                <label for="tarjeta_visa" class="form-label fw-bold">Tarjeta VISA (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                    <input type="text" id="tarjeta_visa" name="tarjeta_visa" class="form-control"
                                        placeholder="Ej: 4111 1111 1111 1111">
                                </div>
                            </div>

                            <!-- Fecha de Caducidad de la Tarjeta -->
                            <div class="col-md-6 mb-2">
                                <label for="fecha_caducidad_tarjeta" class="form-label fw-bold">Fecha de Caducidad
                                    (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check-fill"></i></span>
                                    <input type="date" id="fecha_caducidad_tarjeta" name="fecha_caducidad_tarjeta"
                                        class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Botones -->
                    <div class="d-flex justify-content-between mb-4 p-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar
                        </button>
                        <a href="estudiantes.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Cargar ciudades al seleccionar un país
    document.getElementById('pais').addEventListener('change', function () {
        var paisId = this.value;
        if (paisId) {
            fetch(`../php/get_ciudades.php?pais_id=${paisId}`)
                .then(response => response.json())
                .then(data => {
                    var ciudadSelect = document.getElementById('ciudad');
                    ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona tu ciudad</option>';
                    data.ciudades.forEach(function (ciudad) {
                        ciudadSelect.innerHTML += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar las ciudades:', error);
                    Swal.fire('Error', 'Ocurrió un error al intentar cargar las ciudades.', 'error');
                });
        }
    });

    // Cargar universidades al seleccionar una ciudad
    document.getElementById('ciudad').addEventListener('change', function () {
        var ciudadId = this.value;
        if (ciudadId) {
            fetch(`../php/get_universidades.php?ciudad_id=${ciudadId}`)
                .then(response => response.json())
                .then(data => {
                    var universidadSelect = document.getElementById('universidad');
                    universidadSelect.innerHTML = '<option value="" disabled selected>Selecciona tu universidad</option>';
                    data.universidades.forEach(function (universidad) {
                        universidadSelect.innerHTML += `<option value="${universidad.id}">${universidad.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar las universidades:', error);
                    Swal.fire('Error', 'Ocurrió un error al intentar cargar las universidades.', 'error');
                });
        }
    });

    // Validación Bootstrap 5
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
<?php include_once("../componentes/footer.php"); ?>