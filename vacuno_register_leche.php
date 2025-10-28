<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Fetch data for Leche Production Chart ---
$lecheMonthlyLabels = [];
$lecheMonthlyValues = [];
$lecheCumulativeData = [];

try {
    // Fetch leche production data with cross-month allocation
    $lecheQuery = "SELECT 
                        vh_leche_fecha_inicio,
                        vh_leche_fecha_fin,
                        vh_leche_peso,
                        vh_leche_precio,
                        vh_leche_tagid
                    FROM vh_leche 
                    WHERE vh_leche_fecha_inicio IS NOT NULL 
                    AND vh_leche_fecha_fin IS NOT NULL 
                    AND vh_leche_peso IS NOT NULL 
                    AND vh_leche_precio IS NOT NULL
                    ORDER BY vh_leche_fecha_inicio ASC";
    
    $lecheStmt = $conn->prepare($lecheQuery);
    $lecheStmt->execute();
    $lecheData = $lecheStmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize monthly totals
    $monthlyLecheTotals = [];
    $currentCumulativeLeche = 0;

    foreach ($lecheData as $leche) {
        $startDate = new DateTime($leche['vh_leche_fecha_inicio']);
        $endDate = new DateTime($leche['vh_leche_fecha_fin']);
        
        // Calculate total production value
        $totalDays = $endDate->diff($startDate)->days + 1;
        $totalValue = $leche['vh_leche_peso'] * $leche['vh_leche_precio'] * $totalDays;
        $dailyValue = $totalValue / $totalDays;
        
        // Distribute daily value across months
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $monthYear = $currentDate->format('Y-m');
            
            if (!isset($monthlyLecheTotals[$monthYear])) {
                $monthlyLecheTotals[$monthYear] = 0;
            }
            
            $monthlyLecheTotals[$monthYear] += $dailyValue;
            $currentDate->add(new DateInterval('P1D'));
        }
    }

    // Sort months chronologically and calculate cumulative values
    ksort($monthlyLecheTotals);
    
    foreach ($monthlyLecheTotals as $monthYear => $monthlyTotal) {
        $lecheMonthlyLabels[] = $monthYear;
        $lecheMonthlyValues[] = round($monthlyTotal, 2);
        
        $currentCumulativeLeche += $monthlyTotal;
        $lecheCumulativeData[] = round($currentCumulativeLeche, 2);
    }

} catch (PDOException $e) {
    error_log("Error fetching leche production data: " . $e->getMessage());
}

$lecheMonthlyLabelsJson = json_encode($lecheMonthlyLabels);
$lecheMonthlyValuesJson = json_encode($lecheMonthlyValues);
$lecheCumulativeDataJson = json_encode($lecheCumulativeData);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vacuno Registro Leche</title>
<!-- Link to the Favicon -->
<link rel="icon" href="images/default_image.png" type="image/x-icon">
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

