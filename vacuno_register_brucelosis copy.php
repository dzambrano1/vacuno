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
<title>Vacuno Register CBR</title>
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
  REGISTROS DE BRUCELOSIS
  </h3>
  
  <div class="container mt-3 mb-4 text-center">
    <button type="button" class="btn btn-add-animal" data-bs-toggle="modal" data-bs-target="#newEntryModal">
        <i class="fas fa-plus-circle me-2"></i>Registrar
    </button>
</div>
  
  <!-- New Brucelosis Entry Modal -->
  
  <div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Brucelosis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newBrucelosisForm">
                <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                                <label for="new_fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="new_fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                            </span>                            
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-tag"></i>
                                <label for="new_tagid" class="form-label">Tag ID</label>
                                <input type="text" class="form-control" id="new_tagid" name="tagid" required>
                            </span>                            
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-syringe"></i>
                                <label for="new_vacuna" class="form-label">Vacuna</label>
                                <input type="text" class="form-control" id="new_vacuna" name="vacuna" required>                            
                            </span>                            
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-eye-dropper"></i>
                                <label for="new_dosis" class="form-label">Dosis (ml)</label>
                                <input type="number" step="0.01" class="form-control" id="new_dosis" name="dosis" required>
                            </span>                            
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-money-bill-1-wave"></i>
                                <label for="new_costo" class="form-label">Costo ($)</label>
                                <input type="number" step="0.01" class="form-control" id="new_costo" name="costo" required>
                            </span>                            
                        </div>
                    </div>                                          
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewBrucelosis">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  
  <!-- DataTable for vh_brucelosis records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="vh_brucelosisTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">ID</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Nombre</th>                    
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Producto</th>
                      <th class="text-center">Dosis (ml)</th>
                      <th class="text-center">Costo ($/ml)</th>
                      <th class="text-center">Estatus</th>
                      <th class="text-center">Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get all vh_brucelosis records with animal name
                      $brucelosisQuery = "SELECT b.*, v.nombre as animal_nombre,
                                CAST((b.vh_brucelosis_dosis * b.vh_brucelosis_costo) AS DECIMAL(10,2)) as total_value
                                FROM vh_brucelosis b 
                                LEFT JOIN vacuno v ON b.vh_brucelosis_tagid = v.tagid 
                                ORDER BY b.vh_brucelosis_fecha DESC";
                                
                      $stmt = $conn->prepare($brucelosisQuery);
                      $stmt->execute();
                      $brucelosisData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      
                      // If no data, display a message
                      if (empty($brucelosisData)) {
                          echo "<tr><td colspan='9' class='text-center'>No hay registros disponibles</td></tr>";
                      } else {
                          // Get vigencia setting for brucelosis records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT v_vencimiento_brucelosis FROM v_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['v_vencimiento_brucelosis'])) {
                                  $vigencia = intval($row['v_vencimiento_brucelosis']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          foreach ($brucelosisData as $row) {
                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($row['id'] ?? '') . "</td>";                        
                              echo "<td>" . htmlspecialchars($row['vh_brucelosis_fecha'] ?? '') . "</td>";                        
                              echo "<td>" . htmlspecialchars($row['animal_nombre'] ?? 'N/A') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_brucelosis_tagid'] ?? '') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_brucelosis_producto'] ?? '') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_brucelosis_dosis'] ?? '') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_brucelosis_costo'] ?? '') . "</td>";
                              
                              // Calculate due date and determine status
                              try {
                                  $brucelosisDate = new DateTime($row['vh_brucelosis_fecha']);
                                  $dueDate = clone $brucelosisDate;
                                  $dueDate->modify("+{$vigencia} days");
                                  
                                  if ($currentDate > $dueDate) {
                                      echo '<td class="text-center"><span class="badge bg-danger">VENCIDO</span></td>';
                                  } else {
                                      echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                  }
                              } catch (Exception $e) {
                                  // Handle invalid date format
                                  error_log("Date error: " . $e->getMessage() . " for date: " . $row['vh_brucelosis_fecha']);
                                  echo '<td class="text-center"><span class="badge bg-warning">ERROR</span></td>';
                              }
                              
                              // Add action buttons (edit and delete)
                              echo '<td class="text-center">
                                  <div class="btn-group" role="group">
                                      <button class="btn btn-warning btn-sm edit-brucelosis" 
                                          data-id="' . htmlspecialchars($row['id'] ?? '') . '"
                                          data-tagid="' . htmlspecialchars($row['vh_brucelosis_tagid'] ?? '') . '"
                                          data-vacuna="' . htmlspecialchars($row['vh_brucelosis_producto'] ?? '') . '"
                                          data-dosis="' . htmlspecialchars($row['vh_brucelosis_dosis'] ?? '') . '"
                                          data-costo="' . htmlspecialchars($row['vh_brucelosis_costo'] ?? '') . '"
                                          data-fecha="' . htmlspecialchars($row['vh_brucelosis_fecha'] ?? '') . '">
                                          <i class="fas fa-edit"></i>
                                      </button>
                                      <button class="btn btn-danger btn-sm delete-brucelosis" 
                                          data-id="' . htmlspecialchars($row['id'] ?? '') . '">
                                          <i class="fas fa-trash"></i>
                                      </button>
                                  </div>
                              </td>';
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in brucelosis table: " . $e->getMessage());
                      echo "<tr><td colspan='9' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Initialize DataTable for VH brucelosis -->
