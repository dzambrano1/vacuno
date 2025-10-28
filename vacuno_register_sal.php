<?php
require_once './pdo_conexion.php';  

// Debug connection type
if (!($conn instanceof PDO)) {
    die("Error: Connection is not a PDO instance. Please check your connection setup.");
}
// Enable PDO error mode to get better error messages
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Fetch data for Concentrado Expense Chart ---
$salMonthlyLabels = [];
$salMonthlyValues = [];
$salCumulativeData = [];

try {
    // First try to fetch sal expense data with date range fields
    $salQuery = "SELECT 
                            vh_sal_fecha_inicio,
                            vh_sal_fecha_fin,
                            vh_sal_racion,
                            vh_sal_costo,
                            vh_sal_tagid
                        FROM vh_sal 
                        WHERE vh_sal_fecha_inicio IS NOT NULL 
                        AND vh_sal_fecha_fin IS NOT NULL 
                        AND vh_sal_racion IS NOT NULL 
                        AND vh_sal_costo IS NOT NULL
                        ORDER BY vh_sal_fecha_inicio ASC";
    
    error_log("Executing sal query with date range fields: " . $salQuery);
    
    $salStmt = $conn->prepare($salQuery);
    $salStmt->execute();
    $salData = $salStmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Concentrado query with date range returned " . count($salData) . " records");
    
    // If no data found with date range fields, try with single date field
    if (empty($salData)) {
        error_log("No data found with date range fields, trying single date field");
        
        $salQuery = "SELECT 
                                vh_sal_fecha,
                                vh_sal_fecha as vh_sal_fecha_inicio,
                                vh_sal_fecha as vh_sal_fecha_fin,
                                vh_sal_racion,
                                vh_sal_costo,
                                vh_sal_tagid
                            FROM vh_sal 
                            WHERE vh_sal_fecha IS NOT NULL 
                            AND vh_sal_racion IS NOT NULL 
                            AND vh_sal_costo IS NOT NULL
                            ORDER BY vh_sal_fecha ASC";
        
        error_log("Executing sal query with single date field: " . $salQuery);
        
        $salStmt = $conn->prepare($salQuery);
        $salStmt->execute();
        $salData = $salStmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Concentrado query with single date field returned " . count($salData) . " records");
    }

    // Initialize monthly totals
    $monthlyConcentradoTotals = [];
    $currentCumulativeConcentrado = 0;

    foreach ($salData as $sal) {
        $startDate = new DateTime($sal['vh_sal_fecha_inicio']);
        $endDate = new DateTime($sal['vh_sal_fecha_fin']);
        
        // Calculate total expense value
        $totalDays = $endDate->diff($startDate)->days + 1;
        $totalValue = $sal['vh_sal_racion'] * $sal['vh_sal_costo'] * $totalDays;
        $dailyValue = $totalValue / $totalDays;
        
        error_log("Processing sal: Start=" . $sal['vh_sal_fecha_inicio'] . 
                 ", End=" . $sal['vh_sal_fecha_fin'] . 
                 ", Days=" . $totalDays . 
                 ", TotalValue=" . $totalValue . 
                 ", DailyValue=" . $dailyValue);
        
        // Distribute daily value across months
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $monthYear = $currentDate->format('Y-m');
            
            if (!isset($monthlyConcentradoTotals[$monthYear])) {
                $monthlyConcentradoTotals[$monthYear] = 0;
            }
            
            $monthlyConcentradoTotals[$monthYear] += $dailyValue;
            $currentDate->add(new DateInterval('P1D'));
        }
    }
    
    error_log("Monthly totals calculated: " . print_r($monthlyConcentradoTotals, true));

    // Sort months chronologically and calculate cumulative values
    ksort($monthlyConcentradoTotals);
    
    foreach ($monthlyConcentradoTotals as $monthYear => $monthlyTotal) {
        $salMonthlyLabels[] = $monthYear;
        $salMonthlyValues[] = round($monthlyTotal, 2);
        
        $currentCumulativeConcentrado += $monthlyTotal;
        $salCumulativeData[] = round($currentCumulativeConcentrado, 2);
    }

} catch (PDOException $e) {
    error_log("Error fetching sal expense data: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
}

$salMonthlyLabelsJson = json_encode($salMonthlyLabels);
$salMonthlyValuesJson = json_encode($salMonthlyValues);
$salCumulativeDataJson = json_encode($salCumulativeData);

// Debug information
error_log("Sal Chart Data - Labels: " . print_r($salMonthlyLabels, true));
error_log("Sal Chart Data - Values: " . print_r($salMonthlyValues, true));
error_log("Sal Chart Data - Cumulative: " . print_r($salCumulativeData, true));

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vacuno - Registro de Sal</title>
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
/* Custom styles for Sal Expense Chart */
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
#salExpenseChart {
    border-radius: 8px;
}

