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
    
    // Query to get lombrices data ordered by date
    $query = "SELECT         
                vh_lombrices_fecha as fecha, 
                vh_lombrices_dosis as dosis,
                vh_lombrices_costo as costo,
                vh_lombrices_producto as vacuna,
                v.nombre as animal_nombre,
                vh_lombrices_tagid as tagid
              FROM vh_lombrices
              LEFT JOIN vacuno v ON vh_lombrices_tagid = v.tagid 
              ORDER BY vh_lombrices_fecha ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?> 