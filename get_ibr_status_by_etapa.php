<?php
header('Content-Type: application/json');
require_once "./pdo_conexion.php"; // Adjust path if necessary

$response = [
    'Inicio' => ['total' => 0, 'vaccinated' => 0, 'non_vaccinated_list' => []],
    'Crecimiento' => ['total' => 0, 'vaccinated' => 0, 'non_vaccinated_list' => []],
    'Finalizacion' => ['total' => 0, 'vaccinated' => 0, 'non_vaccinated_list' => []]
];

try {
    // Verify connection is PDO
    if (!($conn instanceof PDO)) {
        throw new Exception("Error: La conexión no es una instancia de PDO.");
    }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Query 1: Get counts --- 
    $sql_counts = "
        SELECT
            v.etapa,
            COUNT(DISTINCT v.tagid) AS total_animals,
            COUNT(DISTINCT a.vh_ibr_tagid) AS vaccinated_animals
        FROM vacuno v
        LEFT JOIN vh_ibr a ON v.tagid = a.vh_ibr_tagid
        WHERE v.etapa IN ('Inicio', 'Crecimiento', 'Finalizacion')
        GROUP BY v.etapa;
    ";

    $stmt_counts = $conn->prepare($sql_counts);
    $stmt_counts->execute();
    $results_counts = $stmt_counts->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results_counts as $row) {
        $etapa = $row['etapa'];
        if (isset($response[$etapa])) {
            $response[$etapa]['total'] = (int)$row['total_animals'];
            $response[$etapa]['vaccinated'] = (int)$row['vaccinated_animals'];
            // Calculate non-vaccinated count
            $response[$etapa]['non_vaccinated'] = $response[$etapa]['total'] - $response[$etapa]['vaccinated'];
             if ($response[$etapa]['non_vaccinated'] < 0) { // Sanity check
                 $response[$etapa]['non_vaccinated'] = 0;
             }
        }
    }
    
    // --- Query 2: Get list of non-vaccinated animals --- 
    $sql_list = "
        SELECT 
            v.etapa, 
            v.nombre, 
            v.tagid
        FROM vacuno v
        LEFT JOIN vh_ibr a ON v.tagid = a.vh_ibr_tagid
        WHERE v.etapa IN ('Inicio', 'Crecimiento', 'Finalizacion') 
          AND a.vh_ibr_tagid IS NULL
        ORDER BY v.etapa, v.nombre;
    ";
    
    $stmt_list = $conn->prepare($sql_list);
    $stmt_list->execute();
    $results_list = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results_list as $row) {
        $etapa = $row['etapa'];
        if (isset($response[$etapa])) {
             // Ensure nombre is not null, provide default if it is
             $nombre = $row['nombre'] ?? 'N/A'; // Use 'N/A' if nombre is null
             $response[$etapa]['non_vaccinated_list'][] = [
                 'nombre' => $nombre,
                 'tagid' => $row['tagid']
             ];
        }
    }

    // Keep 'total' for potential debugging or future use, js can ignore it

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error fetching IBR status by etapa: " . $e->getMessage());
    // Ensure error response is structured correctly
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
}
