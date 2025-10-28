<?php
require_once './pdo_conexion.php';

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Fetch data for Monthly Purchase Value Chart ---
$monthlyValueLabels = [];
$monthlyValueData = [];
try {
    $monthlyQuery = "SELECT DATE_FORMAT(fecha_compra, '%Y-%m') as month_year, 
                           SUM(peso_compra * precio_compra) as total_value 
                     FROM vacuno 
                     WHERE fecha_compra IS NOT NULL AND peso_compra IS NOT NULL AND precio_compra IS NOT NULL
                     GROUP BY month_year 
                     ORDER BY month_year ASC";
    $monthlyStmt = $conn->prepare($monthlyQuery);
    $monthlyStmt->execute();
    $monthlyResults = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($monthlyResults as $row) {
        $monthlyValueLabels[] = $row['month_year'];
        $monthlyValueData[] = (float)$row['total_value'];
    }
} catch (PDOException $e) {
    error_log("Error fetching monthly purchase value data: " . $e->getMessage());
}
$monthlyValueLabelsJson = json_encode($monthlyValueLabels);
$monthlyValueDataJson = json_encode($monthlyValueData);

// --- Fetch data for Cumulative Investment Chart ---
$cumulativeLabels = [];
$cumulativeData = [];
$monthlyTotals = []; // Temporary array to store monthly totals

try {
    // Fetch all purchases ordered by date from vacuno table
    $cumulativeQuery = "SELECT fecha_compra, (peso_compra * precio_compra) as purchase_value
                        FROM vacuno 
                        WHERE fecha_compra IS NOT NULL AND peso_compra IS NOT NULL AND precio_compra IS NOT NULL
                        ORDER BY fecha_compra ASC";
    $cumulativeStmt = $conn->prepare($cumulativeQuery);
    $cumulativeStmt->execute();
    $allPurchases = $cumulativeStmt->fetchAll(PDO::FETCH_ASSOC);

    $currentCumulativeTotal = 0;
    
    // Aggregate totals per month
    foreach ($allPurchases as $purchase) {
        $monthYear = date('Y-m', strtotime($purchase['fecha_compra']));
        $value = (float)$purchase['purchase_value'];
        if (!isset($monthlyTotals[$monthYear])) {
            $monthlyTotals[$monthYear] = 0;
        }
        $monthlyTotals[$monthYear] += $value;
    }

    // Calculate cumulative sum month by month
    ksort($monthlyTotals); // Ensure months are in chronological order

    foreach ($monthlyTotals as $monthYear => $monthlyTotal) {
        $currentCumulativeTotal += $monthlyTotal;
        $cumulativeLabels[] = $monthYear;
        $cumulativeData[] = round($currentCumulativeTotal, 2); // Store cumulative total
    }

} catch (PDOException $e) {
    error_log("Error fetching cumulative investment data: " . $e->getMessage());
}
$cumulativeLabelsJson = json_encode($cumulativeLabels);
$cumulativeDataJson = json_encode($cumulativeData);
// --- End chart data fetching ---

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vacuno Register Compras</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/Ganagram_icono.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!--Bootstrap 5 Css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<!-- Include Chart.js and Chart.js DataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<!-- SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<!-- Place these in the <head> section in this exact order -->

<!-- jQuery Core (main library) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

<!-- DataTables JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add these in the <head> section, after your existing DataTables CSS/JS -->
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables Buttons JS -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="./vacuno.css">
</head>
<body>
<!-- Icon Navigation Buttons -->

<div class="container nav-icons-container">
    <div class="icon-button-container">
        <button onclick="window.location.href='../inicio.php'" class="icon-button">
            <img src="./images/Ganagram_New_Logo-png.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INICIO</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./inventario_vacuno.php'" class="icon-button">
            <img src="./images/vaca.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">POBLACION</span>
    </div>
    
    <div class="icon-button-container">
        <button onclick="window.location.href='./vacuno_indices.php'" class="icon-button">
            <img src="./images/fondo-indexado.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">INDICES</span>
    </div>

    <div class="icon-button-container">
        <button onclick="window.location.href='./vacuno_feria.php'" class="icon-button">
            <img src="./images/feria.png" alt="Inicio" class="nav-icon">
        </button>
        <span class="button-label">FERIA</span>
    </div>

    
    <div class="icon-button-container">
            <button onclick="window.location.href='./vacuno_configuracion.php'" class="icon-button">
                <img src="./images/configuracion.png" alt="Inicio" class="nav-icon">
            </button>
            <span class="button-label">CONFIG</span>
        </div>

