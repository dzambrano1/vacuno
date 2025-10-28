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
<title>Vacuno Register Milk</title>
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
  REGISTROS PESAJE LECHE
  </h3>
  
  <!-- New Milk Entry Modal -->

  <div class="modal fade" id="newPesoModal" tabindex="-1" aria-labelledby="newPesoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPesoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Leche
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newPesoForm">
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
                            <i class="fa-solid fa-weight"></i>
                                <label for="new_peso" class="form-label">Peso Leche (Kg)</label>
                                <input type="text" class="form-control" id="new_peso" name="peso" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">                        
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-dollar-sign"></i>
                                <label for="new_precio" class="form-label">Precio ($)</label>
                                <input type="text" class="form-control" id="new_precio" name="precio" required>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewPeso">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  
  <!-- DataTable for vh_leche records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="lecheTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th class="text-center">Acciones</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Nombre</th>
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Leche (kg)</th>
                      <th class="text-center">Precio ($/kg)</th>
                      <th class="text-center">Valor Total ($)</th>
                      <th class="text-center">Estatus</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get all Female Animals and ALL their milk records (if any)
                        $milkQuery = "
                            SELECT
                                v.tagid AS vacuno_tagid,
                                v.nombre AS animal_nombre,
                                l.id AS leche_id,         -- Will be NULL for animals with no milk records
                                l.vh_leche_fecha,
                                l.vh_leche_tagid,         -- Matches vacuno_tagid if milk record exists
                                l.vh_leche_peso,
                                l.vh_leche_precio,
                                -- Calculate total_value only if l.id is not null
                                CASE WHEN l.id IS NOT NULL THEN CAST((l.vh_leche_peso * l.vh_leche_precio) AS DECIMAL(10,2)) ELSE NULL END as total_value
                            FROM
                                vacuno v
                            LEFT JOIN
                                vh_leche l ON v.tagid = l.vh_leche_tagid -- Join ALL matching milk records
                            WHERE
                                v.genero = 'Hembra' -- Filter for females only
                            ORDER BY
                                -- Prioritize animals with records (IS NOT NULL -> 0, IS NULL -> 1)
                                CASE WHEN l.id IS NOT NULL THEN 0 ELSE 1 END ASC,
                                -- Then order by animal tag ID to group them
                                v.tagid ASC,
                                -- Within each animal, order their milk records by date descending
                                l.vh_leche_fecha DESC";

                        $stmt = $conn->prepare($milkQuery);
                        $stmt->execute();
                        $milksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message
                      if (empty($milksData)) {
                          echo "<tr><td colspan='9' class='text-center'>No hay hembras registradas</td></tr>"; // Updated message
                      } else {
                          // Get vigencia setting for milk records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT v_vencimiento_pesaje_leche FROM v_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['v_vencimiento_pesaje_leche'])) {
                                  $vigencia = intval($row['v_vencimiento_pesaje_leche']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          
                          foreach ($milksData as $row) {
                              $hasLeche = !empty($row['leche_id']); // Check if this row represents a milk record
                              $lecheFecha = $row['vh_leche_fecha'] ?? null;
                              
                              echo "<tr>";
                              
                              // Column 1: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              // Always show Add Button
                              echo '        <button class="btn btn-success btn-sm" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newPesoModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['vacuno_tagid'] ?? '').'" 
                                              title="Registrar Nuevo Pesaje Leche">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              if ($hasLeche) {
                                  // Edit Button (only if milk record exists for this row)
                                  echo '        <button class="btn btn-warning btn-sm edit-peso" 
                                                  data-id="'.htmlspecialchars($row['leche_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vh_leche_tagid'] ?? '').'" 
                                                  data-peso="'.htmlspecialchars($row['vh_leche_peso'] ?? '').'" 
                                                  data-precio="'.htmlspecialchars($row['vh_leche_precio'] ?? '').'" 
                                                  data-fecha="'.htmlspecialchars($lecheFecha ?? '').'" 
                                                  title="Editar Pesaje">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  // Delete Button (only if milk record exists for this row)
                                  echo '        <button class="btn btn-danger btn-sm delete-peso" 
                                                  data-id="'.htmlspecialchars($row['leche_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vh_leche_tagid'] ?? '').'" -- Pass tagid for context
                                                  title="Eliminar Pesaje">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              echo '    </div>';
                              echo '</td>';
                              
                              // Column 2: Fecha
                              echo "<td>" . ($lecheFecha ? htmlspecialchars(date('d/m/Y', strtotime($lecheFecha))) : 'N/A') . "</td>";
                              // Column 3: Nombre Animal
                              echo "<td>" . htmlspecialchars($row['animal_nombre'] ?? 'N/A') . "</td>";
                              // Column 4: Tag ID Animal
                              echo "<td>" . htmlspecialchars($row['vacuno_tagid'] ?? 'N/A') . "</td>"; // Use vacuno_tagid for consistency
                              // Column 5: Leche (kg)
                              echo "<td>" . ($hasLeche ? htmlspecialchars($row['vh_leche_peso'] ?? '') : 'N/A') . "</td>";
                              // Column 6: Precio ($/kg)
                              echo "<td>" . ($hasLeche ? htmlspecialchars($row['vh_leche_precio'] ?? '') : 'N/A') . "</td>";
                              // Column 7: Valor Total ($)
                              echo "<td>" . ($hasLeche && isset($row['total_value']) ? htmlspecialchars($row['total_value']) : 'N/A') . "</td>";
                              
                              // Column 8: Estatus
                              if ($hasLeche && $lecheFecha) {
                                  try {
                                      $milkDate = new DateTime($lecheFecha);
                                      $dueDate = clone $milkDate;
                                      $dueDate->modify("+{$vigencia} days");
                                      
                                      if ($currentDate > $dueDate) {
                                          echo '<td class="text-center"><span class="badge bg-danger">HISTORICO</span></td>'; // Changed from VENCIDO
                                      } else {
                                          echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                      }
                                  } catch (Exception $e) {
                                      error_log("Date error: " . $e->getMessage() . " for date: " . $lecheFecha);
                                      echo '<td class="text-center"><span class="badge bg-warning">ERROR FECHA</span></td>'; // Changed from ERROR
                                  }
                              } else {
                                  echo '<td class="text-center"><span class="badge bg-secondary">Sin Registro</span></td>'; // Status if no milk record
                              }
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in leche table: " . $e->getMessage()); // Updated table name in log
                      echo "<tr><td colspan='8' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>"; // Colspan is 8 now
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Initialize DataTable for VH Leche -->
<script>
$(document).ready(function() {
    $('#lecheTable').DataTable({
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
                targets: [0], // Actions column (new position)
                orderable: false,
                searchable: false
            },
            {
                targets: [4, 5, 6], // Leche, Precio, Valor Total columns (indices shifted)
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data;
                        const number = parseFloat(data);
                        if (!isNaN(number)) {
                            return number.toLocaleString('es-ES', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                targets: [1], // Fecha column (index shifted)
                type: 'date-eu',
                render: function(data, type, row) {
                     if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        // Date is already formatted DD/MM/YYYY in PHP
                        return data; 
                    }
                    // For sorting/filtering, convert DD/MM/YYYY back to YYYY-MM-DD
                    if (type === 'sort' || type === 'filter') {
                         if (data === 'N/A') return null; 
                         const parts = data.split('/');
                         if (parts.length === 3) {
                            return parts[2] + '-' + parts[1] + '-' + parts[0];
                         }
                         return null; // Fallback
                    }
                    return data;
                }
            },
            {
                targets: [7], // Status column (index shifted)
                orderable: true,
                searchable: true
            }
            // Removed old Actions column def (index 8)
        ]
    });
});
</script>

<!-- JavaScript for Modal Pre-fill -->
<script>
$(document).ready(function() {
    var newPesoModalEl = document.getElementById('newPesoModal');
    if (newPesoModalEl) {
        var tagIdInput = newPesoModalEl.querySelector('#new_tagid');
        newPesoModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var tagIdToPrefill = button ? button.getAttribute('data-tagid-prefill') : null;
            
            if (tagIdInput && tagIdToPrefill) {
                tagIdInput.value = tagIdToPrefill;
            } else if (tagIdInput) {
                tagIdInput.value = ''; // Clear if no prefill info
            }
            // Optionally reset other fields
            // newPesoModalEl.querySelector('#new_peso').value = '';
            // newPesoModalEl.querySelector('#new_precio').value = '';
            // newPesoModalEl.querySelector('#new_fecha').value = '<?php echo date('Y-m-d'); ?>';
        });
    }
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // Handle new entry form submission
    $('#saveNewPeso').click(function() {
        // Validate the form
        var form = document.getElementById('newPesoForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            peso: $('#new_peso').val(),
            precio: $('#new_precio').val(),
            fecha: $('#new_fecha').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar el pesaje de la leche ${formData.peso} kg para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_milk.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        peso: formData.peso,
                        precio: formData.precio,
                        fecha: formData.fecha
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newPesoModal'));
                        modal.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de peso ha sido guardado correctamente',
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
    $('.edit-peso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var peso = $(this).data('peso');
        var precio = $(this).data('precio');
        var fecha = $(this).data('fecha');
        
        // Edit Milk Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editPesoModal" tabindex="-1" aria-labelledby="editPesoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPesoModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Pesaje
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPesoForm">
                            <input type="hidden" id="edit_id" value="${id}">                            
                            <div class="mb-4">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                        <label for="edit_fecha" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="edit_fecha" value="${fecha}" required>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="mb-4">                                
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i>
                                    <label for="edit_tagid" class="form-label">Tag ID</label>
                                    <input type="text" class="form-control" id="edit_tagid" value="${tagid}" readonly>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="mb-4">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-weight"></i>
                                        <label for="edit_peso" class="form-label">Peso</label>
                                        <input type="text" class="form-control" id="edit_peso" value="${peso}" required>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4">                                
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_precio" class="form-label">Precio ($/kg)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_precio" value="${precio}" required>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="saveEditPeso">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modal
        $('#editPesoModal').remove();
        
        // Add the modal to the page
        $('body').append(modalHtml);
        
        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editPesoModal'));
        editModal.show();
        
        // Handle save button click
        $('#saveEditPeso').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                peso: $('#edit_peso').val(),
                precio: $('#edit_precio').val(),
                fecha: $('#edit_fecha').val()
            };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de leche para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_milk.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            tagid: formData.tagid,
                            peso: formData.peso,
                            precio: formData.precio,
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
    $('.delete-peso').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        
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
                    url: 'process_milk.php',
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

<!-- Curva de Lactancia Line Chart Section -->

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Curva de Lactancia</h5>
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
                        <option value="20">Últimos 20 pesajes</option>
                        <option value="50">Últimos 50 pesajes</option>
                        <option value="100">Últimos 100 pesajes</option>
                        <option value="all">Todos los pesajes</option>
                    </select>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="milkChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Milk Line Chart -->
<script>
$(document).ready(function() {
    let allMilkData = [];
    let milkChart = null;
    
    // Fetch milk data and create the chart
    $.ajax({
        url: 'get_milk_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error('Server error:', data.error);
                return;
            }
            
            allMilkData = data;
            populateAnimalFilter(data);
            createMilkChart(data);
            
            // Add event listeners for filters
            $('#animalFilter, #dataRangeFilter').on('change', function() {
                updateChart();
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching weight data:', error);
        }
    });
    
    // --- Linear Regression Function ---
    // (Assuming this function exists or copying it here if needed)
    function linearRegression(x, y) {
        const n = x.length;
        if (n < 2) return { slope: 0, intercept: 0 };
        let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
        for (let i = 0; i < n; i++) {
            sumX += x[i]; sumY += y[i]; sumXY += x[i] * y[i]; sumXX += x[i] * x[i];
        }
        const denominator = (n * sumXX - sumX * sumX);
        if (denominator === 0) return { slope: 0, intercept: sumY / n };
        const slope = (n * sumXY - sumX * sumY) / denominator;
        const intercept = (sumY - slope * sumX) / n;
        return { slope: slope, intercept: intercept };
    }
    // ---------------------------------

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
        
        let filteredData = [...allMilkData];
        
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
        if (milkChart) {
            milkChart.destroy();
        }
        createMilkChart(data);
    }
    
    function createMilkChart(data) {
        var ctx = document.getElementById('milkChart').getContext('2d');
        
        // Format the data for the main chart labels and values
        var labels = data.map(function(item) {
            // Format the date for display using timestamp
            var date = new Date(item.timestamp_fecha * 1000); 
            return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        });
        
        var milkWeights = data.map(function(item) {
            return parseFloat(item.peso) || 0; // Use 'peso' field from get_milk_data, ensure numeric
        });

        // --- Trendline Calculation ---
        var xValues = data.map(item => item.timestamp_fecha); // Numeric timestamps for x
        var yValues = milkWeights; // Numeric milk weights for y
        
        const regression = linearRegression(xValues, yValues);
        const trendlineYValues = xValues.map(x => regression.slope * x + regression.intercept);
        
        // Calculate Average Monthly Slope in kg
        const secondsPerDay = 24 * 60 * 60;
        const approxDaysPerMonth = 365.25 / 12;
        const secondsPerMonth = secondsPerDay * approxDaysPerMonth;
        const monthlySlopeKg = regression.slope * secondsPerMonth; // Slope in kg/month
        // ---------------------------

        // Destroy existing chart instance if it exists
        if (milkChart) {
            milkChart.destroy();
        }
        
        milkChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                { // Original Milk Data
                    label: 'Leche (kg)',
                    data: milkWeights,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)', // Blue color for milk
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3
                },
                { // Trendline Data
                    label: 'Tendencia Lineal (kg)',
                    data: trendlineYValues, 
                    borderColor: 'rgba(255, 99, 132, 0.8)', // Reddish color for trend
                    borderWidth: 2,
                    borderDash: [5, 5], 
                    type: 'line',
                    pointRadius: 4, // Show points on trendline
                    pointBackgroundColor: 'rgba(255, 99, 132, 0.8)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0 // Straight line
                }
            ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false, // Milk production might not start at 0
                        title: {
                            display: true,
                            text: 'Leche (kg)',
                            font: { size: 14, weight: 'bold' }
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha', // Changed from Meses to Fecha for clarity
                            font: { size: 14, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var datasetIndex = context.datasetIndex;
                                var value = context.parsed.y;
                                let tooltipText = [];
                                if (datasetIndex === 0) { // Original data
                                    tooltipText.push('Leche: ' + value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg');
                                    // Add animal name if available and filtered
                                    var index = context.dataIndex;
                                    var dataPoint = data[index];
                                    if (dataPoint && dataPoint.animal_nombre) {
                                         tooltipText.unshift('Animal: ' + dataPoint.animal_nombre);
                                    }
                                } else if (datasetIndex === 1) { // Trendline data
                                    tooltipText.push('Tendencia: ' + value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' kg');
                                }
                                return tooltipText;
                            },
                            title: function(tooltipItems) {
                                return 'Fecha: ' + tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            const selectedAnimal = $('#animalFilter').val();
                            let baseTitle = 'Curva de Lactancia';
                            if (selectedAnimal !== 'all') {
                                const animalName = $('#animalFilter option:selected').text();
                                baseTitle = 'Curva de Lactancia - ' + animalName;
                            }
                            // Append monthly slope if calculated
                            if (regression.slope !== 0 && data.length > 1) {
                                const formattedSlope = monthlySlopeKg.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                    signDisplay: 'always' 
                                });
                                return baseTitle + ` | Variación Mensual Prom.: ${formattedSlope} kg`;
                            }
                            return baseTitle; 
                        },
                        font: { size: 16 }
                    }
                }
            }
        });
    }
});
</script>

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white"> <!-- Info color -->
            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Evolución Facturación Lechera ($) (*)</h5>
            <h6 class="mb-0 text-start">(*) Sólo una tendencia. La producción real se hace con tomas diarias ($)</h6>
        </div>
        <div class="card-body">
             <div id="milkValueChartMessage" class="text-center text-muted small mb-2"></div> <!-- Message area -->
            <div class="chart-container" style="position: relative; height:50vh; width:100%">
                <canvas id="milkValueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script for Monthly Milk Value Chart -->
