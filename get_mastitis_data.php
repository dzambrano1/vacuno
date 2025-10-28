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
    
    // Query to get mastitis data ordered by date - Only for "Vacas" group
    $query = "SELECT         
                vh_mastitis_fecha as fecha, 
                vh_mastitis_dosis as dosis,
                vh_mastitis_costo as costo,
                vh_mastitis_producto as vacuna,
                v.nombre as animal_nombre,
                vh_mastitis_tagid as tagid
              FROM vh_mastitis
              LEFT JOIN vacuno v ON vh_mastitis_tagid = v.tagid 
              WHERE v.grupo = 'Vacas'
              ORDER BY vh_mastitis_fecha ASC";
    
    // Fetch all records as associative array
    $result = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
?> 