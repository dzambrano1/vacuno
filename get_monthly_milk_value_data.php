<?php
require_once './pdo_conexion.php';

try {
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the type parameter from the URL (for future extensibility)
    $type = $_GET['type'] ?? '';

    // Query to calculate monthly milk production revenue
    // Each vh_leche_peso is treated as daily average production for that month
    // Formula: daily_average × days_in_month × price_per_unit = monthly_revenue
    $query = "
        SELECT 
            DATE_FORMAT(vh_leche_fecha, '%Y-%m') AS month,
            ROUND(SUM(
                vh_leche_peso * 
                DAY(LAST_DAY(vh_leche_fecha)) * 
                vh_leche_precio
            ), 2) AS total_milk_value,
            UNIX_TIMESTAMP(MIN(vh_leche_fecha)) as month_timestamp
        FROM vh_leche 
        WHERE vh_leche_peso > 0 AND vh_leche_precio > 0 
        GROUP BY DATE_FORMAT(vh_leche_fecha, '%Y-%m')
        ORDER BY month ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($results as $row) {
        $data[] = [
            'month' => $row['month'],
            'total_milk_value' => (float)$row['total_milk_value'],
            'month_timestamp' => (int)$row['month_timestamp']
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    // Return error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Return general error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}