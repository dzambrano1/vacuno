<?php
// Prevent PHP errors from being output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Function to log errors
function logError($message, $error = null) {
    $errorLog = date('[Y-m-d H:i:s] ') . $message;
    if ($error instanceof Exception) {
        $errorLog .= "\nException: " . $error->getMessage();
        $errorLog .= "\nFile: " . $error->getFile() . ":" . $error->getLine();
        $errorLog .= "\nTrace: " . $error->getTraceAsString();
    } elseif ($error instanceof Error) {
        $errorLog .= "\nError: " . $error->getMessage();
        $errorLog .= "\nFile: " . $error->getFile() . ":" . $error->getLine();
        $errorLog .= "\nTrace: " . $error->getTraceAsString();
    }
    error_log($errorLog . "\n");
}

// Function to send JSON response
function sendJsonResponse($success, $message, $statusCode = 200) {
    global $debugInfo;
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    http_response_code($statusCode);
    $response = [
        'success' => $success,
        'message' => $message,
        'debug' => $debugInfo
    ];
    echo json_encode($response);
    exit;
}

// Debug information array
$debugInfo = [];

// Ensure clean output buffer
if (ob_get_level()) ob_end_clean();
ob_start();

try {
    $debugInfo['step'] = 'Iniciando generacion de reporte';
    
    // Check working directory and permissions
    $current_dir = getcwd();
    $debugInfo['working_dir'] = $current_dir;
    
    // Check if we can write to the current directory
    if (!is_writable($current_dir)) {
        logError('Current directory not writable: ' . $current_dir);
        $debugInfo['error'] = 'Current directory not writable';
        sendJsonResponse(false, 'Cannot write to current directory', 500);
    }
    
    // Check if required files exist and are readable
    if (!file_exists('./pdo_conexion.php')) {
        logError('Database configuration file not found at: ' . realpath('./pdo_conexion.php'));
        $debugInfo['error'] = 'Database configuration file not found';
        sendJsonResponse(false, 'Database configuration file not found', 500);
    }

    if (!is_readable('./pdo_conexion.php')) {
        logError('Database configuration file not readable');
        $debugInfo['error'] = 'Database configuration file not readable';
        sendJsonResponse(false, 'Database configuration file not readable', 500);
    }

    if (!file_exists('./fpdf/fpdf.php')) {
        logError('PDF library not found at: ' . realpath('./fpdf/fpdf.php'));
        $debugInfo['error'] = 'PDF library not found';
        sendJsonResponse(false, 'PDF library not found', 500);
    }

    if (!is_readable('./fpdf/fpdf.php')) {
        logError('PDF library not readable');
        $debugInfo['error'] = 'PDF library not readable';
        sendJsonResponse(false, 'PDF library not readable', 500);
    }
    
    $debugInfo['step'] = 'Archivos verificados';
    
    // Include required files
    require_once './pdo_conexion.php';
    require('./fpdf/fpdf.php');

    $debugInfo['step'] = 'Archivos incluidos';

    // Test database connection with more details
    if (!isset($pdo)) {
        logError('PDO not initialized after including pdo_conexion.php');
        $debugInfo['error'] = 'PDO not initialized';
        sendJsonResponse(false, 'Database connection not established', 500);
    }

    try {
        $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        $debugInfo['step'] = 'Conexión a base de datos verificada';
        $debugInfo['db_status'] = 'Connected';
    } catch (PDOException $e) {
        logError('Database connection test failed', $e);
        $debugInfo['error'] = 'Database connection test failed: ' . $e->getMessage();
        $debugInfo['db_info'] = [
            'host' => $servername ?? 'not set',
            'database' => $dbname ?? 'not set',
            'user' => $username ?? 'not set'
        ];
        sendJsonResponse(false, 'Database connection test failed', 500);
    }

    // Create custom PDF class
    class PDF extends FPDF
    {
        function Header()
        {
            // Add a title instead of logo
            $this->SetFont('Arial', 'B', 15);
            $this->SetTextColor(0, 100, 0);
            $this->Cell(0, 5, 'REPORTE INDICES PRODUCTIVOS', 0, 1, 'C');
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0, 5, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');
            $this->Ln(5);
        }
    }

    // Create PDF document
    $pdf = new PDF();
    $debugInfo['step'] = 'Objeto PDF creado';

    $pdf->AliasNbPages();
    $pdf->AddPage('L');
    $debugInfo['step'] = 'Página PDF creada';

    // Function to calculate age from birth date
    function calculateAge($birthDate) {
        try {
            $birth = new DateTime($birthDate);
            $today = new DateTime();
            $age = $today->diff($birth);
            return $age->y . " años, " . $age->m . " meses";
        } catch (Exception $e) {
            logError('Age calculation failed for date: ' . $birthDate, $e);
            return 'N/A';
        }
    }

    // Query data
    try {
        $sql_all = "SELECT tagid, nombre, genero, raza, etapa, grupo, estatus, fecha_nacimiento 
                    FROM vacuno 
                    ORDER BY fecha_nacimiento ASC";
        $stmt = $pdo->prepare($sql_all);
        
        if (!$stmt) {
            $debugInfo['error'] = 'Failed to prepare SQL statement';
            sendJsonResponse(false, 'Database query preparation failed', 500);
        }
        
        $stmt->execute();
        $debugInfo['step'] = 'Query ejecutado';
        
        $row_count = $stmt->rowCount();
        $debugInfo['rows'] = $row_count;
        
        // Generate PDF content
        $pdf->SetFont('Arial', '', 9);
        
        if ($row_count > 0) {
            // Header
            $pdf->SetFillColor(50, 120, 50);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            
            // Define column widths
            $w = array(25, 35, 25, 35, 25, 25, 25, 30, 30);
            
            // Header row
            $pdf->Cell($w[0], 7, 'Tag ID', 1, 0, 'C', true);
            $pdf->Cell($w[1], 7, 'Name', 1, 0, 'C', true);
            $pdf->Cell($w[2], 7, 'gender', 1, 0, 'C', true);
            $pdf->Cell($w[3], 7, 'breed', 1, 0, 'C', true);
            $pdf->Cell($w[4], 7, 'stage', 1, 0, 'C', true);
            $pdf->Cell($w[5], 7, 'group', 1, 0, 'C', true);
            $pdf->Cell($w[6], 7, 'status', 1, 0, 'C', true);
            $pdf->Cell($w[7], 7, 'birthdate', 1, 0, 'C', true);
            $pdf->Cell($w[8], 7, 'age', 1, 1, 'C', true);
            
            $debugInfo['step'] = 'Encabezados creados';
            
            // Data rows
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $fill = false;
            $rows_processed = 0;
            
            // Initialize counters and storage for details
            $total_animals = 0;
            $raza_counts = array();
            $raza_details = array();
            $etapa_counts = array(
                'Inicio' => 0,
                'Crecimiento' => 0,
                'Produccion' => 0
            );
            $etapa_details = array(
                'Inicio' => array(),
                'Crecimiento' => array(),
                'Produccion' => array()
            );
            $grupo_counts = array(
                'Toros' => 0,
                'Vacias' => 0,
                'Gestacion' => 0,
                'Novillos' => 0,
                'Novillas' => 0,
                'Destete' => 0,
                'Lactantes' => 0,
                'Mautas' => 0,
                'Mautes' => 0
            );
            $grupo_details = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $edad = calculateAge($row['fecha_nacimiento']);
                $pdf->Cell($w[0], 6, $row['tagid'], 1, 0, 'C', $fill);
                $pdf->Cell($w[1], 6, $row['nombre'], 1, 0, 'C', $fill);
                $pdf->Cell($w[2], 6, $row['genero'], 1, 0, 'C', $fill);
                $pdf->Cell($w[3], 6, $row['raza'], 1, 0, 'C', $fill);
                $pdf->Cell($w[4], 6, $row['etapa'], 1, 0, 'C', $fill);
                $pdf->Cell($w[5], 6, $row['grupo'], 1, 0, 'C', $fill);
                $pdf->Cell($w[6], 6, $row['estatus'], 1, 0, 'C', $fill);
                $pdf->Cell($w[7], 6, date('d/m/Y', strtotime($row['fecha_nacimiento'])), 1, 0, 'C', $fill);
                $pdf->Cell($w[8], 6, $edad, 1, 1, 'C', $fill);
                $fill = !$fill;
                $rows_processed++;
                
                // Update counters
                $total_animals++;
                
                // Count by raza
                if (!isset($raza_counts[$row['raza']])) {
                    $raza_counts[$row['raza']] = 0;
                    $raza_details[$row['raza']] = array();
                }
                $raza_counts[$row['raza']]++;
                $raza_details[$row['raza']][] = array(
                    'tagid' => $row['tagid'],
                    'nombre' => $row['nombre']
                );
                
                // Count by etapa
                if (isset($etapa_counts[$row['etapa']])) {
                    $etapa_counts[$row['etapa']]++;
                    $etapa_details[$row['etapa']][] = array(
                        'tagid' => $row['tagid'],
                        'nombre' => $row['nombre']
                    );
                }
                
                // Count by grupo and store details
                if (isset($grupo_counts[$row['grupo']])) {
                    $grupo_counts[$row['grupo']]++;
                    if (!isset($grupo_details[$row['grupo']])) {
                        $grupo_details[$row['grupo']] = array();
                    }
                    $grupo_details[$row['grupo']][] = array(
                        'tagid' => $row['tagid'],
                        'nombre' => $row['nombre']
                    );
                }
            }
            
            $debugInfo['rows_processed'] = $rows_processed;
            $debugInfo['step'] = 'Datos procesados';
            
            // Add summary section
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'Resumen del Inventario:', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            
            // Total animals
            $pdf->Cell(0, 6, "Total de animales: $total_animals", 0, 1, 'L');
            
            // Summary by raza
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, 'Distribucion por Raza:', 0, 1, 'L');
            
            foreach ($raza_counts as $raza => $count) {
                // Print breed header with count
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, "$raza: $count animales", 0, 1, 'L');
                
                // Print animal details
                $pdf->SetFont('Arial', '', 9);
                if (isset($raza_details[$raza])) {
                    foreach ($raza_details[$raza] as $animal) {
                        $pdf->Cell(15, 5, '', 0, 0); // Indent
                        $pdf->Cell(30, 5, $animal['tagid'], 0, 0, 'L');
                        $pdf->Cell(0, 5, $animal['nombre'], 0, 1, 'L');
                    }
                }
                $pdf->Ln(2); // Add some space between breeds
            }
            
            // Summary by etapa
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, 'Distribucion por Etapa:', 0, 1, 'L');
            
            foreach ($etapa_counts as $etapa => $count) {
                if ($count > 0) {
                    // Print stage header with count
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(0, 6, "$etapa: $count animales", 0, 1, 'L');
                    
                    // Print animal details
                    $pdf->SetFont('Arial', '', 9);
                    if (isset($etapa_details[$etapa])) {
                        foreach ($etapa_details[$etapa] as $animal) {
                            $pdf->Cell(15, 5, '', 0, 0); // Indent
                            $pdf->Cell(30, 5, $animal['tagid'], 0, 0, 'L');
                            $pdf->Cell(0, 5, $animal['nombre'], 0, 1, 'L');
                        }
                    }
                    $pdf->Ln(2); // Add some space between stages
                }
            }
            
            // Summary by grupo with details
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 6, 'Distribucion por Grupo:', 0, 1, 'L');
            
            foreach ($grupo_counts as $grupo => $count) {
                if ($count > 0) {
                    // Print group header with count
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(0, 6, "$grupo: $count animales", 0, 1, 'L');
                    
                    // Print animal details
                    $pdf->SetFont('Arial', '', 9);
                    if (isset($grupo_details[$grupo])) {
                        foreach ($grupo_details[$grupo] as $animal) {
                            $pdf->Cell(15, 5, '', 0, 0); // Indent
                            $pdf->Cell(30, 5, $animal['tagid'], 0, 0, 'L');
                            $pdf->Cell(0, 5, $animal['nombre'], 0, 1, 'L');
                        }
                    }
                    $pdf->Ln(2); // Add some space between groups
                }
            }
            
            // Monthly Weight Analysis Section
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'Analisis Mensual de Peso de la Carne y Valor de Mercado:', 0, 1, 'L');
            
            // Get monthly weight data
            $monthlyQuery = "
                SELECT 
                    DATE_FORMAT(vh_peso_fecha, '%Y-%m') as month,
                    SUM(vh_peso_animal) as total_weight,
                    AVG(vh_peso_precio) as avg_price,
                    COUNT(DISTINCT vh_peso_tagid) as animal_count
                FROM vh_peso
                GROUP BY month
                ORDER BY month ASC
                LIMIT 12";
            
            try {
                $stmt = $pdo->prepare($monthlyQuery);
                $stmt->execute();
                $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($monthlyData)) {
                    // Calculate linear regression for weight increase
                    $n = count($monthlyData);
                    $sumX = 0;
                    $sumY = 0;
                    $sumXY = 0;
                    $sumX2 = 0;
                    
                    // First pass: calculate means and prepare for regression
                    foreach ($monthlyData as $i => $row) {
                        $x = $i; // Use month index as x coordinate
                        $y = $row['total_weight'];
                        
                        $sumX += $x;
                        $sumY += $y;
                        $sumXY += ($x * $y);
                        $sumX2 += ($x * $x);
                    }
                    
                    // Calculate regression coefficients
                    $slope = (($n * $sumXY) - ($sumX * $sumY)) / (($n * $sumX2) - ($sumX * $sumX));
                    // This is our monthly weight increase
                    $monthlyIncrease = $slope;
                    
                    // Headers for the monthly analysis table
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(40, 6, 'Mes', 1, 0, 'C');
                    $pdf->Cell(45, 6, 'Peso Total Carne', 1, 0, 'C');
                    $pdf->Cell(45, 6, 'Precio Promedio Carne en Pie', 1, 0, 'C');
                    $pdf->Cell(45, 6, 'Ingreso Carne Estimado', 1, 1, 'C');
                    
                    $pdf->SetFont('Arial', '', 9);
                    $totalWeight = 0;
                    $totalIncome = 0;
                    $totalPriceSum = 0;
                    $monthCount = 0;
                    
                    foreach ($monthlyData as $row) {
                        $month = date('F Y', strtotime($row['month'] . '-01'));
                        $total_weight = number_format($row['total_weight'], 2);
                        $avg_price = number_format($row['avg_price'], 2);
                        $total_income = number_format($row['total_weight'] * $row['avg_price'], 2);
                        
                        $totalWeight += $row['total_weight'];
                        $totalIncome += ($row['total_weight'] * $row['avg_price']);
                        $totalPriceSum += $row['avg_price'];
                        $monthCount++;
                        
                        $pdf->Cell(40, 6, $month, 1, 0, 'L');
                        $pdf->Cell(45, 6, $total_weight . ' kg', 1, 0, 'R');
                        $pdf->Cell(45, 6, '$' . $avg_price . '/kg', 1, 0, 'R');
                        $pdf->Cell(45, 6, '$' . $total_income, 1, 1, 'R');
                    }
                    
                    // Add totals row with all columns
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(40, 6, 'TOTALES', 1, 0, 'L', true);
                    $pdf->Cell(45, 6, number_format($totalWeight, 2) . ' kg', 1, 0, 'R', true);
                    $pdf->Cell(45, 6, '$' . number_format($totalPriceSum / $monthCount, 2) . '/kg', 1, 0, 'R', true);
                    $pdf->Cell(45, 6, '$' . number_format($totalIncome, 2), 1, 1, 'R', true);
                    
                    // Add summary of analysis
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(0, 6, 'Resumen del Analisis:', 0, 1, 'L');
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(0, 6, 'Incremento Mensual Carne Promedio: ' . number_format($monthlyIncrease, 2) . ' kg/mes', 0, 1, 'L');
                    $pdf->Cell(0, 6, 'Peso Total de Carne: ' . number_format($totalWeight, 2) . ' kg', 0, 1, 'L');
                    
                    // Add explanation note
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->MultiCell(0, 4, 
                        'Nota: El ingreso estimado por carne se calcula multiplicando el peso total de la carne del mes por el precio promedio del kilo de carne en pie del mes. ' .
                        'El incremento mensual del peso de la carne se calcula mediante regresion lineal del peso de la carne total mensual.', 
                        0, 'L');
                    
                    // Monthly Milk Analysis Section
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 11);
                    $pdf->Cell(0, 8, 'Analisis Mensual de Leche y Valor de Mercado:', 0, 1, 'L');
                    
                    // Get monthly milk data with period calculations
                    $monthlyMilkQuery = "
                        WITH RankedSamples AS (
                            SELECT 
                                vh_leche_tagid,
                                vh_leche_fecha,
                                vh_leche_peso,
                                vh_leche_precio,
                                LEAD(vh_leche_fecha) OVER (PARTITION BY vh_leche_tagid ORDER BY vh_leche_fecha) as next_fecha
                            FROM vh_leche
                        ),
                        ProductionCalculations AS (
                            SELECT 
                                vh_leche_tagid,
                                vh_leche_fecha,
                                vh_leche_peso,
                                vh_leche_precio,
                                COALESCE(
                                    DATEDIFF(next_fecha, vh_leche_fecha),
                                    DATEDIFF(CURRENT_DATE, vh_leche_fecha)
                                ) as days_until_next,
                                vh_leche_peso * 
                                COALESCE(
                                    DATEDIFF(next_fecha, vh_leche_fecha),
                                    DATEDIFF(CURRENT_DATE, vh_leche_fecha)
                                ) as period_production
                            FROM RankedSamples
                        )
                        SELECT 
                            DATE_FORMAT(vh_leche_fecha, '%Y-%m') as month,
                            SUM(period_production) as total_production,
                            AVG(vh_leche_precio) as avg_price,
                            COUNT(DISTINCT vh_leche_tagid) as animal_count
                        FROM ProductionCalculations
                        GROUP BY month
                        ORDER BY month ASC
                        LIMIT 12";
                    
                    try {
                        $stmt = $pdo->prepare($monthlyMilkQuery);
                        $stmt->execute();
                        $monthlyMilkData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($monthlyMilkData)) {
                            // Headers for the monthly milk analysis table
                            $pdf->SetFont('Arial', 'B', 9);
                            $pdf->Cell(40, 6, 'Mes', 1, 0, 'C');
                            $pdf->Cell(45, 6, 'Produccion Leche Total', 1, 0, 'C');
                            $pdf->Cell(45, 6, 'Precio Leche Promedio', 1, 0, 'C');
                            $pdf->Cell(45, 6, 'Ingreso Total Leche Estimado', 1, 1, 'C');
                            
                            $pdf->SetFont('Arial', '', 9);
                            $totalProduction = 0;
                            $totalIncome = 0;
                            $totalPriceSum = 0;
                            $monthCount = 0;
                            
                            foreach ($monthlyMilkData as $row) {
                                $month = date('F Y', strtotime($row['month'] . '-01'));
                                $total_production = $row['total_production'];
                                $avg_price = $row['avg_price'];
                                $total_income = $total_production * $avg_price;
                                
                                $totalProduction += $total_production;
                                $totalIncome += $total_income;
                                $totalPriceSum += $avg_price;
                                $monthCount++;
                                
                                $pdf->Cell(40, 6, $month, 1, 0, 'L');
                                $pdf->Cell(45, 6, number_format($total_production, 2) . ' kg', 1, 0, 'R');
                                $pdf->Cell(45, 6, '$' . number_format($avg_price, 2) . '/kg', 1, 0, 'R');
                                $pdf->Cell(45, 6, '$' . number_format($total_income, 2), 1, 1, 'R');
                            }
                            
                            // Add totals row with all columns
                            $pdf->SetFillColor(240, 240, 240);
                            $pdf->SetFont('Arial', 'B', 9);
                            $pdf->Cell(40, 6, 'TOTALES', 1, 0, 'L', true);
                            $pdf->Cell(45, 6, number_format($totalProduction, 2) . ' kg', 1, 0, 'R', true);
                            $pdf->Cell(45, 6, '$' . number_format($totalPriceSum / $monthCount, 2) . '/kg', 1, 0, 'R', true);
                            $pdf->Cell(45, 6, '$' . number_format($totalIncome, 2), 1, 1, 'R', true);
                            
                            // Add summary statistics
                            $pdf->Ln(5);
                            $pdf->SetFont('Arial', 'B', 10);
                            $pdf->Cell(0, 6, 'Resumen de Produccion:', 0, 1, 'L');
                            $pdf->SetFont('Arial', '', 9);
                            
                            $avgMonthlyProduction = $totalProduction / $monthCount;
                            $avgMonthlyIncome = $totalIncome / $monthCount;
                            
                            $pdf->Cell(0, 6, 'Promedio Mensual de Produccion de leche: ' . number_format($avgMonthlyProduction, 2) . ' kg/mes', 0, 1, 'L');
                            $pdf->Cell(0, 6, 'Promedio Mensual de Ingresos leche: $' . number_format($avgMonthlyIncome, 2) . '/mes', 0, 1, 'L');
                            $pdf->Cell(0, 6, 'Produccion Total de Leche: ' . number_format($totalProduction, 2) . ' kg', 0, 1, 'L');
                            
                            // Add explanation note
                            $pdf->Ln(5);
                            $pdf->SetFont('Arial', 'I', 8);
                            $pdf->MultiCell(0, 4, 
                                'Nota: La Produccion de leche se calcula multiplicando la produccion diaria por el precio y por el numero de dias ' .
                                'hasta el siguiente registro del mismo animal. Para el ultimo registro de cada animal, se usa la fecha actual como referencia. ' .
                                'Los totales y promedios se calculan sobre los ultimos ' . $monthCount . ' meses.', 
                                0, 'L');
                        }
                    } catch (Exception $e) {
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(0, 6, 'Error al generar Analisis de Produccion de leche: ' . $e->getMessage(), 0, 1, 'L');
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    
                } else {
                    $pdf->Cell(0, 10, 'No se encontraron registros', 0, 1, 'C');
                    $debugInfo['step'] = 'No se encontraron registros';
                }
                
            } catch (PDOException $e) {
                $debugInfo['error'] = 'Database error: ' . $e->getMessage();
                sendJsonResponse(false, 'Error retrieving data from database', 500);
            }
            
        } else {
            $pdf->Cell(0, 10, 'No se encontraron registros', 0, 1, 'C');
            $debugInfo['step'] = 'No se encontraron registros';
        }
        
    } catch (PDOException $e) {
        $debugInfo['error'] = 'Database error: ' . $e->getMessage();
        sendJsonResponse(false, 'Error retrieving data from database', 500);
    }

    // Save PDF
    try {
        $pdf_filename = 'finca_report.pdf';
        $dir = dirname($pdf_filename);
        
        if (!is_writable($dir)) {
            $debugInfo['error'] = 'Directory not writable: ' . $dir;
            sendJsonResponse(false, 'Cannot write to directory', 500);
        }
        
        $pdf->Output('F', $pdf_filename);
        
        if (!file_exists($pdf_filename)) {
            $debugInfo['error'] = 'PDF file not created';
            sendJsonResponse(false, 'Failed to create PDF file', 500);
        }
        
        $debugInfo['step'] = 'PDF guardado exitosamente';
        $debugInfo['filename'] = $pdf_filename;
        
        sendJsonResponse(true, 'PDF generado exitosamente');
        
    } catch (Exception $e) {
        $debugInfo['error'] = 'PDF generation error: ' . $e->getMessage();
        sendJsonResponse(false, 'Error generating PDF', 500);
    }

} catch (Exception $e) {
    $debugInfo['error'] = 'System error: ' . $e->getMessage();
    sendJsonResponse(false, 'System error occurred', 500);
}

exit;