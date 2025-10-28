<?php
/**
 * generateAndUploadPDF Function
 * 
 * This function generates a PDF report for an animal and uploads it to ChatPDF
 * 
 * @param string $tagid - The animal's tag ID
 * @param string $reportType - Type of report ('medical', 'production', 'complete')
 * @param array $options - Additional options for PDF generation
 * @return array - Response array with success status and data
 */

require_once './pdo_conexion.php';
require('./fpdf/fpdf.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

/**
 * Custom PDF class extending FPDF
 */
class AnimalPDF extends FPDF
{
    private $animalData;
    private $reportType;
    
    public function setAnimalData($animalData, $reportType = 'complete')
    {
        $this->animalData = $animalData;
        $this->reportType = $reportType;
    }
    
    function Header()
    {
        // Logo or title
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 100, 0);
        $this->Cell(0, 8, 'SISTEMA DE GESTION GANADERA - GANAGRAM', 0, 1, 'C');
        
        // Subtitle
        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, 'Reporte de Animal: ' . $this->animalData['nombre'] . ' (' . $this->animalData['tagid'] . ')', 0, 1, 'C');
        
        // Date
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function addAnimalInfo()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, 'INFORMACION DEL ANIMAL', 0, 1, 'L');
        $this->Ln(2);
        
        // Animal details table
        $this->SetFont('Arial', '', 10);
        $data = [
            ['Tag ID:', $this->animalData['tagid']],
            ['Nombre:', $this->animalData['nombre']],
            ['Genero:', $this->animalData['genero']],
            ['Raza:', $this->animalData['raza']],
            ['Etapa:', $this->animalData['etapa']],
            ['Grupo:', $this->animalData['grupo']],
            ['Estatus:', $this->animalData['estatus']],
            ['Fecha de Nacimiento:', $this->animalData['fecha_nacimiento']]
        ];
        
        foreach ($data as $row) {
            $this->Cell(60, 6, $row[0], 0, 0, 'L');
            $this->Cell(0, 6, $row[1], 0, 1, 'L');
        }
        $this->Ln(5);
    }
    
    function addMedicalHistory($medicalData)
    {
        if (empty($medicalData)) {
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(128, 128, 128);
            $this->Cell(0, 6, 'No hay historial medico disponible', 0, 1, 'C');
            return;
        }
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, 'HISTORIAL MEDICO', 0, 1, 'L');
        $this->Ln(2);
        
        // Medical history table
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(240, 240, 240);
        
        // Table header
        $this->Cell(30, 6, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 6, 'Tratamiento', 1, 0, 'C', true);
        $this->Cell(30, 6, 'Veterinario', 1, 0, 'C', true);
        $this->Cell(0, 6, 'Observaciones', 1, 1, 'C', true);
        
        foreach ($medicalData as $record) {
            $this->Cell(30, 6, $record['fecha'] ?? '', 1, 0, 'C');
            $this->Cell(40, 6, $record['tratamiento'] ?? '', 1, 0, 'C');
            $this->Cell(30, 6, $record['veterinario'] ?? '', 1, 0, 'C');
            $this->Cell(0, 6, $record['observaciones'] ?? '', 1, 1, 'L');
        }
        $this->Ln(5);
    }
    
    function addProductionData($productionData)
    {
        if (empty($productionData)) {
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(128, 128, 128);
            $this->Cell(0, 6, 'No hay datos de produccion disponibles', 0, 1, 'C');
            return;
        }
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 8, 'DATOS DE PRODUCCION', 0, 1, 'L');
        $this->Ln(2);
        
        // Production data table
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(240, 240, 240);
        
        // Table header
        $this->Cell(30, 6, 'Fecha', 1, 0, 'C', true);
        $this->Cell(30, 6, 'Peso (kg)', 1, 0, 'C', true);
        $this->Cell(30, 6, 'Produccion', 1, 0, 'C', true);
        $this->Cell(0, 6, 'Observaciones', 1, 1, 'C', true);
        
        foreach ($productionData as $record) {
            $this->Cell(30, 6, $record['fecha'] ?? '', 1, 0, 'C');
            $this->Cell(30, 6, $record['peso'] ?? '', 1, 0, 'C');
            $this->Cell(30, 6, $record['produccion'] ?? '', 1, 0, 'C');
            $this->Cell(0, 6, $record['observaciones'] ?? '', 1, 1, 'L');
        }
        $this->Ln(5);
    }
}

/**
 * Main function to generate and upload PDF
 */
