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
    
    // Query to get carbunco data ordered by date
    $query = "SELECT         
                vh_carbunco_fecha as fecha, 
                vh_carbunco_dosis as dosis,
                vh_carbunco_costo as costo,
                vh_carbunco_producto as vacuna,
                v.nombre as animal_nombre,
                vh_carbunco_tagid as tagid
              FROM vh_carbunco
              LEFT JOIN vacuno v ON vh_carbunco_tagid = v.tagid 
              ORDER BY vh_carbunco_fecha ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?> 