</div>


<!-- Add back button before the header container -->
<a href="./vacuno_registros.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="container text-center">
  <h3  class="container mt-4 text-white" class="collapse" id="section-historial-produccion-vacuno">
  REGISTROS DE COMPRAS
  </h3>
  
  <div class="container mt-3 mb-4 text-center">
    <button type="button" class="btn btn-add-animal" data-bs-toggle="modal" data-bs-target="#newEntryModal">
        <i class="fas fa-plus-circle me-2"></i>Registrar
    </button>
</div>
  
<!-- New Entry Modal -->
<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Comprar Animal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newEntryForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Images and Video -->
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <!-- Image slider for previews -->
                                <div id="newImagePreviewCarousel" class="carousel slide carousel-fade mb-2" data-bs-ride="carousel" data-bs-interval="5200">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img id="newImagePreview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="newImage2Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="newImage3Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                        </div>
                                        <div class="carousel-item">
                                            <video id="newVideoPreview" class="img-thumbnail" controls 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;">
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#newImagePreviewCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#newImagePreviewCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <!-- Upload buttons -->
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="m-1">
                                        <label for="newImageUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 1
                                        </label>
                                        <input type="file" class="d-none" id="newImageUpload" name="image"
                                               accept="image/*" onchange="previewImage(event, 'newImagePreview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newImage2Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 2
                                        </label>
                                        <input type="file" class="d-none" id="newImage2Upload" name="image2"
                                               accept="image/*" onchange="previewImage(event, 'newImage2Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newImage3Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 3
                                        </label>
                                        <input type="file" class="d-none" id="newImage3Upload" name="image3"
                                               accept="image/*" onchange="previewImage(event, 'newImage3Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="newVideoUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-video me-1"></i>Video
                                        </label>
                                        <input type="file" class="d-none" id="newVideoUpload" name="video"
                                               accept="video/*" onchange="previewVideo(event, 'newVideoPreview')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Tag ID -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="tagid" id="newTagid" required>
                                        <label for="newTagid">Tag ID</label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un Tag ID válido.
                                        </div>
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nombre" id="newNombre" required>
                                        <label for="newNombre">Nombre</label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un nombre.
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_nacimiento" id="newFechaNacimiento" required>
                                        <label for="newFechaNacimiento">Fecha de Nacimiento</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione una fecha de nacimiento.
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha Compra -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_compra" id="newFechaCompra">
                                        <label for="newFechaCompra">Fecha de Compra</label>
                                    </div>
                                </div>

                                <!-- Sexo -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="genero" id="newGenero" required>
                                            <option value="">Seleccionar</option>
                                            <option value="Macho">Macho</option>
                                            <option value="Hembra">Hembra</option>
                                        </select>
                                        <label for="newGenero">Sexo</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione un sexo.
                                        </div>
                                    </div>
                                </div>

                                <!-- Raza -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="raza" id="newRaza" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_razas = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_razas = "SELECT DISTINCT razas_nombre FROM v_razas ORDER BY razas_nombre";
                                            $result_razas = $conn_razas->query($sql_razas);
                                            while ($row_razas = $result_razas->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_razas['razas_nombre']) . '">' 
                                                    . htmlspecialchars($row_razas['razas_nombre']) . '</option>';
                                            }
                                            $conn_razas->close();
                                            ?>
                                        </select>
                                        <label for="newRaza">Raza</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione una raza.
                                        </div>
                                    </div>
                                </div>                                

                                <!-- Etapa -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="etapa" id="newEtapa" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_etapa = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_etapa = "SELECT DISTINCT etapas_nombre FROM v_etapas ORDER BY etapas_nombre";
                                            $result_etapa = $conn_etapa->query($sql_etapa);
                                            while ($row_etapa = $result_etapa->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_etapa['etapas_nombre']) . '">' 
                                                    . htmlspecialchars($row_etapa['etapas_nombre']) . '</option>';
                                            }
                                            $conn_etapa->close();
                                            ?>
                                        </select>
                                        <label for="newEtapa">Etapa</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione una etapa.
                                        </div>
                                    </div>
                                </div>

                                <!-- Grupo -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="grupo" id="newGrupo" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_grupos = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_grupos = "SELECT DISTINCT grupos_nombre FROM v_grupos ORDER BY grupos_nombre";
                                            $result_grupos = $conn_grupos->query($sql_grupos);
                                            while ($row_grupos = $result_grupos->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_grupos['grupos_nombre']) . '">' 
                                                    . htmlspecialchars($row_grupos['grupos_nombre']) . '</option>';
                                            }
                                            $conn_grupos->close();
                                            ?>
                                        </select>
                                        <label for="newGrupo">Grupo</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione un grupo.
                                        </div>
                                    </div>
                                </div>

                                <!-- Estatus -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="estatus" id="newEstatus" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_estatus = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_estatus = "SELECT DISTINCT estatus_nombre FROM v_estatus ORDER BY estatus_nombre";
                                            $result_estatus = $conn_estatus->query($sql_estatus);
                                            while ($row_estatus = $result_estatus->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_estatus['estatus_nombre']) . '">' 
                                                    . htmlspecialchars($row_estatus['estatus_nombre']) . '</option>';
                                            }
                                            $conn_estatus->close();
                                            ?>
                                        </select>
                                        <label for="newEstatus">Estatus</label>
                                        <div class="invalid-feedback">
                                            Por favor seleccione un estatus.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Peso -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.1" class="form-control" name="peso" id="newPeso" required>
                                <label for="newPeso">Peso</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese un peso.
                                </div>
                            </div>
                        </div>
                        <!-- Precio -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.1" class="form-control" name="precio" id="newPrecio" required>
                                <label for="newPrecio">Precio</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese un precio.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-success" form="newEntryForm">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to preview image
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            if (output) {
                output.src = reader.result;
            }            
            // Show the correct carousel item
            const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
            carouselItems.forEach((item, index) => {
                if (item.querySelector('img') && item.querySelector('img').id === previewId) {
                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
                    if (carousel) {
                        carousel.to(index);
                    }
                }
            });
        };
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Function to preview video
    function previewVideo(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            if (output) {
                const source = output.querySelector('source');
                if (source) {
                    source.src = reader.result;
                    output.load();
                }
                
                // Show video carousel item (last item)
                const carousel = bootstrap.Carousel.getInstance(document.getElementById('newImagePreviewCarousel'));
                if (carousel) {
                    const carouselItems = document.querySelectorAll('#newImagePreviewCarousel .carousel-item');
                    carousel.to(carouselItems.length - 1);
                }
            }
        };
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Initialize NewEntryModal form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Get form element
        const createEntryForm = document.getElementById('newEntryForm');
        const newEntryModal = document.getElementById('newEntryModal');

        if (createEntryForm) {
            // Handle form submission
            createEntryForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission
                
                // Check form validation
                if (!createEntryForm.checkValidity()) {
                    event.stopPropagation();
                    createEntryForm.classList.add('was-validated');
                    return;
                }

                // Create a FormData object from the form
                const formData = new FormData(createEntryForm);

                // Show loading state
                const submitButton = document.querySelector('#newEntryModal .btn-success');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                submitButton.disabled = true;

                // Send the form data using fetch
                fetch('vacuno_compras_create.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Nuevo animal agregado exitosamente.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reset form and close modal
                            createEntryForm.reset();
                            createEntryForm.classList.remove('was-validated');
                            
                            // Reset image previews
                            document.getElementById('newImagePreview').src = './images/default_image.png';
                            document.getElementById('newImage2Preview').src = './images/default_image.png';
                            document.getElementById('newImage3Preview').src = './images/default_image.png';
                            const videoPreview = document.getElementById('newVideoPreview');
                            if (videoPreview && videoPreview.querySelector('source')) {
                                videoPreview.querySelector('source').src = '';
                                videoPreview.load();
                            }
                            
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(newEntryModal);
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Reload page to show new entry
                            location.reload();
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al agregar el nuevo animal.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.'
                    });
                })
                .finally(() => {
                    // Restore button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });
        }

        // Initialize carousel when modal is shown
        newEntryModal.addEventListener('shown.bs.modal', function() {
            new bootstrap.Carousel(document.getElementById('newImagePreviewCarousel'), {
                interval: 5200
            });
        });
    });
