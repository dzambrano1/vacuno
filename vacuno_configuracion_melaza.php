<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vacuno Configuracion Melaza</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/default_image.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap 5.3.2 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- DataTables 1.13.7 / Responsive 2.5.0 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<!-- DataTables Buttons 2.4.1 -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="./vacuno.css">

<style>
/* Professional form styling with left-justified alignment */
.form-label {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    text-align: left;
}

.form-control,
.form-select {
    text-align: left !important;
    padding: 0.75rem 1rem !important;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #ffffff;
    height: auto;
    min-height: 48px;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    outline: none;
    background-color: #ffffff;
}

.form-control::placeholder {
    color: #adb5bd;
    font-style: italic;
}

/* Modal styling */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 12px 12px 0 0;
    border-bottom: none;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1.5rem;
}

/* Form spacing and layout */
.mb-4 {
    margin-bottom: 1.5rem !important;
}

.mb-2 {
    margin-bottom: 1rem !important;
}

/* Button styling */
.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    border: none;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

/* Icon styling in labels */
.form-label i {
    color: #007bff;
    width: 20px;
    text-align: center;
}

/* Input focus animation */
.form-control:focus,
.form-select:focus {
    transform: translateY(-1px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-body {
        padding: 1.5rem;
    }
    
    .form-control,
    .form-select {
        padding: 0.625rem 0.875rem !important;
        min-height: 44px;
    }
}
</style>

<!-- JS -->
<!-- jQuery 3.7.0 -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<!-- Bootstrap 5.3.2 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables 1.13.7 / Responsive 2.5.0 -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- DataTables Buttons 2.4.1 -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

</head>
<body>

<!-- Navigation Title -->
<nav class="navbar text-center" style="border: none !important; box-shadow: none !important;">
    <!-- Title Row -->
    <div class="container-fluid">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-between align-items-center position-relative">
                <!-- Botón de Volver -->
                <button type="button" onclick="window.location.href='./inventario_vacuno.php'" class="btn" style="color:white; border: none; border-radius: 8px; padding: 8px 15px; z-index: 1050; position: relative;" title="Volver al Paso 1">
                    <i class="fas fa-arrow-left"></i> << Paso 1
                </button>
                
                <!-- Título centrado -->
                <h1 class="navbar-title text-center position-absolute" style="left: 50%; transform: translateX(-50%); z-index: 1;">
                    <i class="fas fa-clipboard-list me-2"></i>LA GRANJA DE TITO<span class="ms-2"><i class="fas fa-file-medical"></i></span>
                </h1>
                
                <!-- Botón de Salir -->
                <button type="button" onclick="window.location.href='../inicio.php'" class="btn" style="color: white; border: none; border-radius: 8px; padding: 8px 15px; z-index: 1050; position: relative;" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i> 
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Subtitle - 3 Steps Guide -->
<style>
.arrow-step {
    position: relative;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    padding: 20px 30px;
    margin: 0 10px;
    clip-path: polygon(0% 0%, calc(100% - 30px) 0%, 100% 50%, calc(100% - 30px) 100%, 0% 100%, 30px 50%);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    min-height: 108px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity: 0.7;
    transition: all 0.3s ease;
    cursor: pointer;
}

.arrow-step:hover:not(.arrow-step-active) {
    opacity: 0.9;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}

.arrow-step-active {
    background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%) !important;
    opacity: 1 !important;
    box-shadow: 0 8px 25px rgba(32, 201, 151, 0.5) !important;
    transform: scale(1.05);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 8px 25px rgba(32, 201, 151, 0.5);
    }
    50% {
        box-shadow: 0 8px 35px rgba(32, 201, 151, 0.8);
    }
}

.arrow-step-first {
    clip-path: polygon(0% 0%, calc(100% - 30px) 0%, 100% 50%, calc(100% - 30px) 100%, 0% 100%);
    border-radius: 10px 0 0 10px;
}

.arrow-step-last {
    clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 30px 50%);
    border-radius: 0 10px 10px 0;
}

