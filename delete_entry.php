<?php
require_once './pdo_conexion.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if id and table are set
    if (isset($_POST['id']) && isset($_POST['table'])) {
        $id = $_POST['id'];
        $tableName = $_POST['table'];

        // Basic validation: check if id is numeric and table name is one of the expected ones
        // Add more robust validation as needed (e.g., check against a list of allowed table names)
        $allowedTables = ['v_concentrado', 'v_sal', 'v_melaza', 'v_vacunas', 'v_razas', 'v_grupos', 'v_estatus'];
        if (!is_numeric($id)) {
             http_response_code(400); // Bad Request
             echo "Invalid ID.";
             exit;
        }

        if (!in_array($tableName, $allowedTables)) {
            http_response_code(400); // Bad Request
            echo "Invalid table name.";
            exit;
        }


        try {
            // Use prepared statement to prevent SQL injection
            // IMPORTANT: Directly injecting $tableName into the SQL is risky.
            // Since we validated it against a whitelist ($allowedTables), it's safer here,
            // but consider more advanced methods if the list of tables grows or changes dynamically.
            $sql = "DELETE FROM " . $tableName . " WHERE id = :id";
            $stmt = $conn->prepare($sql);

            // Bind the id parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                // Check if any rows were actually deleted
                if ($stmt->rowCount() > 0) {
                    http_response_code(200); // OK
                    echo "Entry deleted successfully.";
                } else {
                    http_response_code(404); // Not Found (or maybe 200 with a specific message)
                    echo "Entry not found or already deleted.";
                }
            } else {
                http_response_code(500); // Internal Server Error
                echo "Error deleting entry.";
                // Log the error: error_log("Error deleting from $tableName: " . implode(":", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo "Database error: " . $e->getMessage();
            // Log the error: error_log("PDOException in delete_entry.php: " . $e->getMessage());
        }

    } else {
        http_response_code(400); // Bad Request
        echo "Missing id or table parameter.";
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}

// No need to close PDO connection explicitly, it closes automatically
?> 