</script>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateModalLabel">
                    <i class="fas fa-edit me-2"></i>Actualizar Animal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Images and Video -->
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <!-- Image slider for previews -->
                                <div id="updateImagePreviewCarousel" class="carousel slide carousel-fade mb-2" data-bs-ride="carousel" data-bs-interval="5200">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img id="updateImagePreview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="updateImage2Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <img id="updateImage3Preview" src="./images/default_image.png" 
                                                class="img-thumbnail" alt="Preview" 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreen(this.src)">
                                        </div>
                                        <div class="carousel-item">
                                            <video id="updateVideoPreview" class="img-thumbnail" controls 
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openFullscreenVideo(this)">
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#updateImagePreviewCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#updateImagePreviewCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <!-- Upload buttons -->
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="m-1">
                                        <label for="updateImageUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 1
                                        </label>
                                        <input type="file" class="d-none" id="updateImageUpload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImagePreview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateImage2Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 2
                                        </label>
                                        <input type="file" class="d-none" id="updateImage2Upload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImage2Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateImage3Upload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-image me-1"></i>Imagen 3
                                        </label>
                                        <input type="file" class="d-none" id="updateImage3Upload" 
                                               accept="image/*" onchange="previewImage(event, 'updateImage3Preview')">
                                    </div>
                                    <div class="m-1">
                                        <label for="updateVideoUpload" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-video me-1"></i>Video
                                        </label>
                                        <input type="file" class="d-none" id="updateVideoUpload" 
                                               accept="video/*" onchange="previewVideo(event, 'updateVideoPreview')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <!-- Tag ID -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="tagid" id="updateTagid" required readonly>
                                        <label for="updateTagid">Tag ID</label>
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nombre" id="updateNombre" required>
                                        <label for="updateNombre">Nombre</label>
                                    </div>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_nacimiento" id="updateFechaNacimiento" required>
                                        <label for="updateFechaNacimiento">Fecha de Nacimiento</label>
                                    </div>
                                </div>

                                <!-- Fecha Compra -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="fecha_compra" id="updateFechaCompra">
                                        <label for="updateFechaCompra">Fecha de Compra</label>
                                    </div>
                                </div>

                                <!-- Sexo -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="genero" id="updateGenero" required>
                                            <option value="">Seleccionar</option>
                                            <option value="Macho">Macho</option>
                                            <option value="Hembra">Hembra</option>
                                        </select>
                                        <label for="updateGenero">Sexo</label>
                                    </div>
                                </div>

                                <!-- Raza -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="raza" id="updateRaza" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_razas = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_razas = "SELECT DISTINCT razas_nombre FROM v_razas ORDER BY razas_nombre";
                                            $result_razas = $conn_razas->query($sql_razas);
                                            while ($row_razas = $result_razas->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_razas['razas_nombre']) . '">' 
                                                    . htmlspecialchars($row_razas['razas_nombre']) . '</option>';
                                            }
                                            $conn_razas->close();
                                            ?>
                                        </select>
                                        <label for="updateRaza">Raza</label>
                                    </div>
                                </div>                                

                                <!-- Etapa -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="etapa" id="updateEtapa" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_etapa = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_etapa = "SELECT DISTINCT etapa FROM vacuno ORDER BY etapa";
                                            $result_etapa = $conn_etapa->query($sql_etapa);
                                            while ($row_etapa = $result_etapa->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_etapa['etapa']) . '">' 
                                                    . htmlspecialchars($row_etapa['etapa']) . '</option>';
                                            }
                                            $conn_etapa->close();
                                            ?>
                                        </select>
                                        <label for="updateEtapa">Etapa</label>
                                    </div>
                                </div>

                                <!-- Grupo -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="grupo" id="updateGrupo" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_grupos = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_grupos = "SELECT DISTINCT grupos_nombre FROM v_grupos ORDER BY grupos_nombre";
                                            $result_grupos = $conn_grupos->query($sql_grupos);
                                            while ($row_grupos = $result_grupos->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_grupos['grupos_nombre']) . '">' 
                                                    . htmlspecialchars($row_grupos['grupos_nombre']) . '</option>';
                                            }
                                            $conn_grupos->close();
                                            ?>
                                        </select>
                                        <label for="updateGrupo">Grupo</label>
                                    </div>
                                </div>

                                <!-- Estatus -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="estatus" id="updateEstatus" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $conn_estatus = new mysqli('localhost', $username, $password, $dbname);
                                            $sql_estatus = "SELECT DISTINCT estatus_nombre FROM v_estatus ORDER BY estatus_nombre";
                                            $result_estatus = $conn_estatus->query($sql_estatus);
                                            while ($row_estatus = $result_estatus->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_estatus['estatus_nombre']) . '">' 
                                                    . htmlspecialchars($row_estatus['estatus_nombre']) . '</option>';
                                            }
                                            $conn_estatus->close();
                                            ?>
                                        </select>
                                        <label for="updateEstatus">Estatus</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Peso -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.1" class="form-control" name="peso" id="updatePeso" required>
                                <label for="updatePeso">Peso</label>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.1" class="form-control" name="precio" id="updatePrecio" required>
                                <label for="updatePrecio">Precio</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-outline-success" onclick="saveUpdates()">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Save Update Modal -->
