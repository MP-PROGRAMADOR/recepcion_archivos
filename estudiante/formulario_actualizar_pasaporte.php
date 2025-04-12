<div class="card border-0 shadow-lg rounded-4">
    <div class="card-header bg-white border-bottom-0 py-4 px-4 rounded-top-4 d-flex align-items-center">
        <i class="bi bi-file-earmark-person-fill text-primary fs-3 me-2"></i>
        <h5 class="mb-0 text-dark fw-bold">Actualizar Documento de Pasaporte</h5>
    </div>

    <div class="card-body bg-light rounded-bottom-4 px-4 py-4">
        <form id="pasaporteForm" action="../php/actualizar_pasaporte.php" method="POST" enctype="multipart/form-data" novalidate>

            <!-- Campo oculto para ID del pasaporte si ya existe -->
            <input type="hidden" name="id_pasaporte" value="<?= $id_pasaporte ?? '' ?>">

            <div class="row g-4">

                <!-- Número de Pasaporte -->
                <div class="col-md-6">
                    <label for="numero" class="form-label">N° de Pasaporte</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-white"><i class="bi bi-hash"></i></span>
                        <input type="text" name="numero_pasaporte" id="numero" class="form-control" placeholder="Ej: A12345678" required value="<?= $numero_pasaporte ?? '' ?>">
                        <div class="invalid-feedback">Campo obligatorio</div>
                    </div>
                </div>

                <!-- Archivo PDF -->
                <div class="col-md-6">
                    <label for="archivo" class="form-label">Archivo PDF (opcional para actualizar)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-file-earmark-pdf-fill text-danger"></i></span>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept="application/pdf">
                    </div>
                    <small class="text-muted ms-1"><i class="bi bi-info-circle"></i> Si no seleccionas archivo, se conservará el actual.</small>
                </div>

                <!-- Fecha de Emisión -->
                <div class="col-md-6">
                    <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar-event-fill text-primary"></i></span>
                        <input type="date" name="fecha_emision" id="fecha_emision" class="form-control" required value="<?= $fecha_emision ?? '' ?>">
                    </div>
                </div>

                <!-- Fecha de Expiración -->
                <div class="col-md-6">
                    <label for="fecha_expiracion" class="form-label">Fecha de Expiración</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar-x-fill text-danger"></i></span>
                        <input type="date" name="fecha_expiracion" id="fecha_expiracion" class="form-control" required value="<?= $fecha_expiracion ?? '' ?>">
                    </div>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div id="progressContainer" class="my-4" style="display:none;">
                <label for="progress" class="form-label">
                    <i class="bi bi-cloud-upload-fill text-primary me-2"></i>Subiendo archivo...
                </label>
                <progress id="progress" value="0" max="100" class="form-range w-100"></progress>
            </div>

            <!-- Botón de acción -->
            <div class="d-flex justify-content-end align-items-center mt-4">
                <button type="submit" class="btn btn-warning rounded-pill px-5">
                    <i class="bi bi-arrow-repeat me-2"></i> Actualizar Pasaporte
                </button>
            </div>
        </form>
    </div>
</div>
