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
    
    // Query to get aborto data ordered by date
    $query = "SELECT         
                vh_aborto_fecha as fecha,
                vh_aborto_causa as causa,
                v.nombre as animal_nombre,
                vh_aborto_tagid as tagid
              FROM vh_aborto
              LEFT JOIN vacuno v ON vh_aborto_tagid = v.tagid 
              ORDER BY vh_aborto_fecha ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
