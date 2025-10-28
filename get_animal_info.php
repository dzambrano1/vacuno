<?php
require_once './pdo_conexion.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Get tagid from GET parameter
    if (!isset($_GET['tagid']) || empty(trim($_GET['tagid']))) {
        throw new Exception('Tag ID requerido');
    }
    
    $tagid = trim($_GET['tagid']);
    
    // Connect to database
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos: ' . mysqli_connect_error());
    }
    
    // Set charset
    mysqli_set_charset($conn, "utf8");
    
    // Prepare SQL query to get animal by tagid
    $sql = "SELECT tagid, nombre, genero, raza, etapa, grupo, estatus, fecha_nacimiento 
            FROM vacuno 
            WHERE tagid = ? 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparando la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param('s', $tagid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("No se encontró animal con Tag ID: '{$tagid}'");
    }
    
    $animal = $result->fetch_assoc();
    
    // Close connections
    $stmt->close();
    $conn->close();
    
    // Return the animal data
    echo json_encode($animal);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(404);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Return fatal error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'debug' => $e->getMessage()
    ]);
}
?> 