.badge-active {
    position: absolute;
    top: 10px;
    right: 20px;
    background: #ffc107;
    color: #000;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@media (max-width: 768px) {
    .arrow-step, .arrow-step-first, .arrow-step-last {
        clip-path: none !important;
        border-radius: 10px !important;
        margin: 10px 0;
    }
    .badge-active {
        right: 10px;
    }
}
</style>

<div class="container-fluid mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="d-flex justify-content-center">
                <div class="arrow-step arrow-step-active w-100" style="border-radius: 15px; clip-path: none;">
                    <span class="badge-active">🎯 Estás configurando Melaza</span>
                    <div style="background: white; color: #17a2b8; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 2rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1.1rem;">CONFIGURACIÓN</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add back button before the header container -->
<a href="./vacuno_configuracion.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>

<!-- New Entry Modal Configuracion Melaza -->

<!-- Add New Vacuna Melaza Button -->
<div class="container my-3 text-center">
  <button type="button" class="btn btn-success text-center" data-bs-toggle="modal" data-bs-target="#newEntryModal">
    <i class="fas fa-plus-circle me-2"></i>Registrar
  </button>
</div>

<div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="newEntryModalLabel">
                  <i class="fas fa-plus-circle me-2"></i>Configurar Nueva Melaza
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
               <form id="newMelazaForm">
                   <input type="hidden" id="new_id" name="id" value="">
                   
                   <div class="mb-4">
                       <label for="new_melaza" class="form-label fw-bold">
                           <i class="fa-solid fa-syringe me-2"></i>Melaza Producto
                       </label>
                       <input type="text" class="form-control" id="new_melaza" name="melaza" placeholder="Ingrese el nombre del producto" required>
                   </div>
                   
                   <div class="mb-4">
                       <label for="new_etapa" class="form-label fw-bold">
                           <i class="fa-solid fa-syringe me-2"></i>Etapa
                       </label>
                       <select class="form-select" id="new_etapa" name="etapa" required>
                           <option value="">Seleccionar etapa</option>
                           <?php
                           $sql_etapas = "SELECT DISTINCT vc_etapas_nombre FROM vc_etapas ORDER BY vc_etapas_nombre ASC";
                           $stmt_etapas = $conn->prepare($sql_etapas);
                           $stmt_etapas->execute();
                           $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                           foreach ($etapas as $etapa_row) {
                               echo '<option value="' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '</option>';
                           }
                           ?>
                       </select>
                   </div>
                   
                   <div class="mb-4">
                       <label for="new_racion" class="form-label fw-bold">
                           <i class="fa-solid fa-eye-dropper me-2"></i>Ración (Kg)
                       </label>
                       <input type="number" step="0.01" class="form-control" id="new_racion" name="racion" placeholder="0.00" required>
                   </div>
                   
                   <div class="mb-4">
                       <label for="new_costo" class="form-label fw-bold">
                           <i class="fa-solid fa-money-bill-1-wave me-2"></i>Costo ($)
                       </label>
                       <input type="number" step="0.01" class="form-control" id="new_costo" name="costo" placeholder="0.00" required>
                   </div>
                   
                   <div class="mb-4">
                       <label for="new_vigencia" class="form-label fw-bold">
                           <i class="fa-solid fa-calendar-days me-2"></i>Vigencia (días)
                       </label>
                       <input type="number" class="form-control" id="new_vigencia" name="vigencia" placeholder="0" required>
                   </div>
               </form>
          </div>
          <div class="modal-footer btn-group">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i>Cancelar
              </button>
              <button type="button" class="btn btn-success" id="saveNewMelaza">
                  <i class="fas fa-save me-1"></i>Guardar
              </button>
          </div>
      </div>
  </div>
</div>
  
  <!-- DataTable for vh_melaza records -->
  
