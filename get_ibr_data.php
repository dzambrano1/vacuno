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
    
    // Query to get ibr data ordered by date
    $query = "SELECT         
                vh_ibr_fecha as fecha, 
                vh_ibr_dosis as dosis,
                vh_ibr_costo as costo,
                vh_ibr_producto as vacuna,
                v.nombre as animal_nombre,
                vh_ibr_tagid as tagid
              FROM vh_ibr
              LEFT JOIN vacuno v ON vh_ibr_tagid = v.tagid 
              ORDER BY vh_ibr_fecha ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?> 