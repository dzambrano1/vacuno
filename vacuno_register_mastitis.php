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
<title>Vacuno - Registro de Vacunación Mastitis</title>
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
                <!-- Botón de Configuración -->
                <button type="button" onclick="window.location.href='./vacuno_configuracion.php'" class="btn" style="color:white; border: none; border-radius: 8px; padding: 8px 15px; z-index: 1050; position: relative;" title="Configuración">
                    <i class="fas fa-cog"></i> 
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
        <div class="col-12 col-md-11">
            <div class="row justify-content-center align-items-stretch">
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step arrow-step-first w-100" onclick="window.location.href='./inventario_vacuno.php'" title="Ir a Inventario">
                        <div style="background: white; color: #28a745; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            1
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 1:<br>Crear Animales</h5>
                    </div>
                </div>
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step arrow-step-active w-100">
                        <span class="badge-active">🎯 Registrando Tratamientos Mastitis</span>
                        <div style="background: white; color: #17a2b8; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            2
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 2:<br>Registrar Tareas</h5>
                    </div>
                </div>
                <div class="col-md-4 d-flex px-1 mb-3 mb-md-0">
                    <div class="arrow-step arrow-step-last w-100" onclick="window.location.href='./vacuno_indices.php'" title="Ir a Índices">
                        <div style="background: white; color: #28a745; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.8rem; font-weight: bold; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                            3
                        </div>
                        <h5 class="text-white text-center mb-0" style="font-weight: bold; font-size: 1rem;">PASO 3:<br>Consultar</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add back button before the header container -->