<script>
function saveUpdates() {
    // Get the form
    const form = document.getElementById('updateForm');
    if (!form) {
        console.error('Update form not found');
            return;
        }

    // Create FormData object from the form
    const formData = new FormData(form);
    
    // Add any fields that might not be in the form or need specific handling
    const tagid = document.getElementById('updateTagid');
    if (tagid) {
        formData.append('tagid', tagid.value); // Ensure tagid is explicitly added
    }
    // Add peso and precio from the modal inputs
    const peso = document.getElementById('updatePeso');
    if (peso) {
        formData.append('peso_compra', peso.value); // Use the correct name expected by update script
    }
    const precio = document.getElementById('updatePrecio');
    if (precio) {
        formData.append('precio_compra', precio.value); // Use the correct name expected by update script
    }

    // Add image files if selected
    const imageFile = document.getElementById('updateImageUpload').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    // Add image2 file if selected
    const image2File = document.getElementById('updateImage2Upload').files[0];
    if (image2File) {
        formData.append('image2', image2File);
    }
    
    // Add image3 file if selected
    const image3File = document.getElementById('updateImage3Upload').files[0];
    if (image3File) {
        formData.append('image3', image3File);
    }
    
    // Add video file if selected
    const videoFile = document.getElementById('updateVideoUpload').files[0];
    if (videoFile) {
        formData.append('video', videoFile);
    }

    // Show loading state
    const saveButton = document.querySelector('#updateModal .btn-outline-success');
    if (!saveButton) {
        console.error('Save button not found');
            return;
        }
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
    saveButton.disabled = true;

    // Send the update request
        $.ajax({
        url: 'vacuno_update.php',
            type: 'POST',
        data: formData,
        processData: false,  // Important for FormData
        contentType: false,  // Important for FormData
        cache: false,        // Prevent caching
        timeout: 30000,      // Increased timeout for larger files
            success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Los datos han sido actualizados exitosamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Close modal and refresh page
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        if (modal) {
                            modal.hide();
                        }
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Hubo un error al actualizar los datos.'
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar la respuesta del servidor.'
                });
                }
            },
            error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            let errorMessage = 'Hubo un error al enviar los datos';
            if (status === 'timeout') {
                errorMessage = 'La solicitud tardó demasiado tiempo. Por favor, intente de nuevo.';
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        },
        complete: function() {
            // Restore button state
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        }
    });
}

