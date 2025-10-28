<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once './pdo_conexion.php';

// Initialize the database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Check if we should update animals
if (isset($_GET['fix']) && $_GET['fix'] == 1) {
    // Fix query to set status to Feria for animals with price > 0
    $fix_query = "UPDATE vacuno SET estatus = 'Feria' WHERE precio > 0";
    if ($conn->query($fix_query)) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>
                <strong>Success!</strong> All animals with price > 0 have been updated to status 'Feria'.
              </div>";
    } else {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>
                <strong>Error!</strong> Failed to update animal status: " . $conn->error . "
              </div>";
    }
}

// Get all animals with price > 0
$query = "SELECT tagid, nombre, genero, raza, etapa, grupo, precio, fecha_publicacion, estatus 
          FROM vacuno 
          WHERE precio > 0
          ORDER BY precio DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Vacuno - Animals with Price</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .status-feria { background-color: #d4edda; }
        .status-other { background-color: #f8d7da; }
        .btn-fix { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Debug Vacuno Database</h1>
        <p>This page shows all animals with price > 0 in the database and their current status.</p>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <p>Found <strong><?php echo $result->num_rows; ?></strong> animals with price > 0.</p>
            <a href="debug_vacuno.php?fix=1" class="btn btn-primary btn-fix">Update All to 'Feria' Status</a>
            
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tag ID</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Raza</th>
                        <th>Etapa</th>
                        <th>Grupo</th>
                        <th>Precio</th>
                        <th>Fecha Publicación</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $rowClass = (strtoupper($row['estatus']) == strtoupper('Feria')) ? 'status-feria' : 'status-other';
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo htmlspecialchars($row['tagid']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['genero']); ?></td>
                        <td><?php echo htmlspecialchars($row['raza']); ?></td>
                        <td><?php echo htmlspecialchars($row['etapa']); ?></td>
                        <td><?php echo htmlspecialchars($row['grupo']); ?></td>
                        <td>$<?php echo number_format((float)$row['precio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_publicacion']); ?></td>
                        <td><?php echo htmlspecialchars($row['estatus']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">
                <strong>No animals found!</strong> There are no animals with price > 0 in the database.
            </div>
        <?php endif; ?>
        
        <h2 class="mt-4">Distinct Status Values</h2>
        <div class="card mb-4">
            <div class="card-body">
                <?php
                $status_query = "SELECT DISTINCT estatus FROM vacuno";
                $status_result = $conn->query($status_query);
                if ($status_result && $status_result->num_rows > 0) {
                    echo "<ul>";
                    while ($status_row = $status_result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($status_row['estatus']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No status values found.</p>";
                }
                ?>
            </div>
        </div>
        
        <a href="vacuno_feria.php" class="btn btn-secondary">Back to Feria</a>
    </div>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?> 