<script>
$(document).ready(function() {
    $('#vh_brucelosisTable').DataTable({
        // Set initial page length
        pageLength: 5,
        
        // Configure length menu options
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            [5, 10, 25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[1, 'desc']],
        
        // Spanish language
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
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
                targets: [ 5, 6], // Dosis, Costo columns
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
                targets: [1], // Fecha column
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
                targets: [7], // Status column
                orderable: true,
                searchable: true
            },
            {
                targets: [8], // Actions column
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
    $('#saveNewBrucelosis').click(function() {
        // Validate the form
        var form = document.getElementById('newBrucelosisForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            vacuna: $('#new_vacuna').val(),
            dosis: $('#new_dosis').val(),
            costo: $('#new_costo').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar la dosis de brucelosis ${formData.dosis} ml para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_brucelosis.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        vacuna: formData.vacuna,
                        dosis: formData.dosis,
                        costo: formData.costo,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newEntryModal'));
                        modal.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de brucelosis ha sido guardado correctamente',
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
    $('.edit-brucelosis').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var vacuna = $(this).data('vacuna');
        var dosis = $(this).data('dosis');
        var costo = $(this).data('costo');
        var fecha = $(this).data('fecha');
        
        // Edit Brucelosis Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editBrucelosisModal" tabindex="-1" aria-labelledby="editBrucelosisModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBrucelosisModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Dosis de brucelosis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editBrucelosisForm">
                            <div class="mb-2">                                
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                            <label for="edit_fecha" class="form-label">Fecha</label>
                                            <input type="date" class="form-control" id="edit_fecha" value="${fecha}" required>
                                        </span>
                                    </div>
                                </div>                            
                            <div class="mb-2">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                        <label for="edit_tagid" class="form-label"> Tag ID </label>
                                        <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="mb-2">                            
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-syringe"></i>
                                        <label for="edit_vacuna" class="form-label">Vacuna</label>                                    
                                        <input type="text" class="form-control" id="edit_vacuna" value="${vacuna}" required>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="mb-2">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-eye-dropper"></i>
                                        <label for="edit_dosis" class="form-label">Dosis (ml)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_dosis" value="${dosis}" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_costo" class="form-label">Costo ($)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_costo" value="${costo}" required>
                                    </span>
                                </div>
                            </div>                            
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditBrucelosis">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editBrucelosisModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editBrucelosisModal'));
        editModal.show();
        
        // Handle save button click
        $('#saveEditBrucelosis').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                vacuna: $('#edit_vacuna').val(),
                dosis: $('#edit_dosis').val(),
                costo: $('#edit_costo').val(),
                fecha: $('#edit_fecha').val()
            };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de brucelosis para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_brucelosis.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            tagid: formData.tagid,
                            vacuna: formData.vacuna,
                            dosis: formData.dosis,
                            costo: formData.costo,
                            fecha: formData.fecha
                        },
                        success: function(response) {
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
    });
    
    // Handle delete button click
    $('.delete-brucelosis').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).closest('tr').find('td:eq(3)').text().trim(); // Get Tag ID from the 4th column
        
        // Confirm before deleting using SweetAlert2
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `¿Está seguro de que desea eliminar el registro para el animal con Tag ID ${tagid}? Esta acción no se puede deshacer.`,
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
                    url: 'process_brucelosis.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
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
});
</script>

<!-- Brucelosis Line Chart Section -->

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evolución Brucelosis</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <select id="animalFilter" class="form-select">
                        <option value="all">Todos los animales</option>
                        <!-- Animal options will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="dataRangeFilter" class="form-select">
                        <option value="20">Últimos 20 dosis</option>
                        <option value="50">Últimos 50 dosis</option>
                        <option value="100">Últimos 100 dosis</option>
                        <option value="all">Todas las dosis</option>
                    </select>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="brucelosisChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Brucelosis Line Chart -->
<script>
$(document).ready(function() {
    let allBrucelosisData = [];
    let brucelosisChart = null;
    
    // Fetch brucelosis data and create the chart
    $.ajax({
        url: 'get_brucelosis_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            allBrucelosisData = data;
            populateAnimalFilter(data);
            createBrucelosisChart(data);
            
            // Add event listeners for filters
            $('#animalFilter, #dataRangeFilter').on('change', function() {
                updateChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching brucelosis data:', error);
        }
    });
    
    function populateAnimalFilter(data) {
        // Get unique animals from the data
        const animals = [];
        const uniqueTagIds = new Set();
        
        data.forEach(item => {
            if (item.tagid && !uniqueTagIds.has(item.tagid)) {
                uniqueTagIds.add(item.tagid);
                animals.push({
                    tagid: item.tagid,
                    nombre: item.animal_nombre || 'Sin nombre'
                });
            }
        });
        
        // Sort animals by name
        animals.sort((a, b) => a.nombre.localeCompare(b.nombre));
        
        // Add options to the dropdown
        const animalFilter = $('#animalFilter');
        animals.forEach(animal => {
            animalFilter.append(`<option value="${animal.tagid}">${animal.nombre} (${animal.tagid})</option>`);
        });
    }
    
    function updateChart() {
        const selectedAnimal = $('#animalFilter').val();
        const selectedRange = $('#dataRangeFilter').val();
        
        let filteredData = [...allBrucelosisData];
        
        // Filter by animal if not "all"
        if (selectedAnimal !== 'all') {
            filteredData = filteredData.filter(item => item.tagid === selectedAnimal);
        }
        
        // Sort data by date
        filteredData.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
        
        // Apply range filter
        if (selectedRange !== 'all' && filteredData.length > parseInt(selectedRange)) {
            filteredData = filteredData.slice(filteredData.length - parseInt(selectedRange));
        }
        
        // Update chart with filtered data
        updateChartData(filteredData);
    }
    
    function updateChartData(data) {
        if (brucelosisChart) {
            brucelosisChart.destroy();
        }
        createBrucelosisChart(data);
    }
    
    function createBrucelosisChart(data) {
        var ctx = document.getElementById('brucelosisChart').getContext('2d');
        
        // Format the data for the chart
        var labels = data.map(function(item) {
            // Format the date for display
            var parts = item.fecha.split('-');
            if (parts.length === 3) {
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            return item.fecha;
        });
        
        var dosis = data.map(function(item) {
            return item.dosis;
        });
        
        brucelosisChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Dosis (ml)',
                    data: dosis,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Dosis (ml)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + ' ml';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var index = context.dataIndex;
                                var datasetIndex = context.datasetIndex;
                                var dataPoint = data[index];
                                
                                var tooltipText = [
                                    'Dosis: ' + context.parsed.y.toLocaleString('es-ES', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) + ' ml'
                                ];
                                
                                if (dataPoint && dataPoint.animal_nombre) {
                                    tooltipText.unshift('Animal: ' + dataPoint.animal_nombre);
                                }
                                
                                return tooltipText;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedAnimal = $('#animalFilter').val();
                            if (selectedAnimal !== 'all') {
                                const animalName = $('#animalFilter option:selected').text();
                                return 'Evolución de Dosis de Brucelosis - ' + animalName;
                            }
                            return 'Evolución de Dosis de Brucelosis - Todos los Animales';
                        },
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }
});
</script>

<!-- Pie Chart Section -->
<div class="container mt-5 mb-5">
    <h4 class="text-center text-black mb-4">Estado de Vacunación Brucelosis por Etapa</h4>
    <div class="row justify-content-center">
        <!-- Inicio Pie Chart -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 text-center"><i class="fas fa-chart-pie me-2"></i>Etapa Inicio</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="brucelosisPieInicio"></canvas>
                        <div id="brucelosisPieInicioMessage" class="text-center small text-muted mt-2"></div>
                    </div>
                    <!-- Toggle Button and Table for Non-Vaccinated -->
                    <div class="text-center mt-2">
                        <button class="btn btn-outline-secondary btn-sm toggle-non-vaccinated-table" data-etapa="Inicio" style="display: none;">
                             Mostrar/Ocultar No Vacunados
                        </button>
                    </div>
                    <div id="nonVaccinatedTableContainerInicio" class="mt-3" style="display: none; max-height: 200px; overflow-y: auto;">
                        <!-- Table will be populated here -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Crecimiento Pie Chart -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 text-center"><i class="fas fa-chart-pie me-2"></i>Etapa Crecimiento</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="brucelosisPieCrecimiento"></canvas>
                        <div id="brucelosisPieCrecimientoMessage" class="text-center small text-muted mt-2"></div>
                    </div>
                     <!-- Toggle Button and Table for Non-Vaccinated -->
                    <div class="text-center mt-2">
                         <button class="btn btn-outline-secondary btn-sm toggle-non-vaccinated-table" data-etapa="Crecimiento" style="display: none;">
                             Mostrar/Ocultar No Vacunados
                         </button>
                    </div>
                     <div id="nonVaccinatedTableContainerCrecimiento" class="mt-3" style="display: none; max-height: 200px; overflow-y: auto;">
                         <!-- Table will be populated here -->
                     </div>
                </div>
            </div>
        </div>
        <!-- Finalizacion Pie Chart -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 text-center"><i class="fas fa-chart-pie me-2"></i>Etapa Finalización</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="brucelosisPieFinalizacion"></canvas>
                         <div id="brucelosisPieFinalizacionMessage" class="text-center small text-muted mt-2"></div>
                    </div>
                     <!-- Toggle Button and Table for Non-Vaccinated -->
                     <div class="text-center mt-2">
                         <button class="btn btn-outline-secondary btn-sm toggle-non-vaccinated-table" data-etapa="Finalizacion" style="display: none;">
                              Mostrar/Ocultar No Vacunados
                         </button>
                     </div>
                     <div id="nonVaccinatedTableContainerFinalizacion" class="mt-3" style="display: none; max-height: 200px; overflow-y: auto;">
                          <!-- Table will be populated here -->
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Brucelosis Status Pie Charts -->
<script>
$(document).ready(function() {
    const brucelosisStatusUrl = 'get_brucelosis_status_by_etapa.php';
    let pieCharts = {}; // Store chart instances

    // --- Helper function to create or update a pie chart ---
    function createOrUpdatePieChart(canvasId, messageId, title, data) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const messageDiv = $('#' + messageId);
        messageDiv.text(''); // Clear previous messages

        const vaccinated = data.vaccinated || 0;
        const nonVaccinated = data.non_vaccinated || 0;
        const total = vaccinated + nonVaccinated;

        if (pieCharts[canvasId]) {
            pieCharts[canvasId].destroy(); // Destroy previous instance if exists
        }

        if (total === 0) {
            messageDiv.text('No hay animales en esta etapa.');
             // Optionally hide the canvas or show a placeholder
             $(ctx.canvas).hide();
            return; // Don't create chart if no data
        }
        
        $(ctx.canvas).show(); // Ensure canvas is visible

        pieCharts[canvasId] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    `Vacunados (${vaccinated})`,
                    `No Vacunados (${nonVaccinated})`
                ],
                datasets: [{
                    label: 'Estado Brucelosis',
                    data: [vaccinated, nonVaccinated],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)', // Green for vaccinated
                        'rgba(220, 53, 69, 0.7)'  // Red for non-vaccinated
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom', // Position legend below chart
                    },
                    title: {
                        display: false, // Title is in the card header
                        // text: title // Already in card header
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let percentage = 0;
                                if (total > 0) {
                                    percentage = ((value / total) * 100).toFixed(1);
                                }
                                return `${label}: ${percentage}%`;
                            }
                        }
                    },
                     datalabels: { // Use chartjs-plugin-datalabels
                         formatter: (value, ctx) => {
                             let sum = 0;
                             let dataArr = ctx.chart.data.datasets[0].data;
                             dataArr.map(data => {
                                 sum += data;
                             });
                             let percentage = (value*100 / sum).toFixed(1)+"%";
                             // Hide label if percentage is too small
                             return (value*100 / sum) < 5 ? '' : percentage;
                         },
                         color: '#fff',
                         font: {
                             weight: 'bold'
                         }
                     }
                }
            },
             plugins: [ChartDataLabels] // Register the plugin
        });
    }

    // --- Helper function to update the non-vaccinated table ---
    function updateNonVaccinatedTable(etapa, nonVaccinatedList) {
        const containerId = `#nonVaccinatedTableContainer${etapa}`;
        const buttonSelector = `.toggle-non-vaccinated-table[data-etapa='${etapa}']`;
        const container = $(containerId);
        const button = $(buttonSelector);

        container.empty().hide(); // Clear previous content and hide
        button.hide(); // Hide button by default

        if (nonVaccinatedList && nonVaccinatedList.length > 0) {
            button.show(); // Show button only if there are animals to list

            let tableHtml = '<table class="table table-sm table-striped table-bordered small">';
            tableHtml += '<thead><tr><th>Nombre</th><th>Tag ID</th></tr></thead><tbody>';

            nonVaccinatedList.forEach(animal => {
                // Use 'N/A' if name is null or empty
                const nombre = animal.nombre ? animal.nombre : 'N/A';
                tableHtml += `<tr><td>${nombre}</td><td>${animal.tagid}</td></tr>`;
            });

            tableHtml += '</tbody></table>';
            container.html(tableHtml);
            // Keep container hidden, button toggles it
        } else {
            // Optional: Display a message if the list is empty but was expected
            // container.html('<p class="text-muted small text-center">Todos los animales en esta etapa están vacunados.</p>');
            // Keep button hidden as there is nothing to show/hide
        }
    }

    // Fetch data and create charts/tables
    $.ajax({
        url: brucelosisStatusUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Error fetching brucelosis status data:', data.error);
                 $('#brucelosisPieInicioMessage, #brucelosisPieCrecimientoMessage, #brucelosisPieFinalizacionMessage').text('Error al cargar datos.');
                return;
            }

            // Process data for each stage
            const stages = ['Inicio', 'Crecimiento', 'Finalizacion'];
            stages.forEach(stage => {
                 const stageData = data[stage] || {vaccinated: 0, non_vaccinated: 0, non_vaccinated_list: []};
                 const canvasId = `brucelosisPie${stage}`;
                 const messageId = `brucelosisPie${stage}Message`;
                
                 createOrUpdatePieChart(canvasId, messageId, stage, stageData);
                 updateNonVaccinatedTable(stage, stageData.non_vaccinated_list);
            });

        },
        error: function(xhr, status, error) {
            console.error('AJAX error fetching brucelosis status data:', error);
            $('#brucelosisPieInicioMessage, #brucelosisPieCrecimientoMessage, #brucelosisPieFinalizacionMessage').text('Error de conexión al cargar datos.');
        }
    });

    // Add event listener for toggle buttons (using event delegation)
     $(document).on('click', '.toggle-non-vaccinated-table', function() {
         const etapa = $(this).data('etapa');
         const containerId = `#nonVaccinatedTableContainer${etapa}`;
         $(containerId).slideToggle('fast'); // Toggle visibility with animation
     });

});
</script>