/* Action buttons width reduction */
.btn-group .btn {
    width: 90% !important;
    min-width: auto !important;
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
                        <span class="badge-active">🎯 Registrando Sal</span>
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

    <!-- New Sal Entry Modal -->
<div class="modal fade" id="newSalModal" tabindex="-1" aria-labelledby="newSalModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSalModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Registro Sal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newSalForm">
                    <input type="hidden" name="id" id="new_id" value="">
                <div class="mb-4">
                <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-tag"></i>
                                <label for="new_tagid" class="form-label">Tag ID</label>
                                <input type="text" class="form-control" id="new_tagid" name="tagid" required>
                            </span>
                        </div>
                    </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                                <label for="new_fecha_inicio" class="form-label">Inicio</label>
                                <input type="date" class="form-control" id="new_fecha_inicio" name="fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required>
                            </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                                <label for="new_fecha_fin" class="form-label">Fin</label>
                                <input type="date" class="form-control" id="new_fecha_fin" name="fecha_fin" value="<?php echo date('Y-m-d'); ?>" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-syringe"></i>
                                <label for="new_etapa" class="form-label">Etapa</label>
                                <select class="form-select" id="new_etapa" name="etapa" required>
                                    <option value="">Seleccionar</option>
                                    <?php
                                    try {
                                        $sql_etapas = "SELECT DISTINCT vc_etapas_nombre FROM vc_etapas ORDER BY vc_etapas_nombre ASC";
                                        $stmt_etapas = $conn->prepare($sql_etapas);
                                        $stmt_etapas->execute();
                                        $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($etapas as $etapa_row) {
                                            echo '<option value="' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '</option>';
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error fetching etapas: " . $e->getMessage());
                                        echo '<option value="">Error al cargar etapas</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-syringe"></i>
                                <label for="new_alimento" class="form-label">Sal</label>
                                <select class="form-select" id="new_alimento" name="alimento" required>
                                    <option value="">Productos</option>
                                    <?php
                                    try {
                                        $sql_alimentos = "SELECT DISTINCT vc_sal_nombre FROM vc_sal ORDER BY vc_sal_nombre ASC";
                                        $stmt_alimentos = $conn->prepare($sql_alimentos);
                                        $stmt_alimentos->execute();
                                        $alimentos = $stmt_alimentos->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($alimentos as $alimento_row) {
                                            echo '<option value="' . htmlspecialchars($alimento_row['vc_sal_nombre']) . '">' . htmlspecialchars($alimento_row['vc_sal_nombre']) . '</option>';
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error fetching alimentos: " . $e->getMessage());
                                        echo '<option value="">Error al cargar alimentos</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-weight"></i>
                                <label for="new_racion" class="form-label">Racion</label>
                                <input type="text" class="form-control" id="new_racion" name="racion" required>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                            <i class="fa-solid fa-dollar-sign"></i>
                                <label for="new_costo" class="form-label">Costo</label>
                                <input type="text" class="form-control" id="new_costo" name="costo" required>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="saveNewSal">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DataTable for vh_sal records -->

<div class="container table-section" style="display: block;">
        <div class="table-responsive">
            <table id="salTable" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center">Acciones</th>
                    <th class="text-center">Fecha Inicio</th>
                    <th class="text-center">Fecha Fin</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Tag ID</th>
                    <th class="text-center">Etapa</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Racion (kg)</th>
                    <th class="text-center">Costo ($/kg)</th>
                    <th class="text-center">Total ($/dia)</th>
                    <th class="text-center">Estatus</th>
                </tr>
                </thead>
                <tbody>
                <?php
            try {
                      // Query to get all Animals and ALL their Sal records (if any)
                        $salQuery = "
                            SELECT
                                v.tagid AS vacuno_tagid,
                                v.nombre AS animal_nombre,
                                v.etapa AS vacuno_etapa,
                                c.id AS sal_id,         -- Will be NULL for animals with no sal records
                                c.vh_sal_fecha_inicio,
                                c.vh_sal_fecha_fin,
                                c.vh_sal_tagid,         -- Matches vacuno_tagid if sal exists
                                c.vh_sal_etapa,
                                c.vh_sal_producto,
                                c.vh_sal_racion,
                                c.vh_sal_costo,
                                -- Calculate total_value only if c.id is not null
                                CASE WHEN c.id IS NOT NULL THEN CAST((c.vh_sal_racion * c.vh_sal_costo) AS DECIMAL(10,2)) ELSE NULL END as total_value
                            FROM
                                vacuno v
                            LEFT JOIN
                                vh_sal c ON v.tagid = c.vh_sal_tagid -- Join ALL matching sal records
                            ORDER BY
                                -- Prioritize animals with records (IS NOT NULL -> 0, IS NULL -> 1)
                                CASE WHEN c.id IS NOT NULL THEN 0 ELSE 1 END ASC,
                                -- Then order by animal tag ID to group them
                                v.tagid ASC,
                                -- Within each animal, order their sal records by date descending
                                c.vh_sal_fecha_inicio DESC";

                        $stmt = $conn->prepare($salQuery);
                        $stmt->execute();
                        $salData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // If no data, display a message
                      if (empty($salData)) {
                          echo "<tr><td colspan='10' class='text-center'>No hay animales registrados</td></tr>"; // Message adjusted
                      } else {
                          // Get vigencia setting for sal records
                          $vigencia = 30; // Default value
                          try {
                              $configQuery = "SELECT v_vencimiento_sal FROM v_vencimiento LIMIT 1";
                              $configStmt = $conn->prepare($configQuery);
                              $configStmt->execute();
                              
                              // Explicitly use PDO fetch method
                              $row = $configStmt->fetch(PDO::FETCH_ASSOC);
                              if ($row && isset($row['v_vencimiento_sal'])) {
                                  $vigencia = intval($row['v_vencimiento_sal']);
                              }
                          } catch (PDOException $e) {
                              error_log("Error fetching configuration: " . $e->getMessage());
                              // Continue with default value
                          }
                          
                          $currentDate = new DateTime();
                          
                          foreach ($salData as $row) {
                              $hasSal = !empty($row['sal_id']);
                              $salFecha = $row['vh_sal_fecha_inicio'] ?? null;
                              $salFechaInicio = $row['vh_sal_fecha_inicio'] ?? null;
                              $salFechaFin = $row['vh_sal_fecha_fin'] ?? null;

                              echo "<tr>";

                              // Column 1: Actions
                              echo '<td class="text-center">';
                              echo '    <div class="btn-group" role="group">';
                              // Always show Add Button
                              echo '        <button class="btn btn-success btn-sm" 
                                              data-bs-toggle="modal" 
                                              data-bs-target="#newSalModal" 
                                              data-tagid-prefill="'.htmlspecialchars($row['vacuno_tagid'] ?? '').'" 
                                              title="Registrar Nuevo Sal">
                                              <i class="fas fa-plus"></i>
                                          </button>';
                              
                              if ($hasSal) {
                                  // Edit Button (only if sal exists)
                                  echo '        <button class="btn btn-warning btn-sm edit-sal" 
                                                  data-id="'.htmlspecialchars($row['sal_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vacuno_tagid'] ?? '').'" 
                                                  data-etapa="'.htmlspecialchars($row['vacuno_etapa'] ?? '').'" 
                                                  data-producto="'.htmlspecialchars($row['vh_sal_producto'] ?? '').'" 
                                                  data-racion="'.htmlspecialchars($row['vh_sal_racion'] ?? '').'" 
                                                  data-costo="'.htmlspecialchars($row['vh_sal_costo'] ?? '').'" 
                                                  data-fecha-inicio="'.htmlspecialchars($salFechaInicio ?? '').'"
                                                  data-fecha-fin="'.htmlspecialchars($salFechaFin ?? '').'"
                                                  title="Editar Registro"
                                                  data-debug="DB: '.htmlspecialchars($row['vh_sal_producto'] ?? 'NULL').'">
                                                  <i class="fas fa-edit"></i>
                                              </button>';
                                  // Delete Button (only if sal exists)
                                  echo '        <button class="btn btn-danger btn-sm delete-sal" 
                                                  data-id="'.htmlspecialchars($row['sal_id'] ?? '').'" 
                                                  data-tagid="'.htmlspecialchars($row['vacuno_tagid'] ?? '').'" 
                                                  title="Eliminar Registro">
                                                  <i class="fas fa-trash"></i>
                                              </button>';
                              }
                              echo '    </div>';
                              echo '</td>';

                              // Column 2: Fecha Sal (or N/A)
                              echo "<td>" . ($salFechaInicio ? htmlspecialchars(date('d/m/Y', strtotime($salFechaInicio))) : 'N/A') . "</td>";
                              // Column 3: Fecha Fin
                              echo "<td>" . ($salFechaFin ? htmlspecialchars(date('d/m/Y', strtotime($salFechaFin))) : 'N/A') . "</td>";
                              // Column 4: Nombre Animal
                              echo "<td>" . htmlspecialchars($row['animal_nombre'] ?? 'N/A') . "</td>";
                              // Column 4: Tag ID Animal
                              echo "<td>" . htmlspecialchars($row['vacuno_tagid'] ?? 'N/A') . "</td>";
                              // Column 5: Etapa (or N/A)
                              echo "<td>" . htmlspecialchars($row['vacuno_etapa'] ?? 'N/A') . "</td>";
                              // Column 6: Producto (or N/A)
                              echo "<td>" . ($hasSal ? htmlspecialchars($row['vh_sal_producto'] ?? '') : 'N/A') . "</td>";
                              // Column 7: Racion (or N/A)
                              echo "<td>" . ($hasSal ? htmlspecialchars($row['vh_sal_racion'] ?? '') : 'N/A') . "</td>";
                              // Column 8: Costo (or N/A)
                              echo "<td>" . ($hasSal ? htmlspecialchars($row['vh_sal_costo'] ?? '') : 'N/A') . "</td>";
                              // Column 9: Valor Total (or N/A)
                              echo "<td>" . ($hasSal && isset($row['total_value']) ? htmlspecialchars($row['total_value']) : 'N/A') . "</td>";
                              
                              // Column 10: Estatus (or N/A)
                              if ($hasSal && $salFechaInicio) {
                                  try {
                                      $salDate = new DateTime($salFechaInicio   );
                                      $dueDate = clone $salDate;
                                      $dueDate->modify("+{$vigencia} days");
                                      
                                      if ($currentDate > $dueDate) {
                                          echo '<td class="text-center"><span class="badge bg-danger">VENCIDO</span></td>';
                                      } else {
                                          echo '<td class="text-center"><span class="badge bg-success">VIGENTE</span></td>';
                                      }
                                  } catch (Exception $e) {
                                      error_log("Date error: " . $e->getMessage() . " for date: " . $salFechaInicio);
                                      echo '<td class="text-center"><span class="badge bg-warning">ERROR FECHA</span></td>';
                                  }
                              } else {
                                  echo '<td class="text-center"><span class="badge bg-secondary">Sin Registro</span></td>'; // Status if no Sal
                              }
                              
                              echo "</tr>";
                          }
                      }
                  } catch (PDOException $e) {
                      error_log("Error in Sal table: " . $e->getMessage());
                      echo "<tr><td colspan='10' class='text-center'>Error al cargar los datos: " . $e->getMessage() . "</td></tr>"; // Adjusted colspan to 10
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
</div>

<!-- Initialize DataTable for VH Sal -->
<script>
$(document).ready(function() {
    $('#salTable').DataTable({
        // Set initial page length
        pageLength: 25,
        
        // Configure length menu options
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],
        
        // Order by fecha (date) column descending
        order: [[1, 'desc']],
        
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
                targets: [0], // Actions column
                orderable: false,
                searchable: false
            },
            {
                targets: [1, 2], // Fecha Inicio, Fecha Fin columns
                type: 'date-eu', // Help DataTables sort European date format
            },
            {
                targets: [6, 7, 8, 9], // Racion, Costo, Valor Total columns
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        const number = parseFloat(data);
                        if (!isNaN(number)) {
                            return number.toLocaleString('es-ES', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            return data; // Return original if parsing failed but wasn't N/A
                        }
                    }
                    return data;
                }
            },
            {
                targets: [1], // Fecha column
                type: 'date-eu', // Help DataTables sort European date format
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data === 'N/A') return data; // Pass through 'N/A'
                        // Date is already formatted DD/MM/YYYY in PHP
                        return data; 
                    }
                    // For sorting/filtering, return the original YYYY-MM-DD if possible, or null
                    if (type === 'sort' || type === 'filter') {
                         // We need the original YYYY-MM-DD date here for correct sorting.
                         // Let's assume the raw data is the 2nd element in the row array `row[1]`
                         // Note: This depends on DataTables internal structure and might need adjustment
                         // A better approach is to fetch YYYY-MM-DD in PHP and pass it via a hidden column or data attribute
                         // For now, let's try getting it from the raw row data for the corresponding display column
                         // If the display data `data` is 'N/A', sorting value should be null or minimal
                         if (data === 'N/A') return null; 
                         // Attempt to convert DD/MM/YYYY back to YYYY-MM-DD for sorting
                         const parts = data.split('/');
                         if (parts.length === 3) {
                            return parts[2] + '-' + parts[1] + '-' + parts[0];
                         }
                         return null; // Fallback if conversion fails
                    }
                    return data;
                }
            },
            {
                targets: [9], // Status column
                orderable: true,
                searchable: true
            }
        ]
    });
});
</script>

