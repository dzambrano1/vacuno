<?php
require_once './pdo_conexion.php';

try {
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the validity period for concentrado from configuration
    $configQuery = "SELECT v_vencimiento_concentrado FROM v_vencimiento LIMIT 1";
    $configStmt = $conn->prepare($configQuery);
    $configStmt->execute();
    $configRow = $configStmt->fetch(PDO::FETCH_ASSOC);
    $validityDays = $configRow ? intval($configRow['v_vencimiento_concentrado']) : 30;

    // Query to get individual concentrado records for proper daily distribution
    $query = "
        SELECT 
            c.vh_concentrado_tagid,
            v.nombre as animal_nombre,
            c.vh_concentrado_racion,
            c.vh_concentrado_costo,
            c.vh_concentrado_fecha
        FROM 
            vh_concentrado c
            LEFT JOIN vacuno v ON c.vh_concentrado_tagid = v.tagid 
        WHERE 
            c.vh_concentrado_fecha IS NOT NULL 
            AND c.vh_concentrado_racion > 0 
            AND c.vh_concentrado_costo > 0
        ORDER BY 
            c.vh_concentrado_fecha ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Array to store monthly aggregated data by animal
    $monthlyData = array();
    
    foreach ($data as $row) {
        // Calculate daily cost: racion × costo (daily expense)
        $dailyCost = (float)$row['vh_concentrado_racion'] * (float)$row['vh_concentrado_costo'];
        
        // Get date range: start date + validity period
        $startDate = new DateTime($row['vh_concentrado_fecha']);
        $endDate = clone $startDate;
        $endDate->add(new DateInterval('P' . ($validityDays - 1) . 'D')); // -1 because we include the start date
        
        // Distribute the daily cost across each day in the validity period
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $animalKey = $row['vh_concentrado_tagid'];
            
            // Initialize month and animal data if not exists
            if (!isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey] = array();
            }
            if (!isset($monthlyData[$monthKey][$animalKey])) {
                $monthlyData[$monthKey][$animalKey] = array(
                    'tagid' => $row['vh_concentrado_tagid'],
                    'animal_nombre' => $row['animal_nombre'],
                    'total_cost' => 0,
                    'days_count' => 0
                );
            }
            
            // Add daily cost to the month/animal total
            $monthlyData[$monthKey][$animalKey]['total_cost'] += $dailyCost;
            $monthlyData[$monthKey][$animalKey]['days_count'] += 1;
            
            // Move to next day
            $currentDate->add(new DateInterval('P1D'));
        }
    }

    // Convert to final format
    $formattedData = array();
    ksort($monthlyData); // Sort by month
    
    foreach ($monthlyData as $month => $animals) {
        foreach ($animals as $animalData) {
            $formattedData[] = array(
                'fecha' => $month . '-01', // Add day to make it a valid date
                'tagid' => $animalData['tagid'],
                'animal_nombre' => $animalData['animal_nombre'],
                'vh_concentrado_cantidad' => round($animalData['total_cost'], 2),
                'days_in_month' => $animalData['days_count']
            );
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($formattedData);

} catch (PDOException $e) {
    // Return error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 