<div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="melazaTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">Melaza Producto</th>
                    <th class="text-center">Etapa</th>
                    <th class="text-center">Racion (Kg)</th>
                    <th class="text-center">Costo ($)</th>
                    <th class="text-center">Vigencia (dias)</th>
                    <th class="text-center" style="width: 10%;">Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                      $melazaQuery = "SELECT * FROM vc_melaza";

                      $stmt = $conn->prepare($melazaQuery);
                      $stmt->execute();
                      $melazasData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      if (empty($melazasData)) {
                          echo "<tr><td colspan='6' class='text-center'>No hay registros disponibles</td></tr>";
                      } else {
                          foreach ($melazasData as $row) {
                              echo "<tr>";                              
                              
                              // Column 0: Producto
                              echo "<td>" . htmlspecialchars($row['vc_melaza_nombre'] ?? '') . "</td>";
                              // Columna 1: Etapa
                              echo "<td>" . htmlspecialchars($row['vc_melaza_etapa'] ?? '') . "</td>";
                              
                              // Column 2: Racion
                              echo "<td>" . htmlspecialchars($row['vc_melaza_racion'] ?? 'N/A') . "</td>";
                              
                              // Column 3: Costo
                              echo "<td>" . htmlspecialchars($row['vc_melaza_costo'] ?? 'N/A') . "</td>";
                              
                              // Column 4: Vigencia
                              echo "<td>" . htmlspecialchars($row['vc_melaza_vigencia'] ?? 'N/A') . "</td>";

                              // Column 5: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              echo '        <button class="btn btn-warning edit-melaza" style="height: 40px !important; width: 40px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                              data-id="' . htmlspecialchars($row['id'] ?? '') . '" 
                                              data-melaza="' . htmlspecialchars($row['vc_melaza_nombre'] ?? '') . '" 
                                              data-etapa="' . htmlspecialchars($row['vc_melaza_etapa'] ?? '') . '" 
                                              data-racion="' . htmlspecialchars($row['vc_melaza_racion'] ?? '') . '" 
                                              data-costo="' . htmlspecialchars($row['vc_melaza_costo'] ?? '') . '" 
                                              data-vigencia="' . htmlspecialchars($row['vc_melaza_vigencia'] ?? '') . '"
                                              title="Editar Configuracion Vacuna Melaza">
                                              <i class="fas fa-edit"></i>
                                          </button>';
                              echo '        <button class="btn btn-danger delete-melaza" style="height: 40px !important; width: 40px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                              data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                              title="Eliminar Configuracion Vacuna Melaza">
                                              <i class="fas fa-trash"></i>
                                          </button>';
                              echo '    </div>';
                              echo '</td>';
                              

                              echo "</tr>";
                          }
                      }
                  ?>
              </tbody>
          </table>
      </div>
</div>