// Add form validation before submission
document.getElementById('updateModal').addEventListener('shown.bs.modal', function () {
    const form = this.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

  <!-- DataTable for vh_compra records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="compraTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Imagen</th>
                      <th class="text-center">Acciones</th>                      
                      <th class="text-center">Nombre</th> 
                      <th class="text-center">Fecha</th>                   
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Precio</th>
                      <th class="text-center">Peso</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get purchase data directly from vacuno table
                      $compraQuery = "SELECT 
                                        id, 
                                        DATE_FORMAT(fecha_compra, '%Y-%m-%d') as fecha_compra_formatted, 
                                        nombre as animal_nombre, 
                                        tagid, 
                                        precio_compra, 
                                        peso_compra, 
                                        image as animal_image 
                                    FROM vacuno 
                                    WHERE fecha_compra != '0000-00-00'
                                    ORDER BY fecha_compra DESC"; // Order by actual purchase date
                                
                      $stmt = $conn->prepare($compraQuery);  
                      $stmt->execute();
                      $compraData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      
                      // If no data, display a message
                      if (empty($compraData)) {
                          echo "<tr><td colspan='8' class='text-center'>No hay registros de compra disponibles</td></tr>"; // Updated colspan to 8
                      } else {
                          // The foreach loop below will handle rendering the rows
                      }
                  } catch (PDOException $e) {
                      error_log("Error in compra table data fetching: " . $e->getMessage());
                      echo "<tr><td colspan='8' class='text-center'>Error al cargar los datos de compra: " . $e->getMessage() . "</td></tr>"; // Updated colspan to 8
                  }

                  // Ensure $compraData is iterable even if the try block failed or returned empty
                  if (!isset($compraData)) {
                      $compraData = []; 
                  }

                  foreach ($compraData as $row) {
                      // Determine image path
                      $imagePath = './images/default_image.png'; // Default image
                      if (!empty($row['animal_image'])) {
                          $imagePath = './' . htmlspecialchars($row['animal_image']); // Use animal's image
                      }

                      // Render row using vacuno table columns
                      echo "<tr>";
                      echo '<td class="text-center"><img src="' . $imagePath . '" alt="Animal Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>';
                      echo '<td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit-compra" 
                                    data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                    data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '"
                                    data-fecha="' . htmlspecialchars($row['fecha_compra_formatted'] ?? '') . '"
                                    data-precio="' . htmlspecialchars($row['precio_compra'] ?? '') . '"
                                    data-peso="' . htmlspecialchars($row['peso_compra'] ?? '') . '">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-compra" 
                                    data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                    data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>';
                      
                      echo "<td class='text-center'>" . htmlspecialchars($row['animal_nombre'] ?? 'N/A') . "</td>"; // vacuno.nombre                      
                      echo "<td class='text-center'>" . htmlspecialchars($row['fecha_compra_formatted'] ?? 'N/A') . "</td>"; // Use formatted fecha_compra
                      echo "<td class='text-center'>" . htmlspecialchars($row['tagid'] ?? '') . "</td>"; // vacuno.tagid
                      echo "<td class='text-center'>" . htmlspecialchars($row['precio_compra'] ?? '0.00') . "</td>"; // vacuno.precio_compra
                      echo "<td class='text-center'>" . htmlspecialchars($row['peso_compra'] ?? '0.00') . "</td>"; // vacuno.peso_compra
                      echo "</tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Monthly Purchase Value Chart -->
<div class="container chart-container mb-4">
    <canvas id="monthlyPurchaseChart"></canvas>
</div>

<!-- Cumulative Investment Chart -->
<div class="container chart-container mb-4">
    <canvas id="cumulativeInvestmentChart"></canvas>
</div>

<!-- Initialize DataTable for VH compra -->
<script>
$(document).ready(function() {
    $('#compraTable').DataTable({
        // Set initial page length
        pageLength: 5,
        
        // Configure length menu options
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            [5, 10, 25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[3, 'desc']],
        
        // Spanish language
        language: {
            url: './es-ES.json',
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        
        // Enable responsive features
        responsive: true,
        
        // Configure DOM layout and buttons
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        
        buttons: [
            {
                extend: 'collection',
                text: 'Exportar',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
                ]
            }
        ],
        
        // Column specific settings
        columnDefs: [
            {
                targets: [0], // New Image column
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Assuming the image path is directly in the data or calculated in PHP
                    // The PHP code already renders the <img> tag, so we just return the cell content
                    return data; 
                }
            },
            {
                targets: [5, 6], // Adjusted: Precio (now 5), Peso (now 6) columns
                render: function(data, type, row) {
                    if (type === 'display') {
                        return parseFloat(data).toLocaleString('es-ES', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    return data;
                }
            },
            {
                targets: [3], // Adjusted: Fecha column (now 3)
                render: function(data, type, row) {
                    if (type === 'display') {
                        // Parse the date parts manually to avoid timezone issues
                        if (data) {
                            // Split the date string (format: YYYY-MM-DD)
                            var parts = data.split('-');
                            // Create date string in local format (DD/MM/YYYY)
                            if (parts.length === 3) {
                                return parts[2] + '/' + parts[1] + '/' + parts[0];
                            }
                        }
                        return data; // Return original if parsing fails
                    }
                    return data;
                }
            },
            {
                targets: [1], // Adjusted: Actions column (now 1)
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Handle new entry form submission
    $('#saveNewCompra').click(function() {
        // Validate the form
        var form = document.getElementById('newCompraForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            precio: $('#new_precio').val(),
            peso: $('#new_peso').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la compra para el animal con Tag ID ${formData.tagid}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espere mientras se procesa la información',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to insert the record
                $.ajax({
                    url: 'process_compra.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        precio: formData.precio,
                        peso: formData.peso,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newCompraModal'));
                        modal.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de compra ha sido guardado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Reload the page to show updated data
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

    // Handle edit button click
    $('.edit-compra').click(function() {
        var button = $(this); // Store reference to the button
        var tagid = button.data('tagid');
        var fechaCompra = button.data('fecha'); // Get the purchase date
        
        // Show loading indicator (optional)
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Fetch animal details via AJAX
        $.ajax({
            url: 'vacuno_get_details.php', // Assuming this endpoint exists
            type: 'GET',
            data: { tagid: tagid },
            dataType: 'json',
            success: function(animalData) {
                if (animalData && animalData.success) {
                    const data = animalData.data;
                    // Populate the #updateModal fields
                    $('#updateTagid').val(data.tagid);
                    $('#updateNombre').val(data.nombre);
                    $('#updateFechaNacimiento').val(data.fecha_nacimiento);
                    $('#updateFechaCompra').val(fechaCompra); // Use the specific purchase date from the button
                    $('#updateGenero').val(data.genero);
                    $('#updateRaza').val(data.raza);
                    $('#updateEtapa').val(data.etapa);
                    $('#updateGrupo').val(data.grupo);
                    $('#updateEstatus').val(data.estatus);
                    $('#updatePeso').val(data.peso_compra); // Populate peso_compra
                    $('#updatePrecio').val(data.precio_compra); // Populate precio_compra

                    // Reset and Populate image/video previews (adjust paths as needed)
                    const basePath = './'; // Adjust if your image/video paths are different
                    $('#updateImagePreview').attr('src', data.image ? basePath + data.image : './images/default_image.png');
                    $('#updateImage2Preview').attr('src', data.image2 ? basePath + data.image2 : './images/default_image.png');
                    $('#updateImage3Preview').attr('src', data.image3 ? basePath + data.image3 : './images/default_image.png');
                    const videoPreview = $('#updateVideoPreview');
                    const videoSource = videoPreview.find('source');
                    if (data.video) {
                        videoSource.attr('src', basePath + data.video);
                        videoPreview[0].load(); // Reload the video element
                        videoPreview.show();
                    } else {
                        videoSource.attr('src', '');
                        videoPreview[0].load();
                         // Optionally hide video preview if no video
                         // videoPreview.hide(); 
                    }
                     // Ensure carousel starts at the first image
                    $('#updateImagePreviewCarousel').carousel(0); 


                    // Get the modal instance and show it
                    var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
                    updateModal.show();

                } else {
                    Swal.fire(
                        'Error',
                        animalData.message || 'No se pudieron obtener los detalles del animal.',
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error fetching animal details: ", status, error);
                Swal.fire(
                    'Error',
                    'Hubo un problema al conectar con el servidor para obtener los detalles.',
                    'error'
                );
            },
            complete: function() {
                 // Restore button state
                button.prop('disabled', false).html('<i class="fas fa-edit"></i>');
            }
        });
    });
    
    // Handle delete button click
    $('.delete-compra').click(function() {
        // var id = $(this).data('id'); // Old ID is irrelevant
        var tagid = $(this).data('tagid');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro de animal?', // Updated title
            text: `¿Está seguro de que desea eliminar TODA la información del animal con Tag ID ${tagid}? Esta acción no se puede deshacer y eliminará al animal del inventario.`, // Updated text
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar animal', // Updated text
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere mientras se elimina el animal',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send AJAX request to delete the vacuno record
                $.ajax({
                    url: 'vacuno_delete.php', // Target the vacuno delete script
                    type: 'POST',
                    data: {
                        action: 'delete', // Action might be implicit in the script
                        tagid: tagid      // Send tagid instead of id
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        if (response.success) {
                             // Show success message
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.message || 'El animal ha sido eliminado correctamente.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        } else {
                             Swal.fire({
                                title: 'Error',
                                text: response.message || 'No se pudo eliminar el animal.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                       
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud de eliminación';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    },
                    complete: function() {
                         // Optional: Hide loading state if it wasn't handled by Swal
                    }
                });
            }
        });
    });
});
</script>

<!-- JavaScript for Monthly Purchase Value Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxMonthly = document.getElementById('monthlyPurchaseChart').getContext('2d');
    const monthlyLabels = <?php echo $monthlyValueLabelsJson; ?>;
    const monthlyData = <?php echo $monthlyValueDataJson; ?>;

    const monthlyGradient = ctxMonthly.createLinearGradient(0, 0, 0, 400);
    monthlyGradient.addColorStop(0, 'rgba(75, 192, 192, 0.6)'); 
    monthlyGradient.addColorStop(1, 'rgba(75, 192, 192, 0.1)');

    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Valor Total Compras por Mes',
                data: monthlyData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: monthlyGradient,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(75, 192, 192)',
                pointBorderColor: '#000',
                pointHoverBackgroundColor: '#000',
                pointHoverBorderColor: 'rgb(75, 192, 192)',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#000', font: { size: 14 } }
                },
                title: {
                    display: true,
                    text: 'Valor Mensual de Compras ($)',
                    color: '#000',
                    font: { size: 18, weight: 'bold' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 4,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += '$' + context.parsed.y.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Meses (Año-Mes)', color: '#000', font: { size: 14, weight: 'bold' } },
                    ticks: { color: '#000', font: { size: 12 } },
                    grid: { color: 'rgba(38, 38, 38, 0.1)', drawBorder: true }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Valor Total ($)', color: '#000', font: { size: 14, weight: 'bold' } },
                    ticks: {
                        color: '#000',
                        font: { size: 12 },
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString('es-ES');
                        }
                    },
                    grid: { color: 'rgba(38, 38, 38, 0.1)', drawBorder: false }
                }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
});
</script>

<!-- JavaScript for Cumulative Investment Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxCumulative = document.getElementById('cumulativeInvestmentChart').getContext('2d');
    const cumulativeLabels = <?php echo $cumulativeLabelsJson; ?>;
    const cumulativeData = <?php echo $cumulativeDataJson; ?>;

    const cumulativeGradient = ctxCumulative.createLinearGradient(0, 0, 0, 400);
    cumulativeGradient.addColorStop(0, 'rgba(40, 167, 69, 0.6)'); // Green gradient
    cumulativeGradient.addColorStop(1, 'rgba(40, 167, 69, 0.1)');

    new Chart(ctxCumulative, {
        type: 'line',
        data: {
            labels: cumulativeLabels,
            datasets: [{
                label: 'Inversión Acumulada en Compras',
                data: cumulativeData,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: cumulativeGradient,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(40, 167, 69)',
                pointBorderColor: '#000',
                pointHoverBackgroundColor: '#000',
                pointHoverBorderColor: 'rgb(40, 167, 69)',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#000', font: { size: 14 } }
                },
                title: {
                    display: true,
                    text: 'Inversión Acumulada en Compras ($)',
                    color: '#000',
                    font: { size: 18, weight: 'bold' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 4,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += '$' + context.parsed.y.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Meses (Año-Mes)', color: '#000', font: { size: 14, weight: 'bold' } },
                    ticks: { color: '#000', font: { size: 12 } },
                    grid: { color: 'rgba(38, 38, 38, 0.1)', drawBorder: true }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Inversión Acumulada ($)', color: '#000', font: { size: 14, weight: 'bold' } },
                    ticks: {
                        color: '#000',
                        font: { size: 12 },
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString('es-ES');
                        }
                    },
                    grid: { color: 'rgba(38, 38, 38, 0.1)', drawBorder: true }
                }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
});
</script>