<!-- Sal Expense Chart -->
<div class="container chart-container mb-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Gastos de Sal - Valor Mensual y Acumulado
                </h5>
                <div class="chart-controls">
                    <select id="salTimeFilter" class="form-select form-select-sm bg-white text-dark">
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
                <canvas id="salExpenseChart"></canvas>
                <!-- Fallback message for when no data is available -->
                <div id="chartNoDataMessage" class="text-center p-4" style="display: none;">
                    <p class="text-muted">No hay datos de gastos de sal disponibles para mostrar en el gráfico.</p>
                </div>
                <!-- Chart status indicator -->
                <div id="chartStatus" class="text-center p-2">
                    <small class="text-info">Estado del gráfico: Inicializando...</small>
                </div>
            </div>
            <!-- Debug information (remove in production) -->
            <div class="mt-3 p-2 bg-light rounded">
                <small class="text-muted">
                    <strong>Debug Info:</strong><br>
                    Labels: <?php echo count($salMonthlyLabels); ?> meses<br>
                    Values: <?php echo count($salMonthlyValues); ?> valores<br>
                    Cumulative: <?php echo count($salCumulativeData); ?> acumulados<br>
                    <strong>Chart Element ID:</strong> salExpenseChart<br>
                    <strong>Chart Container:</strong> <span id="chartContainerStatus">Verificando...</span>
                </small>
            </div>
            
            <!-- Simple test to verify JavaScript is working -->
            <script>
            console.log('Test script running...');
            console.log('Chart element exists:', !!document.getElementById('salExpenseChart'));
            console.log('Chart container exists:', !!document.getElementById('chartContainerStatus'));
            
            // Update container status immediately
            document.addEventListener('DOMContentLoaded', function() {
                const containerStatus = document.getElementById('chartContainerStatus');
                const chartElement = document.getElementById('salExpenseChart');
                
                if (containerStatus) {
                    if (chartElement) {
                        containerStatus.innerHTML = '<span class="text-success">✓ Encontrado (DOM Ready)</span>';
                    } else {
                        containerStatus.innerHTML = '<span class="text-danger">✗ NO ENCONTRADO (DOM Ready)</span>';
                    }
                }
                
                console.log('DOM Ready - Chart element:', chartElement);
                console.log('Chart element tagName:', chartElement ? chartElement.tagName : 'null');
                console.log('Chart element id:', chartElement ? chartElement.id : 'null');
            });
            
            </script>
        </div>
    </div>
