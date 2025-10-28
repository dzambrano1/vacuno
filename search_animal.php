<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Get search query from GET parameter
    if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
        throw new Exception('Parámetro de búsqueda requerido');
    }
    
    $query = trim($_GET['query']);
    
    // Database connection is already established in pdo_conexion.php
    
    // Prepare SQL query to search by tagid or name
    $sql = "SELECT tagid, nombre, genero, raza, etapa, grupo, estatus, fecha_nacimiento 
            FROM vacuno 
            WHERE (tagid LIKE ? OR nombre LIKE ?)
            ORDER BY 
                CASE 
                    WHEN tagid = ? THEN 1
                    WHEN nombre = ? THEN 2
                    WHEN tagid LIKE ? THEN 3
                    WHEN nombre LIKE ? THEN 4
                    ELSE 5
                END
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters for exact match and partial match
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm, $query, $query, $searchTerm, $searchTerm]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($result)) {
        throw new Exception("No se encontró ningún animal activo con Tag ID o nombre: '{$query}'");
    }
    
    $animal = $result[0];
    
    // Return success response
    echo json_encode([
        'success' => true,
        'animal' => $animal,
        'message' => 'Animal encontrado exitosamente'
    ]);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
} catch (Error $e) {
    // Return fatal error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => true,
        'debug' => $e->getMessage()
    ]);
}