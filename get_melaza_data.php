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
    
    // Get the type parameter from the request
    $type = isset($_GET['type']) ? $_GET['type'] : 'basic';
    
    $result = [];
    
    switch ($type) {
        case 'monthly_expense':
            // Monthly total expenses
            $query = "SELECT 
                        DATE_FORMAT(vh_melaza_fecha, '%Y-%m') as month,
                        SUM(vh_melaza_racion * vh_melaza_costo) as total_expense
                      FROM vh_melaza 
                      WHERE vh_melaza_fecha IS NOT NULL
                      GROUP BY DATE_FORMAT(vh_melaza_fecha, '%Y-%m')
                      ORDER BY month ASC";
            break;
            
        case 'monthly_weight':
            // Monthly total weight (racion)
            $query = "SELECT 
                        DATE_FORMAT(vh_melaza_fecha, '%Y-%m') as month,
                        SUM(vh_melaza_racion) as total_weight
                      FROM vh_melaza 
                      WHERE vh_melaza_fecha IS NOT NULL
                      GROUP BY DATE_FORMAT(vh_melaza_fecha, '%Y-%m')
                      ORDER BY month ASC";
            break;
            
        case 'monthly_feed_weight':
            // Monthly feed weight (same as monthly_weight for melaza)
            $query = "SELECT 
                        DATE_FORMAT(vh_melaza_fecha, '%Y-%m') as month,
                        SUM(vh_melaza_racion) as total_feed_kg
                      FROM vh_melaza 
                      WHERE vh_melaza_fecha IS NOT NULL
                      GROUP BY DATE_FORMAT(vh_melaza_fecha, '%Y-%m')
                      ORDER BY month ASC";
            break;
            
        case 'animal_weight':
            // Monthly total animal weight from peso table
            $query = "SELECT 
                        DATE_FORMAT(vh_peso_fecha, '%Y-%m') as month,
                        SUM(vh_peso_animal) as total_weight
                      FROM vh_peso
                      WHERE vh_peso_fecha IS NOT NULL
                      GROUP BY DATE_FORMAT(vh_peso_fecha, '%Y-%m')
                      ORDER BY month ASC";
            break;
            
        default:
            // Basic melaza data (for line charts showing individual records)
            $query = "SELECT                 
                        vh_melaza_fecha as fecha, 
                        vh_melaza_racion as melaza,
                        vh_melaza_costo as costo,
                        vh_melaza_producto as producto,
                        v.nombre as animal_nombre,
                        vh_melaza_tagid as tagid
                      FROM vh_melaza
                      LEFT JOIN vacuno v ON vh_melaza_tagid = v.tagid 
                      ORDER BY vh_melaza_fecha ASC";
            break;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Fetch all records as associative array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}