<a href="./vacuno_registros.php" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="container text-center">

  
  <!-- New Mastitis Entry Modal -->
  
  <div class="modal fade" id="newEntryModal" tabindex="-1" aria-labelledby="newEntryModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEntryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Vacunacion Mastitis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newMastitisForm">
                <div class="mb-4">
                    <input type="hidden" id="new_id" name="id" value="">                        
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
                                <label for="new_vacuna" class="form-label">Vacunas Mastitis</label>
                                <select class="form-select" id="new_vacuna" name="vacuna" required>
                                    <option value="">Vacunas</option>
                                    <?php
                                    try {
                                        $sql_vacunas = "SELECT DISTINCT vc_mastitis_vacuna FROM vc_mastitis ORDER BY vc_mastitis_vacuna ASC";
                                        $stmt_vacunas = $conn->prepare($sql_vacunas);
                                        $stmt_vacunas->execute();
                                        $vacunas = $stmt_vacunas->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($vacunas as $vacuna_row) {
                                            echo '<option value="' . htmlspecialchars($vacuna_row['vc_mastitis_vacuna']) . '">' . htmlspecialchars($vacuna_row['vc_mastitis_vacuna']) . '</option>';
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error fetching vacunas: " . $e->getMessage());
                                        echo '<option value="">Error al cargar vacunas</option>';
                                    }
                                    ?>
                                </select>
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
                <button type="button" class="btn btn-success" id="saveNewMastitis">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
  
  <!-- DataTable for vh_mastitis records -->
  
  <div class="container table-section" style="display: block;">
      <div class="table-responsive">
          <table id="mastitisTable" class="table table-striped table-bordered">
              <thead>
                  <tr>
                    <th class="text-center">F. Vacunacion</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Numero</th>
                    <th class="text-center">Vacuna</th>
                    <th class="text-center">Dosis (ml)</th>
                    <th class="text-center">Costo ($)</th>
                    <th class="text-center">Estatus</th>
                    <th class="text-center">Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      $mastitisQuery = "SELECT 
                                          v.id AS vacuno_id, -- Alias vacuno ID
                                          v.tagid,
                                          v.nombre,
                                          va.vh_mastitis_fecha,
                                          va.id AS mastitis_record_id, -- Alias mastitis record ID
                                          va.vh_mastitis_producto,
                                          va.vh_mastitis_dosis,
                                          va.vh_mastitis_costo,
                                          -- Flag to easily check if there's a history record
                                          CASE WHEN va.vh_mastitis_tagid IS NOT NULL THEN 1 ELSE 0 END AS in_mastitis_history
                                      FROM 
                                          vacuno v
                                      LEFT JOIN 
                                          -- Join with all mastitis records per tagid
                                          vh_mastitis va ON v.tagid = va.vh_mastitis_tagid 
                                      WHERE 
                                          v.genero = 'Hembra'
                                          AND va.vh_mastitis_tagid IS NOT NULL
                                      ORDER BY 
                                          va.vh_mastitis_fecha DESC, v.tagid ASC;";

                      $stmt = $conn->prepare($mastitisQuery);
                      $stmt->execute();
                      $mastitissData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      if (empty($mastitissData)) {
                          echo "<tr><td colspan='8' class='text-center'>No hay registros de mastitis disponibles</td></tr>";
                      } else {
                          // Get vigencia setting for mastitis records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT v_vencimiento_mastitis FROM v_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              $row_config = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row_config && isset($row_config['v_vencimiento_mastitis'])) {
                                  $vigencia = intval($row_config['v_vencimiento_mastitis']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                          }

                          $currentDate = new DateTime();

                          foreach ($mastitissData as $row) {
                              echo "<tr>";

                              echo "<td>" . htmlspecialchars($row['vh_mastitis_fecha'] ?? '') . "</td>";
                              echo "<td>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                              echo "<td>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";

                              // Animal has vaccination history (since we filtered for this)
                              echo "<td>" . htmlspecialchars($row['vh_mastitis_producto'] ?? 'N/A') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_mastitis_dosis'] ?? 'N/A') . "</td>";
                              echo "<td>" . htmlspecialchars($row['vh_mastitis_costo'] ?? 'N/A') . "</td>";

                              // Calculate due date and determine status
                              try {
                                  if (!empty($row['vh_mastitis_fecha'])) {
                                      $mastitisDate = new DateTime($row['vh_mastitis_fecha']);
                                      $dueDate = clone $mastitisDate;
                                      $dueDate->modify("+{$vigencia} days");

                                      if ($currentDate > $dueDate) {
                                          echo '<td class="text-center"><span class="badge bg-danger">VENCIDO</span></td>';
                                      } else {
                                          echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                      }
                                  } else {
                                       echo '<td class="text-center"><span class="badge bg-secondary">Sin Fecha</span></td>';
                                  }
                              } catch (Exception $e) {
                                  error_log("Date error: " . $e->getMessage() . " for date: " . $row['vh_mastitis_fecha']);
                                  echo '<td class="text-center"><span class="badge bg-warning">ERROR FECHA</span></td>';
                              }
                              
                              // Actions column - Modified to always show Add button
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              
                              // Always show Add (+) button
                              echo '        <button class="btn btn-success register-new-mastitis-btn" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newEntryModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                              title="Registrar Nueva Vacuna">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              // Show Edit/Delete buttons since all animals here have mastitis records
                              echo '        <button class="btn btn-warning edit-mastitis" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                                  data-id="' . htmlspecialchars($row['mastitis_record_id'] ?? '') . '" 
                                                  data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '" 
                                                  data-vacuna="' . htmlspecialchars($row['vh_mastitis_producto'] ?? '') . '" 
                                                  data-dosis="' . htmlspecialchars($row['vh_mastitis_dosis'] ?? '') . '" 
                                                  data-costo="' . htmlspecialchars($row['vh_mastitis_costo'] ?? '') . '" 
                                                  data-fecha="' . htmlspecialchars($row['vh_mastitis_fecha'] ?? '') . '"
                                                  title="Editar Vacuna">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                              echo '        <button class="btn btn-danger delete-mastitis" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                                  data-id="' . htmlspecialchars($row['mastitis_record_id'] ?? '') . '"
                                                  data-tagid="' . htmlspecialchars($row['tagid'] ?? '') . '" 
                                                  title="Eliminar Vacuna">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              
                              echo '    </div>';
                              echo '</td>';
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in mastitis table: " . $e->getMessage());
                      echo "<tr><td colspan='8' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Section for animals without mastitis records -->
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Animales sin Registro de Mastitis</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Acciones</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Tag ID</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                                                         // Get animals without mastitis records
                             $noMastitisQuery = "SELECT 
                                                     v.id AS vacuno_id,
                                                     v.tagid,
                                                     v.nombre
                                                 FROM 
                                                     vacuno v
                                                 WHERE 
                                                     v.genero = 'Hembra'
                                                     AND v.tagid NOT IN (
                                                         SELECT DISTINCT vh_mastitis_tagid 
                                                         FROM vh_mastitis 
                                                         WHERE vh_mastitis_tagid IS NOT NULL
                                                     )
                                                 ORDER BY 
                                                     v.nombre ASC, v.tagid ASC;";

                            $stmt = $conn->prepare($noMastitisQuery);
                            $stmt->execute();
                            $noMastitisData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                         if (empty($noMastitisData)) {
                                 echo "<tr><td colspan='4' class='text-center text-success'>Todas las hembras tienen registros de mastitis</td></tr>";
                             } else {
                                foreach ($noMastitisData as $row) {
                                    echo "<tr>";
                                    echo '<td class="text-center">';
                                    echo '    <button class="btn btn-success btn-sm register-new-mastitis-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#newEntryModal" 
                                                data-tagid-prefill="'.htmlspecialchars($row['tagid'] ?? '').'" 
                                                title="Registrar Nueva Vacuna">
                                                <i class="fas fa-plus"></i> Registrar
                                            </button>';
                                    echo '</td>';
                                    echo "<td>" . htmlspecialchars($row['nombre'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tagid'] ?? '') . "</td>";
                                    echo '<td class="text-center"><span class="badge bg-warning">SIN REGISTRAR</span></td>';
                                    echo "</tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching animals without mastitis: " . $e->getMessage());
                            echo "<tr><td colspan='4' class='text-center text-danger'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTable for VH mastitis -->
<script>
$(document).ready(function() {
    $('#mastitisTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],
        
        // Order by Estatus column descending (now index 7)
        order: [[7, 'desc']],
        
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
                targets: [0], // Actions column
                orderable: false,
                searchable: false
            },
            {
                targets: [5, 6], // Dosis, Costo columns
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
                targets: [1], // Fecha Vacunacion column
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
                targets: [7], // Estatus column
                orderable: true,
                searchable: true
            }
        ]
    });
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    // --- Initialize Modals Once --- 
    var newEntryModalElement = document.getElementById('newEntryModal');
    var newEntryModalInstance = new bootstrap.Modal(newEntryModalElement); 
    // Note: editMastitisModal is created dynamically later, so no need to initialize here.

    // Handle new entry form submission
    $('#saveNewMastitis').click(function() {
        // Validate the form
        var form = document.getElementById('newMastitisForm');
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
            text: `¿Desea registrar la dosis de mastitis ${formData.dosis} ml para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_mastitis.php',
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
                        newEntryModalInstance.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de mastitis ha sido guardado correctamente',
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
    $('.edit-mastitis').click(function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var dosis = $(this).data('dosis');
        var vacuna = $(this).data('vacuna');
        var costo = $(this).data('costo');
        var fecha = $(this).data('fecha');

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

        // Edit Mastitis Modal dialog for editing
        var modalHtml = `
        <div class="modal fade" id="editMastitisModal" tabindex="-1" aria-labelledby="editMastitisModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMastitisModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Vacunacion Mastitis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editMastitisForm">
                            <input type="hidden" id="edit_id" name="id" value="${id}">
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
                            <div class="mb-4">                        
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-syringe"></i>
                                        <label for="edit_vacuna" class="form-label">Vacunas Mastitis</label>
                                        <input type="text" class="form-control" id="edit_vacuna" name="vacuna" value="${vacuna}" readonly required>
                                        <button type="button" class="btn btn-outline-secondary" id="selectVacunaBtn" title="Seleccionar de configuración">
                                            <i class="fas fa-list"></i>
                                        </button>
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
                        <button type="button" class="btn btn-success" id="saveEditMastitis">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Vacuna selection modal
        var vacunaSelectionModal = `
        <div class="modal fade" id="vacunaSelectionModal" tabindex="-1" aria-labelledby="vacunaSelectionModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="vacunaSelectionModalLabel">
                            <i class="fas fa-list me-2"></i>Seleccionar Vacuna de Mastitis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="vacunaSearch" class="form-label">Buscar vacuna:</label>
                            <input type="text" class="form-control" id="vacunaSearch" placeholder="Escriba para buscar...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vacunas disponibles:</label>
                            <div class="list-group" id="vacunaList" style="max-height: 300px; overflow-y: auto;">
                                <?php
                                try {
                                    $sql_vacunas = "SELECT DISTINCT vc_mastitis_vacuna FROM vc_mastitis ORDER BY vc_mastitis_vacuna ASC";
                                    $stmt_vacunas = $conn->prepare($sql_vacunas);
                                    $stmt_vacunas->execute();
                                    $vacunas = $stmt_vacunas->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($vacunas as $vacuna_row) {
                                        echo '<button type="button" class="list-group-item list-group-item-action vacuna-option" data-value="' . htmlspecialchars($vacuna_row['vc_mastitis_vacuna']) . '">' . htmlspecialchars($vacuna_row['vc_mastitis_vacuna']) . '</button>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching vacunas: " . $e->getMessage());
                                    echo '<div class="list-group-item text-danger">Error al cargar vacunas</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Remove any existing modals
        $('#editMastitisModal').remove();
        $('#vacunaSelectionModal').remove();
        
        // Add the modals to the page
        $('body').append(modalHtml);
        $('body').append(vacunaSelectionModal);
        
        // Show the edit modal
        var editModal = new bootstrap.Modal(document.getElementById('editMastitisModal'));
        
        // Set up vacuna selection functionality
        $('#selectVacunaBtn').click(function() {
            var vacunaSelectionModal = new bootstrap.Modal(document.getElementById('vacunaSelectionModal'));
            vacunaSelectionModal.show();
        });
        
        // Vacuna search functionality
        $('#vacunaSearch').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.vacuna-option').each(function() {
                var vacunaName = $(this).text().toLowerCase();
                if (vacunaName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Vacuna selection
        $(document).on('click', '.vacuna-option', function() {
            var selectedVacuna = $(this).data('value');
            $('#edit_vacuna').val(selectedVacuna);
            $('#vacunaSelectionModal').modal('hide');
        });
        
        editModal.show();
        
        // Set value after modal is fully rendered
        $('#edit_vacuna').val(vacuna);
        
        // Handle save button click
        $('#saveEditMastitis').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                vacuna: $('#edit_vacuna').val(),
                dosis: $('#edit_dosis').val(),
                costo: $('#edit_costo').val(),
                fecha: $('#edit_fecha').val()
            };
            
            console.log('Save changes clicked. Form Data being sent:', formData); // Debug log 2
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de mastitis para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_mastitis.php',
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
    $('.delete-mastitis').click(function() {
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
                    url: 'process_mastitis.php',
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

    // Handle new register button click for animals without history
    $(document).on('click', '.register-new-mastitis-btn', function() { 
        // Get tagid from the button's data-tagid-prefill attribute
        var tagid = $(this).data('tagid-prefill'); 
        
        // Clear previous data in the modal
        $('#newMastitisForm')[0].reset();
        $('#new_id').val(''); // Ensure ID is cleared
        
        // Pre-fill the tagid field
        $('#new_tagid').val(tagid);
        
        // Show the new entry modal using the existing instance
        newEntryModalInstance.show(); 
    });
});
</script>

<!-- Mastitis Bar Chart Section -->

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-pink text-white d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Registros de Mastitis por Mes
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Enhanced filters with professional styling -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="yearFilter" class="form-label fw-bold text-muted mb-2">
                        <i class="fas fa-calendar me-2"></i>Año:
                    </label>
                    <select id="yearFilter" class="form-select form-select-lg bg-white border-0 shadow-sm">
                        <option value="all">Todos los años</option>
                        <!-- Year options will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="chartTypeFilter" class="form-label fw-bold text-muted mb-2">
                        <i class="fas fa-chart-pie me-2"></i>Tipo de Gráfico:
                    </label>
                    <select id="chartTypeFilter" class="form-select form-select-lg bg-white border-0 shadow-sm">
                        <option value="bar">Gráfico de Barras</option>
                        <option value="line">Gráfico de Líneas</option>
                    </select>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="mastitisChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Professional CSS Styling for Mastitis Charts -->
<style>
/* Gradient backgrounds */
.bg-gradient-pink {
    background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}

.bg-gradient-purple {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%) !important;
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}

/* Chart container styling */
.chart-container {
    position: relative;
    height: 70vh;
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.chart-container.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 50px;
    height: 50px;
    margin: -25px 0 0 -25px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #e83e8c;
    border-radius: 50%;
    animation: spin 1.2s linear infinite;
    z-index: 1000;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Card styling */
.card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-bottom: none;
    padding: 1.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.card-body {
    padding: 2.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

/* Enhanced form controls */
.form-select {
    border-radius: 12px;
    font-weight: 600;
    min-width: 180px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(232, 62, 140, 0.25);
    border-color: #e83e8c;
    transform: translateY(-2px);
}

.form-select:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.form-label {
    font-size: 1.1rem;
    color: #495057;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Canvas styling */
canvas {
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

canvas:hover {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container {
        height: 60vh;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-header {
        padding: 1.25rem;
    }
    
    .form-select {
        min-width: 140px;
        font-size: 0.9rem;
    }
    
    .form-label {
        font-size: 1rem;
    }
}

/* Enhanced shadows and effects */
.shadow-lg {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
}

/* Typography enhancements */
h5 {
    font-weight: 700;
    letter-spacing: 0.4px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Loading state */
.chart-container.loading canvas {
    opacity: 0.4;
    transition: opacity 0.3s ease;
}

/* Animation for chart elements */
.chart-container {
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced hover effects */
.card:hover .card-header {
    background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%) !important;
}

/* Smooth transitions */
* {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Professional color scheme */
.text-muted {
    color: #6c757d !important;
}

.fw-bold {
    font-weight: 700 !important;
}

/* Enhanced form styling */
.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>

<!-- Chart.js Script for Mastitis Bar Chart -->
<script>
$(document).ready(function() {
    let allMastitisData = [];
    let mastitisChart = null;
    
    // Get mastitis data from the PHP table data
    function getMastitisDataFromTable() {
        const tableData = [];
        $('#mastitisTable tbody tr').each(function() {
            const row = $(this);
            const fecha = row.find('td:eq(1)').text().trim(); // F. Vacunacion column
            const nombre = row.find('td:eq(2)').text().trim(); // Nombre column
            const tagid = row.find('td:eq(3)').text().trim(); // Numero column
            const vacuna = row.find('td:eq(4)').text().trim(); // Vacuna column
            const dosis = row.find('td:eq(5)').text().trim(); // Dosis column
            const costo = row.find('td:eq(6)').text().trim(); // Costo column
            
            // Skip rows with "No hay registros" message
            if (fecha && fecha !== 'No hay registros de mastitis disponibles') {
                // Convert DD/MM/YYYY back to YYYY-MM-DD for chart processing
                let fechaFormatted = fecha;
                if (fecha.includes('/')) {
                    const parts = fecha.split('/');
                    if (parts.length === 3) {
                        fechaFormatted = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                    }
                }
                
                tableData.push({
                    fecha: fechaFormatted,
                    nombre: nombre,
                    tagid: tagid,
                    vacuna: vacuna,
                    dosis: dosis,
                    costo: costo
                });
            }
        });
        
        console.log('Data extracted from table:', tableData);
        return tableData;
    }
    
    // Initialize chart with table data
    function initializeChart() {
        const data = getMastitisDataFromTable();
        
        if (!data || data.length === 0) {
            console.log('No mastitis data available in table');
            $('#mastitisChart').parent().html('<div class="alert alert-info">No hay datos de mastitis disponibles para mostrar en el gráfico.</div>');
            return;
        }
        
        allMastitisData = data;
        populateYearFilter(data);
        createMastitisChart(data);
        
        // Add event listeners for filters
        $('#yearFilter, #chartTypeFilter').on('change', function() {
            updateChart();
        });
    }
    
    // Wait for DataTable to be fully loaded before initializing chart
    setTimeout(function() {
        initializeChart();
    }, 500);
    
    function populateYearFilter(data) {
        // Get unique years from the data
        const years = new Set();
        
        data.forEach(item => {
            if (item.fecha) {
                const year = item.fecha.split('-')[0];
                years.add(year);
            }
        });
        
        // Sort years descending (most recent first)
        const sortedYears = Array.from(years).sort((a, b) => b - a);
        
        // Add options to the dropdown
        const yearFilter = $('#yearFilter');
        yearFilter.find('option:not(:first)').remove(); // Remove existing options except first
        
        sortedYears.forEach(year => {
            yearFilter.append(`<option value="${year}">${year}</option>`);
        });
    }
    
    function updateChart() {
        const selectedYear = $('#yearFilter').val();
        const selectedChartType = $('#chartTypeFilter').val();
        
        let filteredData = [...allMastitisData];
        
        // Filter by year if not "all"
        if (selectedYear !== 'all') {
            filteredData = filteredData.filter(item => item.fecha && item.fecha.startsWith(selectedYear));
        }
        
        // Update chart with filtered data and new chart type
        updateChartData(filteredData, selectedChartType);
    }
    
    function updateChartData(data, chartType) {
        if (mastitisChart) {
            mastitisChart.destroy();
        }
        createMastitisChart(data, chartType);
    }
    
    function createMastitisChart(data, chartType = 'bar') {
        try {
            var ctx = document.getElementById('mastitisChart').getContext('2d');
            
            if (!data || data.length === 0) {
                console.log('No data to create mastitis chart');
                return;
            }
            
            // Group data by month
            const monthlyData = {};
            const monthNames = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];
            
            data.forEach(item => {
                if (item.fecha) {
                    const date = new Date(item.fecha);
                    const year = date.getFullYear();
                    const month = date.getMonth();
                    const monthKey = `${year}-${String(month + 1).padStart(2, '0')}`;
                    const monthLabel = `${monthNames[month]} ${year}`;
                    
                    if (!monthlyData[monthKey]) {
                        monthlyData[monthKey] = {
                            label: monthLabel,
                            count: 0,
                            month: month,
                            year: year
                        };
                    }
                    monthlyData[monthKey].count++;
                }
            });
            
            // Sort by date (year, month)
            const sortedMonths = Object.values(monthlyData).sort((a, b) => {
                if (a.year !== b.year) return a.year - b.year;
                return a.month - b.month;
            });
            
            // Prepare chart data
            const labels = sortedMonths.map(item => item.label);
            const counts = sortedMonths.map(item => item.count);
            
            console.log('Monthly mastitis data:', sortedMonths);
            console.log('Chart labels:', labels);
            console.log('Chart counts:', counts);
            
            // Create professional gradients
            const barGradient = ctx.createLinearGradient(0, 0, 0, 400);
            barGradient.addColorStop(0, 'rgba(232, 62, 140, 0.8)');
            barGradient.addColorStop(1, 'rgba(253, 126, 20, 0.8)');

            const lineGradient = ctx.createLinearGradient(0, 0, 0, 400);
            lineGradient.addColorStop(0, 'rgba(232, 62, 140, 1)');
            lineGradient.addColorStop(1, 'rgba(253, 126, 20, 1)');

            const fillGradient = ctx.createLinearGradient(0, 0, 0, 400);
            fillGradient.addColorStop(0, 'rgba(232, 62, 140, 0.3)');
            fillGradient.addColorStop(0.5, 'rgba(232, 62, 140, 0.1)');
            fillGradient.addColorStop(1, 'rgba(232, 62, 140, 0.05)');
            
            // Determine chart type and dataset configuration
            const isBarChart = chartType === 'bar';
            
            mastitisChart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: '🩺 Registros de Mastitis',
                        data: counts,
                        backgroundColor: isBarChart ? barGradient : fillGradient,
                        borderColor: lineGradient,
                        borderWidth: 4,
                        pointBackgroundColor: 'rgba(232, 62, 140, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 8,
                        pointHoverRadius: 12,
                        pointHoverBackgroundColor: 'rgba(232, 62, 140, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 4,
                        tension: 0.4,
                        fill: !isBarChart,
                        borderRadius: isBarChart ? 8 : 0,
                        borderSkipped: isBarChart ? false : true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2500,
                        easing: 'easeInOutQuart',
                        onProgress: function(animation) {
                            const chart = animation.chart;
                            const ctx = chart.ctx;
                            const dataset = chart.data.datasets[0];
                            const meta = chart.getDatasetMeta(0);
                            
                            if (meta.data.length > 0) {
                                const lastPoint = meta.data[meta.data.length - 1];
                                if (lastPoint) {
                                    ctx.save();
                                    ctx.shadowColor = 'rgba(232, 62, 140, 0.6)';
                                    ctx.shadowBlur = 25;
                                    ctx.shadowOffsetX = 0;
                                    ctx.shadowOffsetY = 0;
                                    ctx.restore();
                                }
                            }
                        },
                        onComplete: function(animation) {
                            const chart = animation.chart;
                            const ctx = chart.ctx;
                            ctx.shadowBlur = 0;
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Registros',
                                color: '#495057',
                                font: {
                                    size: 14,
                                    weight: '700',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: {
                                    bottom: 15
                                }
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12,
                                    weight: '600',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 10,
                                callback: function(value) {
                                    return Math.floor(value);
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.08)',
                                lineWidth: 1,
                                drawBorder: false
                            },
                            border: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                width: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes',
                                color: '#495057',
                                font: {
                                    size: 14,
                                    weight: '700',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: {
                                    top: 15
                                }
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12,
                                    weight: '600',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 10
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.08)',
                                lineWidth: 1,
                                drawBorder: false
                            },
                            border: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                width: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 25,
                                font: {
                                    size: 14,
                                    weight: '700',
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                color: '#495057'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            titleFont: {
                                size: 16,
                                weight: '700',
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            bodyFont: {
                                size: 14,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            padding: 18,
                            cornerRadius: 16,
                            displayColors: false,
                            borderColor: 'rgba(232, 62, 140, 0.8)',
                            borderWidth: 3,
                            callbacks: {
                                label: function(context) {
                                    var index = context.dataIndex;
                                    var dataPoint = data[index];
                                    
                                    var tooltipText = [
                                        '🩺 Registros: ' + context.parsed.y
                                    ];
                                    
                                    if (dataPoint && dataPoint.year) {
                                        tooltipText.unshift('📅 Año: ' + dataPoint.year);
                                    }
                                    
                                    return tooltipText;
                                }
                            }
                        },
                        title: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            hoverBorderWidth: 4,
                            hoverBorderColor: '#fff'
                        },
                        line: {
                            borderWidth: 3
                        },
                        bar: {
                            borderWidth: 2,
                            borderColor: 'rgba(232, 62, 140, 0.9)'
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating mastitis chart:', error);
            $('#mastitisChart').parent().html('<div class="alert alert-danger">Error al crear el gráfico: ' + error.message + '</div>');
        }
    }
});
</script>

