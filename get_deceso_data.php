<?php
// Include database connection
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Verify connection is PDO
    if (!($conn instanceof PDO)) {
        throw new Exception("Error: La conexión no es una instancia de PDO");
    }
    
    // Enable PDO error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get deceso data ordered by date with individual animal details
    $query = "SELECT         
                DATE_FORMAT(deceso_fecha, '%Y-%m') as mes,
                COUNT(*) as total_muertes,
                YEAR(deceso_fecha) as año,
                MONTH(deceso_fecha) as mes_numero,
                GROUP_CONCAT(CONCAT(tagid, ' (', COALESCE(deceso_causa, 'Sin causa'), ')') ORDER BY tagid SEPARATOR ', ') as animales_detalle
              FROM vacuno 
              WHERE deceso_fecha IS NOT NULL 
                AND estatus = 'Muerto'
              GROUP BY DATE_FORMAT(deceso_fecha, '%Y-%m'), YEAR(deceso_fecha), MONTH(deceso_fecha)
              ORDER BY año ASC, mes_numero ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