function generateAndUploadPDF($tagid, $reportType = 'complete', $options = [])
{
    try {
        // Validate inputs
        if (empty($tagid)) {
            throw new Exception('Tag ID is required');
        }
        
        // Connect to database
        $conn = mysqli_connect($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);
        
        if (!$conn) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }
        
        mysqli_set_charset($conn, "utf8");
        
        // Fetch animal data
        $animalQuery = "SELECT tagid, nombre, genero, raza, etapa, grupo, estatus, fecha_nacimiento 
                       FROM vacuno 
                       WHERE tagid = ? AND estatus = 'Activo'";
        
        $stmt = mysqli_prepare($conn, $animalQuery);
        if (!$stmt) {
            throw new Exception('Failed to prepare animal query: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, 's', $tagid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            throw new Exception("No active animal found with Tag ID: {$tagid}");
        }
        
        $animal = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Fetch additional data based on report type
        $medicalData = [];
        $productionData = [];
        
        if (in_array($reportType, ['medical', 'complete'])) {
            // Fetch medical history
            $medicalQuery = "SELECT fecha, tratamiento, veterinario, observaciones 
                           FROM historial_medico 
                           WHERE tagid = ? 
                           ORDER BY fecha DESC 
                           LIMIT 20";
            
            $stmt = mysqli_prepare($conn, $medicalQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $tagid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $medicalData = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            }
        }
        
        if (in_array($reportType, ['production', 'complete'])) {
            // Fetch production data
            $productionQuery = "SELECT fecha, peso, produccion, observaciones 
                              FROM datos_produccion 
                              WHERE tagid = ? 
                              ORDER BY fecha DESC 
                              LIMIT 20";
            
            $stmt = mysqli_prepare($conn, $productionQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $tagid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $productionData = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            }
        }
        
        // Close database connection
        mysqli_close($conn);
        
        // Create PDF
        $pdf = new AnimalPDF();
        $pdf->setAnimalData($animal, $reportType);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        // Add content based on report type
        $pdf->addAnimalInfo();
        
        if (in_array($reportType, ['medical', 'complete'])) {
            $pdf->addMedicalHistory($medicalData);
        }
        
        if (in_array($reportType, ['production', 'complete'])) {
            $pdf->addProductionData($productionData);
        }
        
        // Generate filename
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "animal_report_{$tagid}_{$reportType}_{$timestamp}.pdf";
        
        // Ensure reports directory exists
        $reportsDir = __DIR__ . '/reports';
        if (!file_exists($reportsDir)) {
            if (!mkdir($reportsDir, 0777, true)) {
                throw new Exception('Failed to create reports directory');
            }
        }
        
        // Debug: Log the directory path
        error_log("Reports directory: " . $reportsDir);
        error_log("Directory exists: " . (file_exists($reportsDir) ? 'Yes' : 'No'));
        error_log("Directory writable: " . (is_writable($reportsDir) ? 'Yes' : 'No'));
        
        $filepath = $reportsDir . '/' . $filename;
        
        // Debug: Log file path
        error_log("File path: " . $filepath);
        
        // Save PDF to file
        $pdf->Output('F', $filepath);
        
        // Debug: Log file creation result
        error_log("File exists after creation: " . (file_exists($filepath) ? 'Yes' : 'No'));
        if (file_exists($filepath)) {
            error_log("File size: " . filesize($filepath) . " bytes");
        }
        
        // Verify file was created
        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new Exception('Failed to generate PDF file at: ' . $filepath);
        }
        
        // Upload to ChatPDF
        $uploadResult = uploadToChatPDF($filepath, $filename);
        
        // Clean up local file if requested
        if (isset($options['cleanup']) && $options['cleanup']) {
            unlink($filepath);
        }
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'upload_result' => $uploadResult,
            'message' => 'PDF generated and uploaded successfully'
        ];
        
    } catch (Exception $e) {
        error_log('generateAndUploadPDF Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to generate and upload PDF'
        ];
    }
}

/**
 * Upload PDF to ChatPDF service
 */
function uploadToChatPDF($filepath, $filename)
{
    try {
        // Check if file exists and is readable
        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw new Exception('PDF file not found or not readable');
        }
        
        // Prepare file for upload
        $file = new CURLFile($filepath, 'application/pdf', $filename);
        
        // ChatPDF API configuration
        $apiKey = 'sec_AdQUXMlHjjhyrwud6dGCP9DFtUt8ZS7T';
        $apiUrl = 'https://api.chatpdf.com/v1/sources/add-file';
        
        // Prepare cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => $file
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL error: ' . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception('HTTP error: ' . $httpCode . ' - ' . $response);
        }
        
        $result = json_decode($response, true);
        
        if (!$result || isset($result['error'])) {
            throw new Exception('ChatPDF API error: ' . ($result['error'] ?? 'Unknown error'));
        }
        
        return [
            'success' => true,
            'sourceId' => $result['sourceId'] ?? null,
            'message' => 'Successfully uploaded to ChatPDF'
        ];
        
    } catch (Exception $e) {
        error_log('ChatPDF Upload Error: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to upload to ChatPDF'
        ];
    }
}

// Handle direct script execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['tagid'])) {
    $tagid = $_POST['tagid'] ?? $_GET['tagid'] ?? '';
    $reportType = $_POST['reportType'] ?? $_GET['reportType'] ?? 'complete';
    $options = [
        'cleanup' => isset($_POST['cleanup']) ? (bool)$_POST['cleanup'] : false
    ];
    
    $result = generateAndUploadPDF($tagid, $reportType, $options);
    echo json_encode($result);
    exit;
}

// If accessed directly without parameters, return usage info
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['tagid'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usage: POST or GET with tagid parameter',
        'example' => 'generateAndUploadPDF.php?tagid=12345&reportType=complete'
    ]);
    exit;
}
?>