<style>
/* Custom styles for Leche Production Chart */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.chart-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.chart-controls .form-select {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.chart-controls .form-select:focus {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

.card-header {
    border-bottom: none;
    border-radius: 12px 12px 0 0 !important;
}

.card-body {
    border-radius: 0 0 12px 12px;
}

/* Enhanced chart styling */
#lecheProductionChart {
    border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container {
        margin: 1rem 0;
    }
    
    .chart-controls .form-select {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
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
                        <span class="badge-active">🎯 Registrando Leche</span>
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
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Nombre</th>
                      <th class="text-center">Tag ID</th>
                      <th class="text-center">Leche (kg)</th>
                      <th class="text-center">Precio ($/kg)</th>
                      <th class="text-center">Valor Total ($)</th>
                      <th class="text-center">Estatus</th>
                      <th class="text-center">Acciones</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  try {
                      // Query to get all Female Animals and ALL their milk records (if any)
                        $milkQuery = "
                            SELECT
                                b.tagid AS vacuno_tagid,
                                b.nombre AS animal_nombre,
                                l.id AS leche_id,         -- Will be NULL for animals with no milk records
                                l.vh_leche_fecha_inicio,
                                l.vh_leche_tagid,         -- Matches vacuno_tagid if milk record exists
                                l.vh_leche_peso,
                                l.vh_leche_precio,
                                -- Calculate total_value only if l.id is not null
                                CASE WHEN l.id IS NOT NULL THEN CAST((l.vh_leche_peso * l.vh_leche_precio) AS DECIMAL(10,2)) ELSE NULL END as total_value
                            FROM
                                vacuno b
                            LEFT JOIN
                                vh_leche l ON b.tagid = l.vh_leche_tagid -- Join ALL matching milk records
                            WHERE
                                b.genero = 'Hembra' -- Filter for females only
                            ORDER BY
                                -- Prioritize animals with records (IS NOT NULL -> 0, IS NULL -> 1)
                                CASE WHEN l.id IS NOT NULL THEN 0 ELSE 1 END ASC,
                                -- Then order by animal tag ID to group them
                                b.tagid ASC,
                                -- Within each animal, order their milk records by date descending
                                l.vh_leche_fecha_inicio DESC";

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
                              $lecheFecha = $row['vh_leche_fecha_inicio'] ?? null;
                              
                              echo "<tr>";

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
                              
                              // Column 1: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              // Always show Add Button
                              echo '        <button class="btn btn-success register-new-peso-btn" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newPesoModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['vacuno_tagid'] ?? '').'" 
                                              title="Registrar Nuevo Pesaje Leche">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              if ($hasLeche) {
                                  // Edit Button (only if milk record exists for this row)
                                  echo '        <button class="btn btn-warning edit-peso" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                                  data-id="'.htmlspecialchars($row['leche_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vh_leche_tagid'] ?? '').'" 
                                                  data-peso="'.htmlspecialchars($row['vh_leche_peso'] ?? '').'" 
                                                  data-precio="'.htmlspecialchars($row['vh_leche_precio'] ?? '').'" 
                                                  data-fecha="'.htmlspecialchars($lecheFecha ?? '').'" 
                                                  title="Editar Pesaje">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  // Delete Button (only if milk record exists for this row)
                                  echo '        <button class="btn btn-danger delete-peso" style="height: 30px !important; width: 30px !important; padding: 0 !important; font-size: 1rem !important; line-height: 30px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"
                                                  data-id="'.htmlspecialchars($row['leche_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vh_leche_tagid'] ?? '').'" -- Pass tagid for context
                                                  title="Eliminar Pesaje">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              echo '    </div>';
                              echo '</td>';
                                                            
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

<!-- Leche Production Chart -->
<div class="container chart-container mb-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-milk-alt me-2"></i>
                    Producción de Leche - Valor Mensual y Acumulado
                </h5>
                <div class="chart-controls">
                    <select id="lecheTimeFilter" class="form-select form-select-sm bg-white text-dark">
                        <option value="all">Todos los meses</option>
                        <option value="12" selected>Últimos 12 meses</option>
                        <option value="6">Últimos 6 meses</option>
                        <option value="3">Últimos 3 meses</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="chart-container" style="position: relative; height: 60vh; width: 100%">
                <canvas id="lecheProductionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTable for VH Leche -->
<script>
$(document).ready(function() {
    $('#lecheTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[6, 'desc']],
        
        // Spanish language
        language: {
            url: 'es-ES.json',
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
                targets: [7], // Actions column (new position)
                orderable: false,
                searchable: false
            },
            {
                targets: [3, 4, 5], // Leche, Precio, Valor Total columns (indices shifted)
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
                targets: [0], // Fecha column (index shifted)
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
                targets: [6], // Status column (index shifted)
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

<!-- JavaScript for Leche Production Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxLeche = document.getElementById('lecheProductionChart').getContext('2d');
    const lecheMonthlyLabels = <?php echo $lecheMonthlyLabelsJson; ?>;
    const lecheMonthlyValues = <?php echo $lecheMonthlyValuesJson; ?>;
    const lecheCumulativeData = <?php echo $lecheCumulativeDataJson; ?>;

    // Create gradients for professional look
    const barGradient = ctxLeche.createLinearGradient(0, 0, 0, 400);
    barGradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
    barGradient.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

    const lineGradient = ctxLeche.createLinearGradient(0, 0, 0, 400);
    lineGradient.addColorStop(0, 'rgba(255, 99, 132, 0.8)');
    lineGradient.addColorStop(1, 'rgba(255, 99, 132, 0.2)');

    let lecheChart = new Chart(ctxLeche, {
        type: 'bar',
        data: {
            labels: lecheMonthlyLabels,
            datasets: [
                {
                    label: 'Valor Mensual de Producción',
                    data: lecheMonthlyValues,
                    backgroundColor: barGradient,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Valor Acumulado',
                    data: lecheCumulativeData,
                    type: 'line',
                    backgroundColor: lineGradient,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointHoverBorderColor: '#fff',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 14,
                            weight: '600'
                        },
                        color: '#333'
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleFont: { 
                        size: 16, 
                        weight: 'bold' 
                    },
                    bodyFont: { 
                        size: 14 
                    },
                    padding: 16,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                if (context.datasetIndex === 0) {
                                    // Monthly production value
                                    return label + ': $' + context.parsed.y.toLocaleString('es-ES', { 
                                        minimumFractionDigits: 2, 
                                        maximumFractionDigits: 2 
                                    });
                                } else {
                                    // Cumulative value
                                    return label + ': $' + context.parsed.y.toLocaleString('es-ES', { 
                                        minimumFractionDigits: 2, 
                                        maximumFractionDigits: 2 
                                    });
                                }
                            }
                            return '';
                        },
                        afterBody: function(context) {
                            const monthIndex = context[0].dataIndex;
                            const monthLabel = context[0].label;
                            
                            return [
                                '───────────────────',
                                `Mes: ${monthLabel}`,
                                `Producción Mensual: $${lecheMonthlyValues[monthIndex].toLocaleString('es-ES', { 
                                    minimumFractionDigits: 2, 
                                    maximumFractionDigits: 2 
                                })}`,
                                `Total Acumulado: $${lecheCumulativeData[monthIndex].toLocaleString('es-ES', { 
                                    minimumFractionDigits: 2, 
                                    maximumFractionDigits: 2 
                                })}`
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { 
                        display: true, 
                        text: 'Período (Año-Mes)', 
                        color: '#333', 
                        font: { 
                            size: 14, 
                            weight: 'bold' 
                        } 
                    },
                    ticks: { 
                        color: '#666', 
                        font: { 
                            size: 12 
                        } 
                    },
                    grid: { 
                        color: 'rgba(0, 0, 0, 0.1)', 
                        drawBorder: true 
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: { 
                        display: true, 
                        text: 'Valor Mensual ($)', 
                        color: '#333', 
                        font: { 
                            size: 14, 
                            weight: 'bold' 
                        } 
                    },
                    ticks: {
                        color: '#666',
                        font: { 
                            size: 12 
                        },
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString('es-ES', { 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            });
                        }
                    },
                    grid: { 
                        color: 'rgba(54, 162, 235, 0.1)', 
                        drawBorder: false 
                    }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: { 
                        display: true, 
                        text: 'Valor Acumulado ($)', 
                        color: '#333', 
                        font: { 
                            size: 14, 
                            weight: 'bold' 
                        } 
                    },
                    ticks: {
                        color: '#666',
                        font: { 
                            size: 12 
                        },
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString('es-ES', { 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            });
                        }
                    }
                }
            }
        }
    });

    // Add time filter functionality
    document.getElementById('lecheTimeFilter').addEventListener('change', function() {
        const selectedRange = this.value;
        let filteredLabels = [...lecheMonthlyLabels];
        let filteredValues = [...lecheMonthlyValues];
        let filteredCumulative = [...lecheCumulativeData];

        if (selectedRange !== 'all' && filteredLabels.length > parseInt(selectedRange)) {
            const startIndex = filteredLabels.length - parseInt(selectedRange);
            filteredLabels = filteredLabels.slice(startIndex);
            filteredValues = filteredValues.slice(startIndex);
            filteredCumulative = filteredCumulative.slice(startIndex);
        }

        // Update chart data
        lecheChart.data.labels = filteredLabels;
        lecheChart.data.datasets[0].data = filteredValues;
        lecheChart.data.datasets[1].data = filteredCumulative;
        lecheChart.update('active');
    });
});
</script>