</div>

<!-- JavaScript for Sal Expense Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing chart...');
    
    // Update status
    const chartStatus = document.getElementById('chartStatus');
    if (chartStatus) {
        chartStatus.innerHTML = '<small class="text-info">Estado del gráfico: DOM cargado, buscando elemento...</small>';
    }
    
    const ctxSal = document.getElementById('salExpenseChart');
    
    // Update container status
    const containerStatus = document.getElementById('chartContainerStatus');
    if (containerStatus) {
        if (ctxSal) {
            containerStatus.innerHTML = '<span class="text-success">✓ Encontrado</span>';
        } else {
            containerStatus.innerHTML = '<span class="text-danger">✗ NO ENCONTRADO</span>';
        }
    }
    
    if (!ctxSal) {
        console.error('Chart canvas element not found');
        if (chartStatus) {
            chartStatus.innerHTML = '<small class="text-danger">Error: Elemento del gráfico no encontrado</small>';
        }
        return;
    }
    
    if (chartStatus) {
        chartStatus.innerHTML = '<small class="text-info">Estado del gráfico: Elemento encontrado, cargando datos...</small>';
    }
    
    console.log('Chart canvas found, loading data...');
    
    try {
        const SalMonthlyLabels = <?php echo $salMonthlyLabelsJson; ?>;
        const SalMonthlyValues = <?php echo $salMonthlyValuesJson; ?>;
        const SalCumulativeData = <?php echo $salCumulativeDataJson; ?>;
        
        console.log('Chart data loaded:', {
            labels: SalMonthlyLabels,
            values: SalMonthlyValues,
            cumulative: SalCumulativeData
        });
        
        if (chartStatus) {
            chartStatus.innerHTML = '<small class="text-info">Estado del gráfico: Datos cargados, verificando...</small>';
        }
        
        // Check if we have data to display
        if (!SalMonthlyLabels || SalMonthlyLabels.length === 0) {
            console.log('No data available for chart, using sample data for testing');
            // Use sample data for testing if no real data is available
            SalMonthlyLabels = ['2024-01', '2024-02', '2024-03', '2024-04', '2024-05'];
            SalMonthlyValues = [1500.50, 1800.75, 2200.25, 1950.00, 2400.80];
            SalCumulativeData = [1500.50, 3301.25, 5501.50, 7451.50, 9852.30];
            
            // Show the no data message
            const noDataMessage = document.getElementById('chartNoDataMessage');
            if (noDataMessage) {
                noDataMessage.style.display = 'block';
            }
            
            if (chartStatus) {
                chartStatus.innerHTML = '<small class="text-warning">Estado del gráfico: Usando datos de muestra</small>';
            }
        } else {
            if (chartStatus) {
                chartStatus.innerHTML = '<small class="text-success">Estado del gráfico: Datos reales encontrados</small>';
            }
        }

        if (chartStatus) {
            chartStatus.innerHTML = '<small class="text-info">Estado del gráfico: Creando gráfico...</small>';
        }

        // Create gradients for professional look
        const barGradient = ctxSal.getContext('2d').createLinearGradient(0, 0, 0, 400);
        barGradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
        barGradient.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

        const lineGradient = ctxSal.getContext('2d').createLinearGradient(0, 0, 0, 400);
        lineGradient.addColorStop(0, 'rgba(255, 99, 132, 0.8)');
        lineGradient.addColorStop(1, 'rgba(255, 99, 132, 0.2)');

        console.log('Creating Chart.js instance...');
        
        let salChart = new Chart(ctxSal, {
            type: 'bar',
            data: {
                labels: SalMonthlyLabels,
                datasets: [
                    {
                        label: 'Valor Mensual de Gastos',
                        data: SalMonthlyValues,
                        backgroundColor: barGradient,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Valor Acumulado',
                        data: SalCumulativeData,
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
                                        // Monthly expense value
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
                                    `Gasto Mensual: $${SalMonthlyValues[monthIndex].toLocaleString('es-ES', { 
                                        minimumFractionDigits: 2, 
                                        maximumFractionDigits: 2 
                                    })}`,
                                    `Total Acumulado: $${SalCumulativeData[monthIndex].toLocaleString('es-ES', { 
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
        const timeFilter = document.getElementById('salTimeFilter');
        if (timeFilter) {
            timeFilter.addEventListener('change', function() {
                const selectedRange = this.value;
                let filteredLabels = [...SalMonthlyLabels];
                let filteredValues = [...SalMonthlyValues];
                let filteredCumulative = [...SalCumulativeData];

                if (selectedRange !== 'all' && filteredLabels.length > parseInt(selectedRange)) {
                    const startIndex = filteredLabels.length - parseInt(selectedRange);
                    filteredLabels = filteredLabels.slice(startIndex);
                    filteredValues = filteredValues.slice(startIndex);
                    filteredCumulative = filteredCumulative.slice(startIndex);
                }

                // Update chart data
                SalChart.data.labels = filteredLabels;
                salChart.data.datasets[0].data = filteredValues;
                salChart.data.datasets[1].data = filteredCumulative;
                salChart.update('active');
            });
        }
        
        console.log('Chart initialized successfully');
        
        if (chartStatus) {
            chartStatus.innerHTML = '<small class="text-success">Estado del gráfico: ¡Gráfico cargado exitosamente!</small>';
        }
        
    } catch (error) {
        console.error('Error initializing chart:', error);
        
        if (chartStatus) {
            chartStatus.innerHTML = '<small class="text-danger">Error al cargar el gráfico: ' + error.message + '</small>';
        }
        
        // Show error message on the page
        const chartContainer = ctxConcentrado.parentElement;
        chartContainer.innerHTML = `
            <div class="text-center p-4">
                <p class="text-danger">Error al cargar el gráfico: ${error.message}</p>
                <p class="text-muted">Por favor, revise la consola del navegador para más detalles.</p>
            </div>
        `;
    }
});
</script>

<!-- JavaScript for Edit and Delete buttons -->
<script>
$(document).ready(function() {
    var newSalModalEl = document.getElementById('newSalModal');
    var tagIdInput = document.getElementById('new_tagid');

    // --- Pre-fill Tag ID when New Sal Modal opens --- 
    if (newSalModalEl && tagIdInput) {
        newSalModalEl.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget; 
            
            if (button) { // Check if modal was triggered by a button
                // Extract info from data-* attributes
                var tagid = button.getAttribute('data-tagid-prefill');
                
                // Update the modal's input field
                if (tagid) {
                    tagIdInput.value = tagid;
                } else {
                     tagIdInput.value = ''; // Clear if no tagid passed
                }
            } else {
                tagIdInput.value = ''; // Clear if opened programmatically without a relatedTarget
            }
        });

        // Optional: Clear the input when the modal is hidden to avoid stale data
        newSalModalEl.addEventListener('hidden.bs.modal', function (event) {
            tagIdInput.value = ''; 
            // Optionally reset form validation state
            $('#newSalForm').removeClass('was-validated'); 
            document.getElementById('newSalForm').reset(); // Reset other fields too
        });
    }
    // --- End Pre-fill Logic ---
    
    // Handle new entry form submission
    $('#saveNewSal').click(function() {
        // Validate the form
        var form = document.getElementById('newSalForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        var formData = {
            tagid: $('#new_tagid').val(),
            racion: $('#new_racion').val(),
            etapa: $('#new_etapa').val(),
            producto: $('#new_alimento').val(),
            costo: $('#new_costo').val(),
            fecha_inicio: $('#new_fecha_inicio').val(),
            fecha_fin: $('#new_fecha_fin').val()
        };
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `¿Desea registrar el registro de Sal ${formData.racion} kg para el animal con Tag ID ${formData.tagid}?`,
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
                    url: 'process_sal.php',
                    type: 'POST',
                    data: {
                        action: 'insert',
                        tagid: formData.tagid,
                        racion: formData.racion,
                        etapa: formData.etapa,
                        producto: formData.producto,
                        costo: formData.costo,
                        fecha_inicio: formData.fecha_inicio,
                        fecha_fin: formData.fecha_fin
                    },
                    success: function(response) {
                        // Close the modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('newSalModal'));
                        modal.hide();
                        
                        // Show success message
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            text: 'El registro de Sal ha sido guardado correctamente',
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

    // Handle edit button click (using event delegation for dynamically created elements)
    $(document).on('click', '.edit-sal', function() {
        var id = $(this).data('id');
        var tagid = $(this).data('tagid');
        var etapa = $(this).data('etapa');
        var producto = $(this).data('producto');
        var racion = $(this).data('racion');
        var costo = $(this).data('costo');
        var fecha_inicio = $(this).data('fecha-inicio');
        var fecha_fin = $(this).data('fecha-fin');
        
        // Debug logging for captured values
        console.log('Edit button clicked - Captured values:');
        console.log('id:', id);
        console.log('tagid:', tagid);
        console.log('etapa:', etapa);
        console.log('producto:', producto);
        console.log('racion:', racion);
        console.log('costo:', costo);
        console.log('fecha_inicio:', fecha_inicio);
        console.log('fecha_fin:', fecha_fin);
        
        // Additional debug info from data attributes
        var debugInfo = $(this).data('debug');
        console.log('Debug info from button:', debugInfo);
        console.log('Raw producto data attribute:', $(this).attr('data-producto'));
        
        // Edit Sal Modal dialog for editing

        var modalHtml = `
        <div class="modal fade" id="editSalModal" tabindex="-1" aria-labelledby="editSalModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSalModalLabel">
                            <i class="fas fa-weight me-2"></i>Editar Sal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editSalForm">
                            <input type="hidden" id="edit_id" value="${id}">
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
                                        <i class="fas fa-calendar"></i>
                                        <label for="edit_fecha_inicio" class="form-label">Inicio</label>
                                        <input type="date" class="form-control" id="edit_fecha_inicio" value="${fecha_inicio}" required>
                                    </span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">
                                       <i class="fas fa-calendar"></i>
                                       <label for="edit_fecha_fin" class="form-label">Fin</label>
                                       <input type="date" class="form-control" id="edit_fecha_fin" value="${fecha_fin}" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-syringe"></i>
                                <label for="edit_producto" class="form-label">Producto</label>
                                <input type="text" class="form-control" id="edit_producto" name="producto" value="${producto}" readonly required>
                                <button type="button" class="btn btn-outline-secondary" id="selectProductoBtn" title="Seleccionar de configuración">
                                    <i class="fas fa-list"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-syringe"></i>
                                        <label for="edit_etapa" class="form-label">Etapa</label>
                                        <input type="text" class="form-control" id="edit_etapa" name="etapa" value="${etapa}" readonly required>
                                        <button type="button" class="btn btn-outline-secondary" id="selectEtapaBtn" title="Seleccionar de configuración">
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-weight"></i>
                                        <label for="edit_racion" class="form-label">Racion (kg)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_racion" value="${racion}" required>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-dollar-sign"></i>
                                        <label for="edit_costo" class="form-label">Costo ($/kg)</label>
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
                        <button type="button" class="btn btn-success" id="saveEditSal">
                            <i class="fas fa-save me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        // Product selection modal
        var productSelectionModal = `
        <div class="modal fade" id="productSelectionModal" tabindex="-1" aria-labelledby="productSelectionModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productSelectionModalLabel">
                            <i class="fas fa-list me-2"></i>Seleccionar Producto de Sal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productSearch" class="form-label">Buscar producto:</label>
                            <input type="text" class="form-control" id="productSearch" placeholder="Escriba para buscar...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Productos disponibles:</label>
                            <div class="list-group" id="productList" style="max-height: 300px; overflow-y: auto;">
                                <?php
                                try {
                                    $sql_alimentos = "SELECT DISTINCT vc_sal_nombre FROM vc_sal ORDER BY vc_sal_nombre ASC";
                                    $stmt_alimentos = $conn->prepare($sql_alimentos);
                                    $stmt_alimentos->execute();
                                    $alimentos = $stmt_alimentos->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($alimentos as $alimento_row) {
                                        echo '<button type="button" class="list-group-item list-group-item-action product-option" data-value="' . htmlspecialchars($alimento_row['vc_sal_nombre']) . '">' . htmlspecialchars($alimento_row['vc_sal_nombre']) . '</button>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching sals: " . $e->getMessage());
                                    echo '<div class="list-group-item text-danger">Error al cargar sals</div>';
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
        
        // Etapa selection modal
        var etapaSelectionModal = `
        <div class="modal fade" id="etapaSelectionModal" tabindex="-1" aria-labelledby="etapaSelectionModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="etapaSelectionModalLabel">
                            <i class="fas fa-list me-2"></i>Seleccionar Etapa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="etapaSearch" class="form-label">Buscar etapa:</label>
                            <input type="text" class="form-control" id="etapaSearch" placeholder="Escriba para buscar...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Etapas disponibles:</label>
                            <div class="list-group" id="etapaList" style="max-height: 300px; overflow-y: auto;">
                                <?php
                                try {
                                    $sql_etapas = "SELECT DISTINCT vc_etapas_nombre FROM vc_etapas ORDER BY vc_etapas_nombre ASC";
                                    $stmt_etapas = $conn->prepare($sql_etapas);
                                    $stmt_etapas->execute();
                                    $etapas = $stmt_etapas->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($etapas as $etapa_row) {
                                        echo '<button type="button" class="list-group-item list-group-item-action etapa-option" data-value="' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '">' . htmlspecialchars($etapa_row['vc_etapas_nombre']) . '</button>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error fetching etapas: " . $e->getMessage());
                                    echo '<div class="list-group-item text-danger">Error al cargar etapas</div>';
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
        $('#editSalModal').remove();
        $('#productSelectionModal').remove();
        $('#etapaSelectionModal').remove();
        
        // Add the modals to the page
        $('body').append(modalHtml);
        $('body').append(productSelectionModal);
        $('body').append(etapaSelectionModal);
        
        // Show the edit modal
        var editModal = new bootstrap.Modal(document.getElementById('editSalModal'));
        
        // Set up product selection functionality
        $('#selectProductoBtn').click(function() {
            var productSelectionModal = new bootstrap.Modal(document.getElementById('productSelectionModal'));
            productSelectionModal.show();
        });
        
        // Product search functionality
        $('#productSearch').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.product-option').each(function() {
                var productName = $(this).text().toLowerCase();
                if (productName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Product selection
        $(document).on('click', '.product-option', function() {
            var selectedProduct = $(this).data('value');
            $('#edit_producto').val(selectedProduct);
            $('#productSelectionModal').modal('hide');
        });
        
        // Set up etapa selection functionality
        $('#selectEtapaBtn').click(function() {
            var etapaSelectionModal = new bootstrap.Modal(document.getElementById('etapaSelectionModal'));
            etapaSelectionModal.show();
        });
        
        // Etapa search functionality
        $('#etapaSearch').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.etapa-option').each(function() {
                var etapaName = $(this).text().toLowerCase();
                if (etapaName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Etapa selection
        $(document).on('click', '.etapa-option', function() {
            var selectedEtapa = $(this).data('value');
            $('#edit_etapa').val(selectedEtapa);
            $('#etapaSelectionModal').modal('hide');
        });
        
        // Listen for when modal is fully shown
        $('#editSalModal').on('shown.bs.modal', function() {
            // Both fields are now text inputs, so no complex dropdown logic needed
            
            // Set values after modal is fully rendered
            // Both fields are now text inputs, so just set the values directly
            $('#edit_producto').val(producto);
            $('#edit_etapa').val(etapa);
            
            // Debug logging
            console.log('Modal shown - Setting values:');
            console.log('producto:', producto);
            console.log('etapa:', etapa);
            console.log('Current edit_producto value:', $('#edit_producto').val());
            console.log('Current edit_etapa value:', $('#edit_etapa').val());
        });
        
        editModal.show();
        
        // Set the selected values for dropdowns after modal is shown
        setTimeout(function() {
            $('#edit_producto').val(producto);
            $('#edit_etapa').val(etapa);
        }, 100);
        
        // Additional delay to ensure all options are loaded
        setTimeout(function() {
            $('#edit_producto').val(producto);
            $('#edit_etapa').val(etapa);
        }, 300);
        
        // Final attempt with longer delay to ensure complete rendering
        setTimeout(function() {
            $('#edit_producto').val(producto);
            $('#edit_etapa').val(etapa);
            
            // Debug logging to verify values are set
            console.log('Setting edit_producto to:', producto);
            console.log('Setting edit_etapa to:', etapa);
            console.log('Current edit_producto value:', $('#edit_producto').val());
            console.log('Current edit_etapa value:', $('#edit_etapa').val());
        }, 500);
        
        // Handle save button click
        $('#saveEditSal').click(function() {
            var formData = {
                id: $('#edit_id').val(),
                tagid: $('#edit_tagid').val(),
                racion: $('#edit_racion').val(),
                etapa: $('#edit_etapa').val(),
                producto: $('#edit_producto').val(),
                costo: $('#edit_costo').val(),
                fecha_inicio: $('#edit_fecha_inicio').val(),
                fecha_fin: $('#edit_fecha_fin').val()
            };
            
            // Show confirmation dialog
            Swal.fire({
                title: '¿Guardar cambios?',
                text: `¿Desea actualizar el registro de Sal para el animal con Tag ID ${formData.tagid}?`,
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
                        url: 'process_sal.php',
                        type: 'POST',
                        data: {
                            action: 'update',
                            id: formData.id,
                            tagid: formData.tagid,
                            racion: formData.racion,
                            etapa: formData.etapa,
                            producto: formData.producto,
                            costo: formData.costo,
                            fecha_inicio: formData.fecha_inicio,
                            fecha_fin: formData.fecha_fin
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
    
    // Handle delete button click (using event delegation for dynamically created elements)
    $(document).on('click', '.delete-sal', function() {
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
                    url: 'process_sal.php',
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