<!-- Initialize DataTable for VH melaza -->
<script>
$(document).ready(function() {
    // Check if table has actual data rows (excluding the "no data" message row)
    var table = $('#melazaTable');
    var tbody = table.find('tbody');
    var hasData = false;
    
    // Check if there are any rows with actual data (not the "No hay registros disponibles" row)
    tbody.find('tr').each(function() {
        var row = $(this);
        var firstCell = row.find('td:first');
        // If the first cell doesn't contain "No hay registros disponibles", it's a data row
        if (firstCell.text().trim() !== 'No hay registros disponibles') {
            hasData = true;
            return false; // Break the loop
        }
    });
    
    if (hasData) {
        // Initialize DataTable only if there's actual data
        try {
            $('#melazaTable').DataTable({
                // Set initial page length
                pageLength: 5,
                
                // Configure length menu options
                lengthMenu: [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, "Todos"]
                ],
                
                // Order by Vigencia column descending (column index 5)
                order: [[5, 'desc']],
                
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
                
                // Column specific settings - Updated indices
                columnDefs: [
                     {
                         targets: [5], // Actions column
                         orderable: false,
                         searchable: false
                     },
                    {
                    targets: [0, 1, 2, 3], // Producto, Etapa, Racion, Costo columns
                        render: function(data, type, row) {
                            if (type === 'display' && data !== 'N/A' && data !== 'No Registrado') {
                                // Attempt to parse only if data looks like a number
                                 const num = parseFloat(data);
                                 if (!isNaN(num)) {
                                     return num.toLocaleString('es-ES', {
                                         minimumFractionDigits: 2,
                                         maximumFractionDigits: 2
                                     });
                                 }
                            }
                            return data; // Return original data if not display or not a valid number
                        }
                    },
                    {
                        targets: [4], // Vigencia column
                        orderable: true,
                        searchable: true
                    }
                ]
            });
        } catch (error) {
            console.warn('DataTable initialization failed:', error);
            // Fallback to basic styling
            table.addClass('table-striped table-bordered');
        }
    } else {
        // If no data, just add basic styling without DataTable
        table.addClass('table-striped table-bordered');
        console.log('No data found, DataTable not initialized');
    }
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // --- Initialize Modals Once --- 
    var newEntryModalElement = document.getElementById('newEntryModal');
    var newEntryModalInstance = new bootstrap.Modal(newEntryModalElement); 
    // Note: editMelazaModal is created dynamically later, so no need to initialize here.

    // Handle new entry form submission
    $('#saveNewMelaza').click(function() {
        // Validate the form
        var form = document.getElementById('newMelazaForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            melaza: $('#new_melaza').val(),
            etapa: $('#new_etapa').val(),
            racion: $('#new_racion').val(),
            costo: $('#new_costo').val(),
            vigencia: $('#new_vigencia').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar el alimento ${formData.melaza} con ración de ${formData.racion} kg?`,
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
                    url: 'process_configuracion_melaza.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        melaza: formData.melaza,
                        etapa: formData.etapa,
                        racion: formData.racion,
                        costo: formData.costo,
                        vigencia: formData.vigencia
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        // Close the modal
                        newEntryModalInstance.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de melaza ha sido guardado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Reload the page to show updated data
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', xhr, status, error);
                        console.log('Request data:', {
                            action: 'insert',
                            melaza: formData.melaza,
                            etapa: formData.etapa,
                            racion: formData.racion,
                            costo: formData.costo,
                            vigencia: formData.vigencia
                        });
                        
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Error response:', response);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
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
    $('.edit-melaza').click(function() {
        var id = $(this).data('id');
        var melaza = $(this).data('melaza');
        var etapa = $(this).data('etapa');
        var racion = $(this).data('racion');
        var costo = $(this).data('costo');
        var vigencia = $(this).data('vigencia');

        console.log('Edit button clicked. Record ID captured:', id); // Debug log 1
        
        // Simple check if ID is missing before creating modal
        if (!id) {
             console.error('Attempting to edit a record with a missing ID.');
             Swal.fire({
                 title: 'Error',
                 text: 'No se puede editar este registro porque falta el ID.',
                 icon: 'error',
                 confirmButtonColor: '#dc3545'
             });
             return; // Stop execution if ID is missing
        }

        // Edit Configuracion Melaza Modal dialog for editing
        var modalHtml = `
        <div class="modal fade" id="editMelazaModal" tabindex="-1" aria-labelledby="editMelazaModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMelazaModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Melaza
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editMelazaForm">
                            <input type="hidden" id="edit_id" name="id" value="${id}">
                            
                            <div class="mb-4">
                                <label for="edit_melaza" class="form-label fw-bold">
                                    <i class="fas fa-syringe me-2"></i>Melaza
                                </label>
                                <select class="form-select" id="edit_melaza" name="melaza" required>
                                    <option value="">Seleccionar melaza</option>
                                    <?php
                                    // Fetch distinct names from the database
                                    $sql_names = "SELECT DISTINCT vc_melaza_nombre FROM vc_melaza ORDER BY vc_melaza_nombre ASC";
                                    $stmt_names = $conn->prepare($sql_names);
                                    $stmt_names->execute();
                                    $names = $stmt_names->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($names as $name_row) {
                                        echo '<option value="' . htmlspecialchars($name_row['vc_melaza_nombre']) . '">' . htmlspecialchars($name_row['vc_melaza_nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="edit_etapa" class="form-label fw-bold">
                                    <i class="fas fa-syringe me-2"></i>Etapa
                                </label>
                                <select class="form-select" id="edit_etapa" name="etapa" required>
                                    <option value="">Seleccionar etapa</option>
                                    <?php
                                    $sql_etapas = "SELECT DISTINCT vc_etapas_nombre FROM vc_etapas ORDER BY vc_etapas_nombre ASC";
                                    $stmt_etapas = $conn->prepare($sql_etapas);
                                    $stmt_etapas->execute();
                                    $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($etapas as $etapa_row) {
                                        echo '<option value="' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="edit_racion" class="form-label fw-bold">
                                    <i class="fa-solid fa-eye-dropper me-2"></i>Ración (Kg)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="edit_racion" name="racion" value="${racion}" placeholder="0.00" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="edit_costo" class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign me-2"></i>Costo ($)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="edit_costo" name="costo" value="${costo}" placeholder="0.00" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="edit_vigencia" class="form-label fw-bold">
                                    <i class="fas fa-calendar-days me-2"></i>Vigencia (días)
                                </label>
                                <input type="number" class="form-control" id="edit_vigencia" name="vigencia" value="${vigencia}" placeholder="0" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditMelaza">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editMelazaModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editMelazaModal'));
        editModal.show();
        
        // Set the selected value for the etapa field after the modal is shown
        setTimeout(function() {
            $('#edit_etapa').val(etapa);
            $('#edit_melaza').val(melaza);
        }, 100);
        
        // Handle save button click
        $('#saveEditMelaza').click(function() {
            // Create a form object to properly validate
            var form = document.getElementById('editMelazaForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            var formData = {
                id: $('#edit_id').val(),
                melaza: $('#edit_melaza').val(),
                etapa: $('#edit_etapa').val(),
                racion: $('#edit_racion').val(),
                costo: $('#edit_costo').val(),
                vigencia: $('#edit_vigencia').val()
            };
            
            console.log('Save changes clicked. Form Data being sent:', formData); // Debug log 2
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar la configuracion de melaza?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espere mientras se procesa la información',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send AJAX request to update the record
                    $.ajax({
                        url: 'process_configuracion_melaza.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            melaza: formData.melaza,
                            etapa: formData.etapa,
                            racion: formData.racion,
                            costo: formData.costo,
                            vigencia: formData.vigencia
                        },
                        success: function(response) {
                            console.log('Update success response:', response);
                            // Close the modal
                            editModal.hide();
                            
                            // Show success message
                            Swal.fire({
                                title: '¡Actualización exitosa!',
                                text: 'El registro ha sido actualizado correctamente',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page to show updated data
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Update AJAX error:', xhr, status, error);
                            console.log('Update request data:', {
                                action: 'update',
                                id: formData.id,
                                melaza: formData.melaza,
                                etapa: formData.etapa,
                                racion: formData.racion,
                                costo: formData.costo,
                                vigencia: formData.vigencia
                            });
                            
                            // Show error message
                            let errorMsg = 'Error al procesar la solicitud';
                            
                            try {
                                const response = JSON.parse(xhr.responseText);
                                console.log('Update error response:', response);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing update response:', e);
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
    });
    
    // Handle delete button click
    $('.delete-melaza').click(function() {
        var id = $(this).data('id');
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar la configuracion de melaza? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere mientras se procesa la solicitud',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send AJAX request to delete the record
                $.ajax({
                    url: 'process_configuracion_melaza.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        console.log('Delete success response:', response);
                        // Show success message
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'El registro ha sido eliminado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Reload the page to show updated data
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete AJAX error:', xhr, status, error);
                        console.log('Delete request data:', {
                            action: 'delete',
                            id: id
                        });
                        
                        // Show error message
                        let errorMsg = 'Error al procesar la solicitud';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Delete error response:', response);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing delete response:', e);
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

    // Handle new register button click for animals without history
    $(document).on('click', '.register-new-melaza-btn', function() { 
        // Get tagid from the button's data-tagid-prefill attribute
        var tagid = $(this).data('tagid-prefill'); 
        
        // Clear previous data in the modal
        $('#newMelazaForm')[0].reset();
        $('#new_id').val(''); // Ensure ID is cleared
        
      
        
        // Show the new entry modal using the existing instance
        newEntryModalInstance.show(); 
    });
});
</script>
</body>
</html>