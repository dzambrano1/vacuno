<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once './pdo_conexion.php';

try {
    // Create connection using mysqli
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    // Set charset to UTF-8
    mysqli_set_charset($conn, "utf8");

    // Initialize monthly expenses array
    $monthlyExpenses = [];
    
    // Define expense categories
    $expenseCategories = [
        // Feed supplements (quantity × cost)
        'feed' => [
            'Concentrado' => ['table' => 'vh_concentrado', 'date_field' => 'vh_concentrado_fecha_inicio', 'qty_field' => 'vh_concentrado_racion', 'price_field' => 'vh_concentrado_costo'],
            'Melaza' => ['table' => 'vh_melaza', 'date_field' => 'vh_melaza_fecha_inicio', 'qty_field' => 'vh_melaza_racion', 'price_field' => 'vh_melaza_costo'],
            'Sal' => ['table' => 'vh_sal', 'date_field' => 'vh_sal_fecha_inicio', 'qty_field' => 'vh_sal_racion', 'price_field' => 'vh_sal_costo']
        ],
        // Health/vaccine expenses (doses × cost or single cost)
        'health' => [
            'Aftosa' => ['table' => 'vh_aftosa', 'date_field' => 'vh_aftosa_fecha', 'qty_field' => 'vh_aftosa_dosis', 'price_field' => 'vh_aftosa_costo'],
            'Brucelosis' => ['table' => 'vh_brucelosis', 'date_field' => 'vh_brucelosis_fecha', 'qty_field' => 'vh_brucelosis_dosis', 'price_field' => 'vh_brucelosis_costo'],
            'IBR' => ['table' => 'vh_ibr', 'date_field' => 'vh_ibr_fecha', 'qty_field' => 'vh_ibr_dosis', 'price_field' => 'vh_ibr_costo'],
            'CBR' => ['table' => 'vh_cbr', 'date_field' => 'vh_cbr_fecha', 'qty_field' => 'vh_cbr_dosis', 'price_field' => 'vh_cbr_costo'],
            'Carbunco' => ['table' => 'vh_carbunco', 'date_field' => 'vh_carbunco_fecha', 'qty_field' => 'vh_carbunco_dosis', 'price_field' => 'vh_carbunco_costo'],
            'Garrapatas' => ['table' => 'vh_garrapatas', 'date_field' => 'vh_garrapatas_fecha', 'qty_field' => 'vh_garrapatas_dosis', 'price_field' => 'vh_garrapatas_costo'],
            'Lombrices' => ['table' => 'vh_lombrices', 'date_field' => 'vh_lombrices_fecha', 'qty_field' => 'vh_lombrices_dosis', 'price_field' => 'vh_lombrices_costo']
        ]
    ];

    // Process each expense category
    foreach ($expenseCategories as $categoryType => $categories) {
        foreach ($categories as $categoryName => $config) {
            
            // Special handling for feed supplements with validity periods
            if ($categoryName === 'Concentrado' || $categoryName === 'Melaza' || $categoryName === 'Sal') {
                
                if ($categoryName === 'Concentrado') {
                    // Concentrado uses date ranges
                    $sql = "SELECT 
                                vh_concentrado_racion as racion,
                                vh_concentrado_costo as costo,
                                vh_concentrado_fecha_inicio as fecha_inicio,
                                vh_concentrado_fecha_fin as fecha_fin,
                                NULL as fecha
                            FROM vh_concentrado 
                            WHERE vh_concentrado_fecha_inicio IS NOT NULL 
                            AND vh_concentrado_fecha_fin IS NOT NULL
                            AND vh_concentrado_racion > 0 
                            AND vh_concentrado_costo > 0
                            ORDER BY vh_concentrado_fecha_inicio ASC";
                } else {
                    // Get validity period from configuration
                    $validityField = $categoryName === 'Melaza' ? 'v_vencimiento_melaza' : 'v_vencimiento_sal';
                    $configQuery = "SELECT {$validityField} FROM v_vencimiento LIMIT 1";
                    $configResult = mysqli_query($conn, $configQuery);
                    $validityDays = 30; // Default validity
                    if ($configResult && mysqli_num_rows($configResult) > 0) {
                        $configRow = mysqli_fetch_assoc($configResult);
                        $validityDays = intval($configRow[$validityField]);
                    }
                    
                    // Melaza and Sal use single date with validity period
                    $table = $config['table'];
                    $dateField = $config['date_field'];
                    $qtyField = $config['qty_field'];
                    $priceField = $config['price_field'];
                    
                    $sql = "SELECT 
                                {$qtyField} as racion,
                                {$priceField} as costo,
                                NULL as fecha_inicio,
                                NULL as fecha_fin,
                                {$dateField} as fecha
                            FROM {$table}
                            WHERE {$dateField} IS NOT NULL 
                            AND {$qtyField} > 0 
                            AND {$priceField} > 0
                            ORDER BY {$dateField} ASC";
                }
                
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Handle date ranges differently based on data type
                        if ($categoryName === 'Concentrado') {
                            // Concentrado uses explicit date ranges
                            $startDate = new DateTime($row['fecha_inicio']);
                            $endDate = new DateTime($row['fecha_fin']);
                        } else {
                            // Melaza and Sal use single date with validity period
                            $startDate = new DateTime($row['fecha']);
                            $endDate = clone $startDate;
                            $endDate->add(new DateInterval("P{$validityDays}D"));
                        }
                        
                        // Calculate total days in the period (inclusive)
                        $totalDays = $startDate->diff($endDate)->days + 1;
                        
                        // Skip if invalid date range
                        if ($totalDays <= 0) {
                            continue;
                        }
                        
                        // Calculate total expense using new formula: racion × costo × days
                        $totalExpense = (float)$row['racion'] * (float)$row['costo'] * $totalDays;
                        
                        // Calculate daily cost for distribution across months
                        $dailyCost = $totalExpense / $totalDays;
                        
                        // Distribute the daily cost across each day in the actual date range
                        $currentDate = clone $startDate;
                        while ($currentDate <= $endDate) {
                            $monthKey = $currentDate->format('Y-m');
                            
                            // Initialize month if not exists
                            if (!isset($monthlyExpenses[$monthKey])) {
                                $monthlyExpenses[$monthKey] = [
                                    'month' => $monthKey,
                                    'total_expenses' => 0,
                                    'feed_expenses' => 0,
                                    'health_expenses' => 0,
                                    'categories' => [],
                                    'details' => []
                                ];
                            }
                            
                            // Add daily cost to the month total
                            $monthlyExpenses[$monthKey]['total_expenses'] += $dailyCost;
                            $monthlyExpenses[$monthKey]['feed_expenses'] += $dailyCost;
                            
                            // Initialize or add to category
                            if (!isset($monthlyExpenses[$monthKey]['categories'][$categoryName])) {
                                $monthlyExpenses[$monthKey]['categories'][$categoryName] = 0;
                                $monthlyExpenses[$monthKey]['details'][$categoryName] = [
                                    'expense' => 0,
                                    'quantity' => 0,
                                    'avg_price' => 0,
                                    'records' => 0,
                                    'days' => 0
                                ];
                            }
                            
                            $monthlyExpenses[$monthKey]['categories'][$categoryName] += $dailyCost;
                            $monthlyExpenses[$monthKey]['details'][$categoryName]['expense'] += $dailyCost;
                            $monthlyExpenses[$monthKey]['details'][$categoryName]['quantity'] += (float)$row['racion'];
                            $monthlyExpenses[$monthKey]['details'][$categoryName]['days'] += 1;
                            
                            // Move to next day
                            $currentDate->add(new DateInterval('P1D'));
                        }
                        
                        // Update record count for the start month only
                        $recordStartMonth = $startDate->format('Y-m');
                        if (isset($monthlyExpenses[$recordStartMonth]['details'][$categoryName])) {
                            $monthlyExpenses[$recordStartMonth]['details'][$categoryName]['records'] += 1;
                        }
                    }
                }
            } else {
                // Standard processing for other categories
                $sql = "SELECT 
                            DATE_FORMAT({$config['date_field']}, '%Y-%m') as month,
                            SUM({$config['qty_field']} * {$config['price_field']}) as total_expense,
                            SUM({$config['qty_field']}) as total_quantity,
                            AVG({$config['price_field']}) as avg_price,
                            COUNT(*) as record_count
                        FROM {$config['table']} 
                        WHERE {$config['date_field']} IS NOT NULL 
                        AND {$config['qty_field']} > 0 
                        AND {$config['price_field']} > 0
                        GROUP BY DATE_FORMAT({$config['date_field']}, '%Y-%m')
                        ORDER BY DATE_FORMAT({$config['date_field']}, '%Y-%m') ASC";

                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $month = $row['month'];
                        $expense = (float)$row['total_expense'];
                        
                        // Initialize month if not exists
                        if (!isset($monthlyExpenses[$month])) {
                            $monthlyExpenses[$month] = [
                                'month' => $month,
                                'total_expenses' => 0,
                                'feed_expenses' => 0,
                                'health_expenses' => 0,
                                'categories' => [],
                                'details' => []
                            ];
                        }
                        
                        // Add to totals
                        $monthlyExpenses[$month]['total_expenses'] += $expense;
                        if ($categoryType === 'feed') {
                            $monthlyExpenses[$month]['feed_expenses'] += $expense;
                        } else {
                            $monthlyExpenses[$month]['health_expenses'] += $expense;
                        }
                        
                        // Store category details
                        $monthlyExpenses[$month]['categories'][$categoryName] = $expense;
                        $monthlyExpenses[$month]['details'][$categoryName] = [
                            'expense' => number_format($expense, 2, '.', ''),
                            'quantity' => (int)$row['total_quantity'],
                            'avg_price' => number_format((float)$row['avg_price'], 2, '.', ''),
                            'records' => (int)$row['record_count']
                        ];
                    }
                }
            }
        }
    }

    // Convert to final format and sort by month
    $data = [];
    ksort($monthlyExpenses);
    
    foreach ($monthlyExpenses as $monthData) {
        // Format feed supplement details properly (Concentrado, Melaza, Sal)
        foreach (['Concentrado', 'Melaza', 'Sal'] as $feedCategory) {
            if (isset($monthData['details'][$feedCategory])) {
                $feedDetails = $monthData['details'][$feedCategory];
                $avgPrice = $feedDetails['days'] > 0 && $feedDetails['quantity'] > 0 ? $feedDetails['expense'] / $feedDetails['quantity'] : 0;
                
                $monthData['details'][$feedCategory] = [
                    'expense' => number_format($feedDetails['expense'], 2, '.', ''),
                    'quantity' => number_format($feedDetails['quantity'], 2, '.', ''),
                    'avg_price' => number_format($avgPrice, 2, '.', ''),
                    'records' => $feedDetails['records'],
                    'days' => $feedDetails['days']
                ];
            }
        }
        
        $data[] = [
            'month' => $monthData['month'],
            'total_expenses' => number_format($monthData['total_expenses'], 2, '.', ''),
            'feed_expenses' => number_format($monthData['feed_expenses'], 2, '.', ''),
            'health_expenses' => number_format($monthData['health_expenses'], 2, '.', ''),
            'categories' => $monthData['categories'],
            'details' => $monthData['details']
        ];
    }

    // Close connection
    mysqli_close($conn);

    // Return JSON response
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

} catch (Exception $e) {
    // Log error for debugging
    error_log("Error in get_all_expenses_data.php: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode(array(
        'error' => 'Error al obtener datos de gastos totales',
        'message' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