<script>
$(document).ready(function() {
    let milkValueChart = null;
    const milkValueUrl = 'get_monthly_milk_value_data.php'; // New endpoint

    // Re-use the linear regression function 
    function linearRegression(x, y) {
        const n = x.length;
        if (n < 2) return { slope: 0, intercept: 0 };
        let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
        for (let i = 0; i < n; i++) {
            sumX += x[i]; sumY += y[i]; sumXY += x[i] * y[i]; sumXX += x[i] * x[i];
        }
        const denominator = (n * sumXX - sumX * sumX);
        if (denominator === 0) return { slope: 0, intercept: sumY / n };
        const slope = (n * sumXY - sumX * sumY) / denominator;
        const intercept = (sumY - slope * sumX) / n;
        return { slope: slope, intercept: intercept };
    }

    // Fetch monthly milk value data
    $.ajax({
        url: milkValueUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (!Array.isArray(data) || data.error) {
                console.error('Server error fetching monthly milk value:', data);
                 $('#milkValueChartMessage').text('Error al cargar datos de valor de leche.');
                return;
            }
            if (data.length === 0) {
                 console.warn('No monthly milk value data received.');
                 $('#milkValueChartMessage').text('No hay datos disponibles para mostrar el valor de leche mensual.');
                 return;
            }
            createMilkValueChart(data);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching monthly milk value data:', error);
            $('#milkValueChartMessage').text('Error al cargar datos para el gráfico de valor de leche.');
        }
    });

    function createMilkValueChart(data) {
        var ctx = document.getElementById('milkValueChart').getContext('2d');

        // Format data for chart
        var labels = data.map(item => item.month); // YYYY-MM labels
        var values = data.map(item => parseFloat(item.total_milk_value) || 0);

        // --- Trendline Calculation ---
        var xValues = data.map(item => item.month_timestamp); // Use month timestamp
        var yValues = values;
        
        const regression = linearRegression(xValues, yValues);
        const trendlineYValues = xValues.map(x => regression.slope * x + regression.intercept);
        
        // Calculate Average Monthly Slope in $
        const secondsPerDay = 24 * 60 * 60;
        const approxDaysPerMonth = 365.25 / 12;
        const secondsPerMonth = secondsPerDay * approxDaysPerMonth;
        const monthlySlopeValue = regression.slope * secondsPerMonth; // Slope in $/month
        // ---------------------------

        // Destroy existing chart instance if it exists
        if (milkValueChart) {
            milkValueChart.destroy();
        }

        milkValueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                { // Original Monthly Value Data
                    label: 'Tendencia Produccion Lechera ($)',
                    data: values,
                    backgroundColor: 'rgba(23, 162, 184, 0.2)', // Info blue
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3
                },
                { // Trendline Data
                    label: 'Tendencia Lineal ($)',
                    data: trendlineYValues,
                    borderColor: 'rgba(255, 193, 7, 0.8)', // Warning yellow for trend
                    borderWidth: 2,
                    borderDash: [5, 5],
                    type: 'line',
                    pointRadius: 4, // Show points
                    pointBackgroundColor: 'rgba(255, 193, 7, 0.8)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0 
                }
            ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true, // Value should start at 0
                        title: {
                            display: true,
                            text: 'Tendencia Facturación Lechera ($)',
                            font: { size: 14, weight: 'bold' }
                        },
                        ticks: {
                            callback: function(value) {
                                return '$ ' + value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mes',
                            font: { size: 14, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var datasetIndex = context.datasetIndex;
                                var value = context.parsed.y;
                                if (datasetIndex === 0) { // Original data
                                    return 'Valor: $ ' + value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                } else if (datasetIndex === 1) { // Trendline data
                                     return 'Tendencia: $ ' + value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                                return ''; // Default
                            },
                            title: function(tooltipItems) {
                                return 'Mes: ' + tooltipItems[0].label;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: function() {
                            let baseTitle = 'Evolución Facturación Lechera ($)';
                            // Append monthly slope if calculated
                            if (regression.slope !== 0 && data.length > 1) {
                                const formattedSlope = monthlySlopeValue.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                    signDisplay: 'always' 
                                });
                                return baseTitle + ` | Variación Mensual Prom.: $ ${formattedSlope}`;
                            }
                            return baseTitle; 
                        },
                        font: { size: 16 }
                    }
                }
            }
        });
    }
});
</script>