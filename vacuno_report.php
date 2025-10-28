<?php
// Temporarily enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './pdo_conexion.php';
require('./fpdf/fpdf.php'); // You might need to install FPDF library

// Check if reports directory exists, if not create it
$reportsDir = './reports';
if (!file_exists($reportsDir)) {
    mkdir($reportsDir, 0777, true);
}

// Ensure no output has been sent before
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Check if animal ID is provided
if (!isset($_GET['tagid']) || empty($_GET['tagid'])) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error: No animal ID provided']);
        exit;
    } else {
        die('Error: No animal ID provided');
    }
}

$tagid = $_GET['tagid'];

// PDO connection is already established in pdo_conexion.php

/**
 * Upload PDF to ChatPDF service
 */
function uploadToChatPDF($filepath, $filename) {
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

// PDO already handles UTF-8 encoding

// Log successful connection
error_log("Database connection established successfully");

// Fetch animal basic info
$sql_animal = "SELECT * FROM vacuno WHERE tagid = ?";
$stmt_animal = $conn->prepare($sql_animal);
$stmt_animal->execute([$tagid]);
$result_animal = $stmt_animal->fetch(PDO::FETCH_ASSOC);

if (!$result_animal) {
    error_log('Animal not found with tagid: ' . $tagid);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error: Animal not found']);
        exit;
    } else {
        die('Error: Animal not found');
    }
}

$animal = $result_animal;
error_log("Animal data retrieved successfully: " . $animal['nombre'] . " (" . $animal['tagid'] . ")");

// Create PDF
class PDF extends FPDF
{
    // Animal data to access in header
    protected $animalData;
    
    // Set animal data
    function setAnimalData($data) {
        $this->animalData = $data;
    }
    
    // Helper function to ensure proper UTF-8 encoding for searchable text
    function EncodeText($text) {
        // Handle null or empty values
        if ($text === null || $text === '') {
            return '';
        }
        
        // Convert to string if needed
        $text = (string)$text;
        
        // Remove control characters and normalize text
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Convert text to proper encoding for FPDF
        if (mb_detect_encoding($text, 'UTF-8', true)) {
            // Text is UTF-8, convert to ISO-8859-1 for FPDF compatibility
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        }
        return $text;
    }
    
    // Override Cell method to ensure proper text encoding
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        // Ensure text is properly formatted for searchability
        $txt = trim($txt); // Remove extra whitespace
        $txt = preg_replace('/\s+/', ' ', $txt); // Normalize whitespace
        parent::Cell($w, $h, $this->EncodeText($txt), $border, $ln, $align, $fill, $link);
    }
    
    // Add method to set optimal font for searchability
    function SetSearchableFont($family='Arial', $style='', $size=10) {
        $this->SetFont($family, $style, $size);
        // Ensure text rendering mode is optimal for searchability
        $this->_out('2 Tr'); // Set text rendering mode to fill (most searchable)
    }
    
    // Page header
    function Header()
    {
        // Only show header on first page
        if ($this->PageNo() == 1) {
            // Set margins and padding
            $this->SetMargins(10, 10, 10);
            
            // Draw a subtle header background
            $this->SetFillColor(240, 240, 240);
            $this->Rect(0, 0, 210, 35, 'F');
            
            // Logo with adjusted position
            $this->Image('./images/default_image.png', 10, 6, 30);
            
            // Add current date on upper right
            $this->SetSearchableFont('Arial', '', 10);
            $this->SetTextColor(80, 80, 80); // Gray color for date
            $current_date = date('d/m/Y H:i:s');
            $this->SetXY(150, 8); // Position on upper right
            $this->Cell(50, 8, 'Fecha: ' . $current_date, 0, 0, 'R');
            
            // Add a decorative line
            $this->SetDrawColor(0, 128, 0); // Green line
            $this->Line(10, 35, 200, 35);
            
            // Main report title
            $this->SetFont('Arial', 'B', 18);
            $this->SetTextColor(0, 80, 0); // Darker green for main title
            
            $this->Ln(5);
            
            // Title section with animal name - larger, bold font
            $this->SetSearchableFont('Arial', 'B', 16);
            $this->SetTextColor(0, 100, 0); // Dark green color for title
            // Center alignment for animal name
            $this->Cell(0, 10, mb_strtoupper($this->animalData['nombre']), 0, 1, 'C');
            
            // Tag ID in a slightly smaller font, still professional
            $this->SetSearchableFont('Arial', 'B', 12);
            $this->SetTextColor(80, 80, 80); // Gray color for tag ID
            // Center alignment for Tag ID
            $this->Cell(0, 10, 'Tag ID: ' . $this->animalData['tagid'], 0, 1, 'C');
            $this->Ln(5);
            
            // Add animal images
            if (!empty($this->animalData)) {
                // Photo section title
                $this->SetFont('Arial', 'B', 12);
                $this->SetTextColor(0, 0, 0);
                $this->Cell(0, 5, 'CONDICION CORPORAL', 0, 1, 'C');
                $this->Ln(1);
                
                // Start position for images
                $y = 70; // Adjusted for the new title
                $imageWidth = 60;
                $spacing = 5;
                
                // Left position for first image
                $x1 = 10;
                // Left position for second image
                $x2 = $x1 + $imageWidth + $spacing;
                // Left position for third image
                $x3 = $x2 + $imageWidth + $spacing;
                
                // Add first image if exists
                if (!empty($this->animalData['image'])) {
                    $imagePath = $this->animalData['image'];
                    $imagePath = str_replace('\\', '/', $imagePath); // Normalize path
                    
                    // Paths to try
                    $pathsToTry = [
                        $imagePath,
                        './' . ltrim($imagePath, './'),
                        '../' . $imagePath,
                        $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($imagePath, '/')
                    ];
                    
                    foreach ($pathsToTry as $path) {
                        if (file_exists($path)) {
                            $this->Image($path, $x1, $y, $imageWidth);
                            break;
                        }
                    }
                }
                
                // Add second image if exists
                if (!empty($this->animalData['image2'])) {
                    $imagePath = $this->animalData['image2'];
                    $imagePath = str_replace('\\', '/', $imagePath); // Normalize path
                    
                    // Paths to try
                    $pathsToTry = [
                        $imagePath,
                        './' . ltrim($imagePath, './'),
                        '../' . $imagePath,
                        $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($imagePath, '/')
                    ];
                    
                    foreach ($pathsToTry as $path) {
                        if (file_exists($path)) {
                            $this->Image($path, $x2, $y, $imageWidth);
                            break;
                        }
                    }
                }
                
                // Add third image if exists
                if (!empty($this->animalData['image3'])) {
                    $imagePath = $this->animalData['image3'];
                    $imagePath = str_replace('\\', '/', $imagePath); // Normalize path
                    
                    // Paths to try
                    $pathsToTry = [
                        $imagePath,
                        './' . ltrim($imagePath, './'),
                        '../' . $imagePath,
                        $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($imagePath, '/')
                    ];
                    
                    foreach ($pathsToTry as $path) {
                        if (file_exists($path)) {
                            $this->Image($path, $x3, $y, $imageWidth);
                            break;
                        }
                    }
                }
                
                // Add image captions
                $this->SetFont('Arial', 'I', 8);
                $this->SetY($y + $imageWidth + 2);
                $this->SetX($x1);
                $this->Cell($imageWidth, 10, 'Foto Principal', 0, 0, 'C');
                $this->SetX($x2);
                $this->Cell($imageWidth, 10, 'Foto Secundaria', 0, 0, 'C');
                $this->SetX($x3);
                $this->Cell($imageWidth, 10, 'Foto Adicional', 0, 0, 'C');
                
                // Add extra space after images
                $this->Ln(10);
            }
        }
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetSearchableFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Draw a circle
    function Circle($x, $y, $r, $style='D')
    {
        $this->Ellipse($x, $y, $r, $r, $style);
    }
    
    // Draw an ellipse
    function Ellipse($x, $y, $rx, $ry, $style='D')
    {
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
            
        $lx=4/3*(M_SQRT2-1)*$rx;
        $ly=4/3*(M_SQRT2-1)*$ry;
        $k=$this->k;
        $h=$this->h;
        
        $this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x)*$k, ($h-$y)*$k,
            ($x+$lx)*$k, ($h-$y)*$k,
            ($x+$rx)*$k, ($h-$y+$ly)*$k,
            ($x+$rx)*$k, ($h-$y+$ry)*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x+$rx)*$k, ($h-$y+$ry+$ly)*$k,
            ($x+$lx)*$k, ($h-$y+$ry+$ry)*$k,
            ($x)*$k, ($h-$y+$ry+$ry)*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x-$lx)*$k, ($h-$y+$ry+$ry)*$k,
            ($x-$rx)*$k, ($h-$y+$ry+$ly)*$k,
            ($x-$rx)*$k, ($h-$y+$ry)*$k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
            ($x-$rx)*$k, ($h-$y+$ly)*$k,
            ($x-$lx)*$k, ($h-$y)*$k,
            ($x)*$k, ($h-$y)*$k,
            $op));
    }

    // Function to styled chapter titles
    function ChapterTitle($title)
    {
        // Add animal tagid and nombre to the title (except for farm-wide statistics)
        $animalInfo = '';
        if ($this->animalData && isset($this->animalData['tagid']) && isset($this->animalData['nombre'])) {
            // Don't add animal info for farm-wide statistics (any title containing "(Finca)" or distribution reports)
            if (strpos($title, '(Finca)') === false && $title !== 'Distribucion por Raza' && $title !== 'Distribucion de Animales por Grupo' && $title !== 'Indice de Conversion Alimenticia (ICA)' && $title !== 'Resumen de Vacunaciones y Tratamientos' && $title !== 'Duracion de Gestaciones' && $title !== 'Hembras Sin Registro de Gestacion' && $title !== 'Animales con mas de 365 Dias Desde Ultimo Parto' && $title !== 'ESTADISTICAS DE LA FINCA') {
                $animalInfo = ' ' . $this->animalData['tagid'] . ' (' . $this->animalData['nombre'] . ')';
            }
        }
        $fullTitle = $title . $animalInfo;
        
        $this->SetSearchableFont('Arial', 'B', 12);
        $this->SetFillColor(0, 100, 0); // Darker green
        $this->SetTextColor(255, 255, 255); // White text
        
        // Check if this is a main section title (all caps)
        if ($title == 'PRODUCCION' || $title == 'ALIMENTACION' || $title == 'SALUD' || 
            $title == 'REPRODUCCION' || $title == 'ESTADISTICAS DE LA FINCA') {
            // Main section titles - centered, larger font, more space before/after
            $this->SetSearchableFont('Arial', 'B', 14);
            $this->Ln(5); // Extra space before main sections
            $this->Cell(0, 10, $fullTitle, 0, 1, 'C', true);
            $this->Ln(5); // Extra space after main sections
        } else {
            // Regular subsection titles - left aligned
            $this->Cell(0, 8, $fullTitle, 0, 1, 'L', true);
            $this->Ln(3);
        }
        
        $this->SetTextColor(0, 0, 0); // Reset to black text
    }

    // Data table
    function DataTable($header, $data)
    {
        // Column widths
        $w = array(40, 50, 40, 50);
        
        // Header
        $this->SetSearchableFont('Arial', 'B', 10);
        $this->SetFillColor(50, 120, 50); // Darker green for header
        $this->SetTextColor(255, 255, 255); // White text for better contrast
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0); // Reset to black text for data
        
        // Data
        $this->SetSearchableFont('Arial', '', 9); // Match SimpleTable font size
        $this->SetFillColor(245, 250, 245); // Match SimpleTable fill color
        $fill = false;
        foreach ($data as $row) {
            for ($i = 0; $i < count($row); $i++) {
                $this->Cell($w[$i], 6, $row[$i], 1, 0, 'C', $fill); // Center align all cells
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->Ln(5);
    }
    
    // Simple table for two columns
    function SimpleTable($header, $data)
    {
        // Determine column count and adjust widths accordingly
        $columnCount = count($header);
        
        // Default column widths
        if ($columnCount == 2) {
            $w = array(60, 120); // Original 2-column layout
        } elseif ($columnCount == 3) {
            $w = array(50, 50, 80); // 3-column layout (date, value, price)
        } elseif ($columnCount == 4) {
            $w = array(40, 60, 40, 40); // 4-column layout
        } else {
            // Create automatic column widths
            $pageWidth = $this->GetPageWidth() - 20; // Adjust for margins
            $w = array_fill(0, $columnCount, $pageWidth / $columnCount);
        }
        
        // Check if this is a table that needs special formatting
        if (in_array('Precio ($/Kg)', $header) || in_array('Dosis', $header)) {
            // Special column widths for tables with price or dose fields
            if ($columnCount == 3) {
                $w = array(45, 60, 75); // Date, Weight/Product, Price/Dose
            }
        }
        
        // Header with background
        $this->SetSearchableFont('Arial', 'B', 10);
        $this->SetFillColor(50, 120, 50); // Darker green for header
        $this->SetTextColor(255, 255, 255); // White text for better contrast
        for ($i = 0; $i < $columnCount; $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(0, 0, 0); // Reset to black text for data
        
        // Data
        $this->SetSearchableFont('Arial', '', 9); // Slightly smaller font to fit more text
        $this->SetFillColor(245, 250, 245); // Lighter green tint
        $fill = false;
        
        foreach ($data as $row) {
            // Make sure we have the right number of cells
            $rowCount = count($row);
            for ($i = 0; $i < $columnCount; $i++) {
                // If the cell exists in data, display it, otherwise display empty cell
                $cellContent = ($i < $rowCount) ? $row[$i] : '';
                
                // Center align all data cells for consistency
                $align = 'C';
                
                $this->Cell($w[$i], 6, $cellContent, 1, 0, $align, $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        
        // Add space after table
        $this->Ln(5);
    }
}

// Create PDF instance
try {
    $pdf = new PDF();
    $pdf->setAnimalData($animal);
    error_log("PDF instance created successfully");
} catch (Exception $e) {
    error_log('Failed to create PDF instance: ' . $e->getMessage());
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to create PDF instance: ' . $e->getMessage()]);
        exit;
    } else {
        die('Failed to create PDF instance: ' . $e->getMessage());
    }
}

// Set UTF-8 metadata for better searchability
try {
    $pdf->SetTitle('Reporte Veterinario - ' . $animal['nombre'] . ' (' . $animal['tagid'] . ')', true);
    $pdf->SetAuthor('Sistema Ganagram', true);
    $pdf->SetSubject('Historial Veterinario Completo', true);
    $pdf->SetKeywords('veterinario, ganado, bovino, historial, ' . $animal['tagid'] . ', ' . $animal['nombre'], true);
    $pdf->SetCreator('Ganagram - Sistema de Gestión Ganadera', true);
    error_log("PDF metadata set successfully");
} catch (Exception $e) {
    error_log('Failed to set PDF metadata: ' . $e->getMessage());
    // Continue anyway as this is not critical
}

$pdf->AliasNbPages();
try {
    $pdf->AddPage();
    error_log("First PDF page added successfully");
} catch (Exception $e) {
    error_log('Failed to add first page: ' . $e->getMessage());
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to add first page: ' . $e->getMessage()]);
        exit;
    } else {
        die('Failed to add first page: ' . $e->getMessage());
    }
}

// Basic animal information
$pdf->ChapterTitle('Datos');
$header = array('Concepto', 'Descripcion');
$data = array(
    array('Tag ID', $animal['tagid']),
    array('Nombre', $animal['nombre']),
    array('Fecha Nacimiento', $animal['fecha_nacimiento']),
    array('Genero', $animal['genero']),
    array('Raza', $animal['raza']),
    array('Etapa', $animal['etapa']),
    array('Grupo', $animal['grupo']),
    array('Estatus', $animal['estatus'])
);
$pdf->SimpleTable($header, $data);

// Peso history
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Pesos del animal');
$sql_weight = "SELECT vh_peso_tagid, vh_peso_fecha, vh_peso_animal, vh_peso_precio FROM vh_peso WHERE vh_peso_tagid = ? ORDER BY vh_peso_fecha DESC";
$stmt_weight = $conn->prepare($sql_weight);
$stmt_weight->execute([$tagid]);
$result_weight = $stmt_weight->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_weight)) {
    $header = array('Tag ID', 'Fecha', 'Peso (kg)', 'Precio ($/Kg)');
    $data = array();
    foreach ($result_weight as $row) {
        $data[] = array($row['vh_peso_tagid'], $row['vh_peso_fecha'], $row['vh_peso_animal'], $row['vh_peso_precio']);
    }
    $pdf->SimpleTable($header, $data);

} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay regisros de pesajes', 0, 1);
    $pdf->Ln(2);
}   

// Leche
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Leche del animal');
$sql_leche = "SELECT vh_leche_tagid, vh_leche_fecha_inicio, vh_leche_fecha_fin, vh_leche_peso, vh_leche_precio FROM vh_leche WHERE vh_leche_tagid = ? ORDER BY vh_leche_fecha_inicio DESC";
$stmt_leche = $conn->prepare($sql_leche);
$stmt_leche->execute([$tagid]);
$result_leche = $stmt_leche->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_leche)) {
    $header = array('Tag ID', 'Fecha Inicio', 'Fecha Fin', 'Peso (kg)', 'Precio ($/Kg)');
    $data = array();
    foreach ($result_leche as $row) {
        $data[] = array($row['vh_leche_tagid'], $row['vh_leche_fecha_inicio'], $row['vh_leche_fecha_fin'], $row['vh_leche_peso'], $row['vh_leche_precio']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de produccion de leche', 0, 1);
    $pdf->Ln(2);
}

// Concentrado
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Concentrado del animal');
$sql_concentrado = "SELECT vh_concentrado_tagid, vh_concentrado_fecha_inicio, vh_concentrado_fecha_fin, vh_concentrado_racion, vh_concentrado_costo FROM vh_concentrado WHERE vh_concentrado_tagid = ? ORDER BY vh_concentrado_fecha_inicio DESC";
$stmt_concentrado = $conn->prepare($sql_concentrado);
$stmt_concentrado->execute([$tagid]);
$result_concentrado = $stmt_concentrado->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_concentrado)) {
    $header = array('Tag ID', 'Fecha Inicio', 'Fecha Fin', 'Consumo Concentrado Peso (kg)', 'Precio ($/Kg)');
    $data = array();
    foreach ($result_concentrado as $row) {
        $data[] = array($row['vh_concentrado_tagid'], $row['vh_concentrado_fecha_inicio'], $row['vh_concentrado_fecha_fin'], $row['vh_concentrado_racion'], $row['vh_concentrado_costo']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de concentrado', 0, 1);
    $pdf->Ln(2);
}

// Salt
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Sal del animal');
$sql_salt = "SELECT vh_sal_tagid, vh_sal_fecha_inicio, vh_sal_fecha_fin, vh_sal_racion, vh_sal_costo FROM vh_sal WHERE vh_sal_tagid = ? ORDER BY vh_sal_fecha_inicio DESC";
$stmt_salt = $conn->prepare($sql_salt);
$stmt_salt->execute([$tagid]);
$result_salt = $stmt_salt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_salt)) {
    $header = array('Tag ID', 'Fecha Inicio', 'Fecha Fin', 'Consumo Sal Racion (Kg)', 'Costo ($/Kg)');
    $data = array();
    foreach ($result_salt as $row) {
        $data[] = array($row['vh_sal_tagid'], $row['vh_sal_fecha_inicio'], $row['vh_sal_fecha_fin'], $row['vh_sal_racion'], $row['vh_sal_costo']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de sal', 0, 1);
    $pdf->Ln(2);
}

// Molasses
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Melaza del animal');
$sql_molasses = "SELECT vh_melaza_tagid, vh_melaza_fecha_inicio, vh_melaza_fecha_fin, vh_melaza_racion, vh_melaza_costo FROM vh_melaza WHERE vh_melaza_tagid = ? ORDER BY vh_melaza_fecha_inicio DESC";
$stmt_molasses = $conn->prepare($sql_molasses);
$stmt_molasses->execute([$tagid]);
$result_molasses = $stmt_molasses->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_molasses)) {
    $header = array('Tag ID', 'Fecha Inicio', 'Fecha Fin', 'Consumo Melaza Racion (Kg)', 'Costo ($/Kg)');
    $data = array();
    foreach ($result_molasses as $row) {
        $data[] = array($row['vh_melaza_tagid'], $row['vh_melaza_fecha_inicio'], $row['vh_melaza_fecha_fin'], $row['vh_melaza_racion'], $row['vh_melaza_costo']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de melaza', 0, 1);
    $pdf->Ln(2);
}

// Vaccination - Aftosa
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Aftosa del animal');
$pdf->ChapterTitle('Aftosa');
$sql_aftosa = "SELECT vh_aftosa_tagid, vh_aftosa_fecha, vh_aftosa_producto, vh_aftosa_dosis FROM vh_aftosa WHERE vh_aftosa_tagid = ? ORDER BY vh_aftosa_fecha DESC";
$stmt_aftosa = $conn->prepare($sql_aftosa);
$stmt_aftosa->execute([$tagid]);
$result_aftosa = $stmt_aftosa->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_aftosa)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_aftosa as $row) {
        $data[] = array($row['vh_aftosa_tagid'], $row['vh_aftosa_fecha'], $row['vh_aftosa_producto'], $row['vh_aftosa_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de vacunacion aftosa', 0, 1);
    $pdf->Ln(2);
}

// Vaccination - Brucelosis
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Brucelosis');
$sql_bruc = "SELECT vh_brucelosis_tagid, vh_brucelosis_fecha, vh_brucelosis_producto, vh_brucelosis_dosis FROM vh_brucelosis WHERE vh_brucelosis_tagid = ? ORDER BY vh_brucelosis_fecha DESC";
$stmt_bruc = $conn->prepare($sql_bruc);
$stmt_bruc->execute([$tagid]);
$result_bruc = $stmt_bruc->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_bruc)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_bruc as $row) {
        $data[] = array($row['vh_brucelosis_tagid'], $row['vh_brucelosis_fecha'], $row['vh_brucelosis_producto'], $row['vh_brucelosis_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de vacunacion brucelosis', 0, 1);
    $pdf->Ln(2);
}

// Vaccination - Carbunco
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Carbunco');
$sql_carbunco = "SELECT vh_carbunco_tagid, vh_carbunco_fecha, vh_carbunco_producto, vh_carbunco_dosis FROM vh_carbunco WHERE vh_carbunco_tagid = ? ORDER BY vh_carbunco_fecha DESC";
$stmt_carbunco = $conn->prepare($sql_carbunco);
$stmt_carbunco->execute([$tagid]);
$result_carbunco = $stmt_carbunco->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_carbunco)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_carbunco as $row) {
        $data[] = array($row['vh_carbunco_tagid'], $row['vh_carbunco_fecha'], $row['vh_carbunco_producto'], $row['vh_carbunco_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de vacunacion carbunco', 0, 1);
    $pdf->Ln(2);
}

// Vaccination - IBR
$pdf->AddPage();
$pdf->ChapterTitle('Tabla IBR');
$sql_ibr = "SELECT vh_ibr_tagid, vh_ibr_fecha, vh_ibr_producto, vh_ibr_dosis FROM vh_ibr WHERE vh_ibr_tagid = ? ORDER BY vh_ibr_fecha DESC";
$stmt_ibr = $conn->prepare($sql_ibr);
$stmt_ibr->execute([$tagid]);
$result_ibr = $stmt_ibr->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_ibr)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_ibr as $row) {
        $data[] = array($row['vh_ibr_tagid'], $row['vh_ibr_fecha'], $row['vh_ibr_producto'], $row['vh_ibr_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de vacunacion IBR', 0, 1);
    $pdf->Ln(2);
}

// Vaccination - CBR
$pdf->AddPage();
$pdf->ChapterTitle('Tabla CBR');
$sql_cbr = "SELECT vh_cbr_tagid, vh_cbr_fecha, vh_cbr_producto, vh_cbr_dosis FROM vh_cbr WHERE vh_cbr_tagid = ? ORDER BY vh_cbr_fecha DESC";
$stmt_cbr = $conn->prepare($sql_cbr);
$stmt_cbr->execute([$tagid]);
$result_cbr = $stmt_cbr->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_cbr)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_cbr as $row) {
        $data[] = array($row['vh_cbr_tagid'], $row['vh_cbr_fecha'], $row['vh_cbr_producto'], $row['vh_cbr_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de vacunacion CBR', 0, 1);
    $pdf->Ln(2);
}

// Parasites Treatment
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Parasitos');
$sql_para = "SELECT vh_parasitos_tagid, vh_parasitos_fecha, vh_parasitos_producto, vh_parasitos_dosis FROM vh_parasitos WHERE vh_parasitos_tagid = ? ORDER BY vh_parasitos_fecha DESC";
$stmt_para = $conn->prepare($sql_para);
$stmt_para->execute([$tagid]);
$result_para = $stmt_para->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_para)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_para as $row) {
        $data[] = array($row['vh_parasitos_tagid'], $row['vh_parasitos_fecha'], $row['vh_parasitos_producto'], $row['vh_parasitos_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de tratamiento parasitos', 0, 1);
    $pdf->Ln(2);
}

// Garrapatas Treatment
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Garrapatas');
$sql_tick = "SELECT vh_garrapatas_tagid, vh_garrapatas_fecha, vh_garrapatas_producto, vh_garrapatas_dosis FROM vh_garrapatas WHERE vh_garrapatas_tagid = ? ORDER BY vh_garrapatas_fecha DESC";
$stmt_tick = $conn->prepare($sql_tick);
$stmt_tick->execute([$tagid]);
$result_tick = $stmt_tick->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_tick)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_tick as $row) {
        $data[] = array($row['vh_garrapatas_tagid'], $row['vh_garrapatas_fecha'], $row['vh_garrapatas_producto'], $row['vh_garrapatas_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de tratamiento garrapatas', 0, 1);
    $pdf->Ln(2);
}

// Mastitis Treatment
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Mastitis');
$sql_mastitis = "SELECT vh_mastitis_tagid, vh_mastitis_fecha, vh_mastitis_producto, vh_mastitis_dosis FROM vh_mastitis WHERE vh_mastitis_tagid = ? ORDER BY vh_mastitis_fecha DESC";
$stmt_mastitis = $conn->prepare($sql_mastitis);
$stmt_mastitis->execute([$tagid]);
$result_mastitis = $stmt_mastitis->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_mastitis)) {
    $header = array('Tag ID', 'Fecha', 'Producto', 'Dosis (ml)');
    $data = array();
    foreach ($result_mastitis as $row) {
        $data[] = array($row['vh_mastitis_tagid'], $row['vh_mastitis_fecha'], $row['vh_mastitis_producto'], $row['vh_mastitis_dosis']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de tratamiento mastitis', 0, 1);
    $pdf->Ln(2);
}

// Inseminacion
$pdf->AddPage();
$pdf->ChapterTitle('REPRODUCCION');
$pdf->ChapterTitle('Tabla Inseminaciones del animal');
$sql_ins = "SELECT vh_inseminacion_tagid, vh_inseminacion_fecha, vh_inseminacion_numero FROM vh_inseminacion WHERE vh_inseminacion_tagid = ? ORDER BY vh_inseminacion_fecha DESC";
$stmt_ins = $conn->prepare($sql_ins);
$stmt_ins->execute([$tagid]);
$result_ins = $stmt_ins->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_ins)) {
    $header = array('Tag ID', 'Fecha', 'Inseminacion Nro.');
    $data = array();
    foreach ($result_ins as $row) {
        $data[] = array($row['vh_inseminacion_tagid'], $row['vh_inseminacion_fecha'], $row['vh_inseminacion_numero']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de inseminaciones', 0, 1);
    $pdf->Ln(2);
}

// Gestacion
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Gestaciones del animal');
$sql_preg = "SELECT vh_gestacion_tagid, vh_gestacion_fecha, vh_gestacion_numero FROM vh_gestacion WHERE vh_gestacion_tagid = ? ORDER BY vh_gestacion_fecha DESC";
$stmt_preg = $conn->prepare($sql_preg);
$stmt_preg->execute([$tagid]);
$result_preg = $stmt_preg->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_preg)) {
    $header = array('Tag ID', 'Fecha', 'Gestacion Nro.');
    $data = array();
    foreach ($result_preg as $row) {
        $data[] = array($row['vh_gestacion_tagid'], $row['vh_gestacion_fecha'], $row['vh_gestacion_numero']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No registros de gestacion encontrados', 0, 1);
    $pdf->Ln(2);
}

// Parto
$pdf->AddPage();
$pdf->ChapterTitle('Tabla Partos del animal');
$sql_birth = "SELECT vh_parto_tagid, vh_parto_fecha, vh_parto_numero FROM vh_parto WHERE vh_parto_tagid = ? ORDER BY vh_parto_fecha DESC";
$stmt_birth = $conn->prepare($sql_birth);
$stmt_birth->execute([$tagid]);
$result_birth = $stmt_birth->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_birth)) {
    $header = array('Tag ID', 'Fecha', 'Parto Nro.');
    $data = array();
    foreach ($result_birth as $row) {
        $data[] = array($row['vh_parto_tagid'], $row['vh_parto_fecha'], $row['vh_parto_numero']);
    }
    $pdf->SimpleTable($header, $data);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de partos', 0, 1);
    $pdf->Ln(2);
}

// Add Monthly Farm Weight Statistics
$pdf->AddPage();
$pdf->ChapterTitle('ESTADISTICAS DE LA FINCA');

// Add Breed Distribution Statistics
$pdf->ChapterTitle('Razas');

// SQL to get breed distribution
$sql_breeds = "SELECT 
    raza,
    COUNT(*) as total_animales,
    ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM vacuno WHERE estatus = 'Activo'), 1) as porcentaje
FROM vacuno 
WHERE estatus = 'Activo'
GROUP BY raza
ORDER BY total_animales DESC";

$stmt_breeds = $conn->prepare($sql_breeds);
$stmt_breeds->execute();
$result_breeds = $stmt_breeds->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_breeds)) {
    $header = array('Raza', 'Total Animales', 'Porcentaje (%)');
    $data = array();
    $total_animals = 0;
    
    foreach ($result_breeds as $row) {
        $data[] = array(
            $row['raza'] ?: 'No Especificada',  // Handle NULL or empty breed
            $row['total_animales'],
            number_format($row['porcentaje'], 1)
        );
        $total_animals += $row['total_animales'];
    }
    
    // Add total row
    $data[] = array(
        'TOTAL',
        $total_animals,
        '100.0'
    );
    
    $pdf->SimpleTable($header, $data);
    
    // Add explanatory note
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Nota: Porcentajes calculados sobre el total de animales activos en el sistema.', 0, 1);
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de animales para generar la distribucion por razas', 0, 1);
    $pdf->Ln(2);
}

// Add Animal Distribution by Group
$pdf->ChapterTitle('Grupos');

// SQL to get animal distribution by group
$sql_groups = "SELECT 
    grupo,
    COUNT(*) as total_animales,
    ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM vacuno WHERE estatus = 'Activo'), 1) as porcentaje
FROM vacuno 
WHERE estatus = 'Activo'
GROUP BY grupo
ORDER BY total_animales DESC";

$stmt_groups = $conn->prepare($sql_groups);
$stmt_groups->execute();
$result_groups = $stmt_groups->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_groups)) {
    $header = array('Grupo', 'Total Animales', 'Porcentaje (%)');
    $data = array();
    $total_animals = 0;
    
    foreach ($result_groups as $row) {
        $data[] = array(
            $row['grupo'],
            $row['total_animales'],
            number_format($row['porcentaje'], 1)
        );
        $total_animals += $row['total_animals'];
    }
    
    // Add total row
    $data[] = array(
        'TOTAL',
        $total_animals,
        '100.0'
    );
    
    $pdf->SimpleTable($header, $data);
    
    // Add explanatory note
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Nota: Porcentajes calculados sobre el total de animales activos en el sistema.', 0, 1);
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de animales para generar la distribucion por grupos', 0, 1);
    $pdf->Ln(2);
}


//Estadisticas------------------

// Add Monthly Weight Statistics
$pdf->AddPage();
$pdf->ChapterTitle('Produccion Carnica');

// SQL to get monthly total weight with averages for multiple weights in same month
$sql_monthly = "WITH MonthlyWeights AS (
    SELECT 
        DATE_FORMAT(vh_peso_fecha, '%Y-%m-01') as primer_dia_mes,
        vh_peso_tagid,
        AVG(vh_peso_animal) as peso_promedio_animal
    FROM vh_peso 
    GROUP BY DATE_FORMAT(vh_peso_fecha, '%Y-%m-01'), vh_peso_tagid
)
SELECT 
    primer_dia_mes as mes,
    COUNT(DISTINCT vh_peso_tagid) as total_animales,
    ROUND(SUM(peso_promedio_animal), 2) as peso_total,
    ROUND(AVG(peso_promedio_animal), 2) as peso_promedio
FROM MonthlyWeights
GROUP BY primer_dia_mes
ORDER BY primer_dia_mes DESC
LIMIT 12";  // Last 12 months

$stmt_monthly = $conn->prepare($sql_monthly);
$stmt_monthly->execute();
$result_monthly = $stmt_monthly->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_monthly)) {
    $header = array('Mes', 'Total Animales', 'Peso Total (kg)', 'Promedio (kg)');
    $data = array();
    $total_weight = 0;
    $total_months = 0;
    $min_weight = PHP_FLOAT_MAX;
    $max_weight = 0;
    
    foreach ($result_monthly as $row) {
        // Format the month to Spanish format
        $date = DateTime::createFromFormat('Y-m-d', $row['mes']);
        $mes_espanol = strftime('%B %Y', $date->getTimestamp());
        $mes_espanol = ucfirst(mb_strtolower($mes_espanol, 'UTF-8'));
        
        $data[] = array(
            $mes_espanol,
            $row['total_animales'],
            number_format($row['peso_total'], 2),
            number_format($row['peso_promedio'], 2)
        );
        
        // Track statistics
        $total_weight += $row['peso_promedio'];
        $total_months++;
        $min_weight = min($min_weight, $row['peso_promedio']);
        $max_weight = max($max_weight, $row['peso_promedio']);
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics
    if ($total_months > 0) {
        $overall_average = $total_weight / $total_months;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'ESTADISTICAS DE PESO:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, sprintf('Promedio General: %.2f kg', $overall_average), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Peso Minimo Mensual: %.2f kg', $min_weight), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Peso Maximo Mensual: %.2f kg', $max_weight), 0, 1, 'L');
    }
    
    // Add explanatory note
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Notas:
- Los pesos se calculan como un promedio mensual por animal.
- Si hay varios pesos para un animal en el mismo mes, se usa el promedio.
- El peso total es la suma de los pesos promedio de todos los animales del mes.
- El promedio mensual es el peso total dividido por el numero de animales.
- Las estadisticas muestran la tendencia de peso en los ultimos 12 meses.', 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de peso para generar estadisticas mensuales', 0, 1);
    $pdf->Ln(2);
}

// Add Monthly Milk Production Statistics
$pdf->AddPage();    
$pdf->ChapterTitle('Produccion lechera');

// SQL to get monthly milk production with daily calculations and costs
$sql_milk_monthly = "WITH MonthlyMilk AS (
    SELECT 
        DATE_FORMAT(vh_leche_fecha_inicio, '%Y-%m-01') as primer_dia_mes,
        vh_leche_tagid,
        AVG(vh_leche_peso) as produccion_diaria_promedio,
        AVG(vh_leche_precio) as precio_promedio
    FROM vh_leche
    GROUP BY DATE_FORMAT(vh_leche_fecha_inicio, '%Y-%m-01'), vh_leche_tagid
),
MonthlyStats AS (
    SELECT 
        primer_dia_mes as mes,
        COUNT(DISTINCT vh_leche_tagid) as total_vacas,
        ROUND(SUM(produccion_diaria_promedio), 2) as produccion_diaria_total,
        ROUND(AVG(produccion_diaria_promedio), 2) as promedio_diario_por_vaca,
        ROUND(AVG(precio_promedio), 2) as precio_promedio_mes
    FROM MonthlyMilk
    GROUP BY primer_dia_mes
)
SELECT 
    mes,
    total_vacas,
    produccion_diaria_total,
    produccion_diaria_total * DAY(LAST_DAY(mes)) as produccion_total_mes,
    promedio_diario_por_vaca,
    promedio_diario_por_vaca * DAY(LAST_DAY(mes)) as promedio_mensual_por_vaca,
    precio_promedio_mes
FROM MonthlyStats
ORDER BY mes DESC
LIMIT 12";

$stmt_milk_monthly = $conn->prepare($sql_milk_monthly);
$stmt_milk_monthly->execute();
$result_milk_monthly = $stmt_milk_monthly->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_milk_monthly)) {
    $header = array('Mes', '# Vacas', 'Prod. Diaria', 'Prod. Mensual', 'Diario x Vaca', 'Mensual x Vaca', 'Precio Prom.');
    $data = array();
    
    // Statistics tracking
    $total_production = 0;
    $total_months = 0;
    $min_daily_per_cow = PHP_FLOAT_MAX;
    $max_daily_per_cow = 0;
    $total_daily_per_cow = 0;
    
    foreach ($result_milk_monthly as $row) {
        // Format the month to Spanish format
        $date = DateTime::createFromFormat('Y-m-d', $row['mes']);
        $mes_espanol = strftime('%B %Y', $date->getTimestamp());
        $mes_espanol = ucfirst(mb_strtolower($mes_espanol, 'UTF-8'));
        
        $data[] = array(
            $mes_espanol,
            $row['total_vacas'],
            number_format($row['produccion_diaria_total'], 2),
            number_format($row['produccion_total_mes'], 2),
            number_format($row['promedio_diario_por_vaca'], 2),
            number_format($row['promedio_mensual_por_vaca'], 2),
            number_format($row['precio_promedio_mes'], 2)
        );
        
        // Track statistics
        $total_production += $row['produccion_total_mes'];
        $total_daily_per_cow += $row['promedio_diario_por_vaca'];
        $min_daily_per_cow = min($min_daily_per_cow, $row['promedio_diario_por_vaca']);
        $max_daily_per_cow = max($max_daily_per_cow, $row['promedio_diario_por_vaca']);
        $total_months++;
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics summary
    if ($total_months > 0) {
        $avg_daily_per_cow = $total_daily_per_cow / $total_months;
        $avg_monthly_production = $total_production / $total_months;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, sprintf('Produccion Mensual Promedio: %.2f litros', $avg_monthly_production), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Promedio Diario por Vaca: %.2f litros', $avg_daily_per_cow), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Minimo Diario por Vaca: %.2f litros', $min_daily_per_cow), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Maximo Diario por Vaca: %.2f litros', $max_daily_per_cow), 0, 1, 'L');
    }
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Notas:
- La produccion se calcula como un promedio diario por animal por mes.
- Si hay varios registros para un animal en el mismo mes, se usa el promedio.
- La produccion diaria total es la suma de los promedios diarios de todas las vacas.
- La produccion mensual se calcula multiplicando la produccion diaria por los dias del mes.
- El promedio por vaca representa la produccion individual promedio.
- Los precios mostrados son promedios mensuales por litro.
- Las estadisticas muestran la tendencia de produccion en los ultimos 12 meses.', 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de produccion de leche para generar estadisticas mensuales', 0, 1);
    $pdf->Ln(2);
}

// Add Monthly Feed Consumption Statistics
$pdf->AddPage();    
$pdf->ChapterTitle('Consumo Concentrado');

// SQL to get monthly feed consumption with daily calculations and costs
$sql_feed_monthly = "WITH MonthlyFeed AS (
    SELECT 
        DATE_FORMAT(vh_concentrado_fecha_inicio, '%Y-%m-01') as primer_dia_mes,
        vh_concentrado_tagid,
        AVG(vh_concentrado_racion) as consumo_diario_promedio,
        AVG(vh_concentrado_costo) as costo_promedio
    FROM vh_concentrado
    GROUP BY DATE_FORMAT(vh_concentrado_fecha_inicio, '%Y-%m-01'), vh_concentrado_tagid
),
MonthlyStats AS (
    SELECT 
        primer_dia_mes as mes,
        COUNT(DISTINCT vh_concentrado_tagid) as total_animales,
        ROUND(SUM(consumo_diario_promedio), 2) as consumo_diario_total,
        ROUND(AVG(consumo_diario_promedio), 2) as promedio_diario_por_animal,
        ROUND(AVG(costo_promedio), 2) as costo_promedio_mes
    FROM MonthlyFeed
    GROUP BY primer_dia_mes
)
SELECT 
    mes,
    total_animales,
    consumo_diario_total,
    consumo_diario_total * DAY(LAST_DAY(mes)) as consumo_total_mes,
    promedio_diario_por_animal,
    promedio_diario_por_animal * DAY(LAST_DAY(mes)) as promedio_mensual_por_animal,
    costo_promedio_mes,
    ROUND(consumo_diario_total * DAY(LAST_DAY(mes)) * costo_promedio_mes, 2) as costo_total_mes
FROM MonthlyStats
ORDER BY mes DESC";

$stmt_feed_monthly = $conn->prepare($sql_feed_monthly);
$stmt_feed_monthly->execute();
$result_feed_monthly = $stmt_feed_monthly->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_feed_monthly)) {
    $header = array('Mes', '# Animales', 'C. Diario', 'C. Mensual', 'Diario x Animal', 'Mensual x Animal', 'Precio Prom.', 'Total');
    $data = array();
    
    // Statistics tracking
    $total_consumption = 0;
    $total_cost = 0;
    $total_months = 0;
    $min_daily_per_animal = PHP_FLOAT_MAX;
    $max_daily_per_animal = 0;
    $total_daily_per_animal = 0;
    
    foreach ($result_feed_monthly as $row) {
        // Format the month to Spanish format
        $date = DateTime::createFromFormat('Y-m-d', $row['mes']);
        $mes_espanol = strftime('%B %Y', $date->getTimestamp());
        $mes_espanol = ucfirst(mb_strtolower($mes_espanol, 'UTF-8'));
        
        $data[] = array(
            $mes_espanol,
            $row['total_animales'],
            number_format($row['consumo_diario_total'], 2),
            number_format($row['consumo_total_mes'], 2),
            number_format($row['promedio_diario_por_animal'], 2),
            number_format($row['promedio_mensual_por_animal'], 2),
            number_format($row['costo_promedio_mes'], 2),
            number_format($row['costo_total_mes'], 2)
        );
        
        // Track statistics
        $total_consumption += $row['consumo_total_mes'];
        $total_cost += $row['costo_total_mes'];
        $total_daily_per_animal += $row['promedio_diario_por_animal'];
        $min_daily_per_animal = min($min_daily_per_animal, $row['promedio_diario_por_animal']);
        $max_daily_per_animal = max($max_daily_per_animal, $row['promedio_diario_por_animal']);
        $total_months++;
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics summary
    if ($total_months > 0) {
        $avg_daily_per_animal = $total_daily_per_animal / $total_months;
        $avg_monthly_consumption = $total_consumption / $total_months;
        $avg_monthly_cost = $total_cost / $total_months;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, sprintf('Consumo Mensual Promedio: %.2f kg', $avg_monthly_consumption), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Promedio Diario por Animal: %.2f kg', $avg_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Minimo Diario por Animal: %.2f kg', $min_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Maximo Diario por Animal: %.2f kg', $max_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Costo Mensual Promedio: $%.2f', $avg_monthly_cost), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, sprintf('Costo Total (12 meses): $%.2f', $total_cost), 0, 1, 'L');
    }
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Notas:
- El consumo se calcula como un promedio diario por animal por mes.
- Si hay varios registros para un animal en el mismo mes, se usa el promedio.
- El consumo diario total es la suma de los promedios diarios de todos los animales.
- El consumo mensual se calcula multiplicando el consumo diario por los dias del mes.
- El promedio por animal representa el consumo individual promedio.
- Los precios mostrados son promedios mensuales por kilogramo.
- El costo total incluye el consumo mensual multiplicado por el precio promedio.
- Las estadisticas muestran la tendencia de consumo en los ultimos 12 meses.', 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de alimento concentrado para generar estadisticas mensuales', 0, 1);
    $pdf->Ln(2);
}

// Add Feed Conversion Ratio Analysis
$pdf->AddPage();
$pdf->ChapterTitle('Conversion');

// SQL to calculate FCR using total feed and weight gain
$sql_fcr = "WITH AllAnimals AS (
    SELECT tagid, nombre, fecha_nacimiento, genero, etapa 
    FROM vacuno 
    WHERE estatus = 'Activo'
),
MonthlyWeights AS (
    SELECT 
        vh_peso_tagid,
        DATE_FORMAT(vh_peso_fecha, '%Y-%m-01') as mes,
        AVG(vh_peso_animal) as peso_promedio
    FROM vh_peso
    GROUP BY vh_peso_tagid, DATE_FORMAT(vh_peso_fecha, '%Y-%m-01')
),
WeightChanges AS (
    SELECT 
        w1.vh_peso_tagid,
        w1.mes as mes_inicial,
        w2.mes as mes_final,
        w1.peso_promedio as peso_inicial,
        w2.peso_promedio as peso_final,
        w2.peso_promedio - w1.peso_promedio as ganancia_peso
    FROM MonthlyWeights w1
    JOIN MonthlyWeights w2 ON w1.vh_peso_tagid = w2.vh_peso_tagid
        AND w1.mes < w2.mes
        AND NOT EXISTS (
            SELECT 1 FROM MonthlyWeights w3
            WHERE w3.vh_peso_tagid = w1.vh_peso_tagid
            AND w3.mes > w1.mes AND w3.mes < w2.mes
        )
),
TotalFeed AS (
    SELECT 
        vh_concentrado_tagid,
        DATE_FORMAT(vh_concentrado_fecha_inicio, '%Y-%m-01') as mes,
        SUM(vh_concentrado_racion) as consumo_total
    FROM vh_concentrado
    GROUP BY vh_concentrado_tagid, DATE_FORMAT(vh_concentrado_fecha_inicio, '%Y-%m-01')
),
FCRCalculation AS (
    SELECT 
        wc.vh_peso_tagid,
        a.nombre,
        a.genero,
        a.etapa,
        wc.mes_inicial,
        wc.mes_final,
        wc.peso_inicial,
        wc.peso_final,
        wc.ganancia_peso,
        SUM(tf.consumo_total) as consumo_periodo,
        CASE 
            WHEN wc.ganancia_peso > 0 THEN SUM(tf.consumo_total) / wc.ganancia_peso
            ELSE NULL
        END as fcr
    FROM WeightChanges wc
    JOIN AllAnimals a ON wc.vh_peso_tagid = a.tagid
    LEFT JOIN TotalFeed tf ON wc.vh_peso_tagid = tf.vh_concentrado_tagid
        AND tf.mes >= wc.mes_inicial AND tf.mes <= wc.mes_final
    GROUP BY wc.vh_peso_tagid, a.nombre, a.genero, a.etapa, wc.mes_inicial, wc.mes_final, 
             wc.peso_inicial, wc.peso_final, wc.ganancia_peso
    HAVING consumo_periodo IS NOT NULL AND ganancia_peso > 0
)
SELECT 
    (SELECT COUNT(*) FROM AllAnimals) as total_animales_hato,
    COUNT(DISTINCT vh_peso_tagid) as animales_con_ica,
    ROUND(AVG(fcr), 2) as fcr_promedio,
    ROUND(MIN(fcr), 2) as fcr_minimo,
    ROUND(MAX(fcr), 2) as fcr_maximo,
    ROUND(SUM(consumo_periodo), 2) as consumo_total,
    ROUND(SUM(ganancia_peso), 2) as ganancia_total,
    ROUND(SUM(consumo_periodo) / SUM(ganancia_peso), 2) as fcr_global
FROM FCRCalculation";

$stmt_fcr = $conn->prepare($sql_fcr);
$stmt_fcr->execute();
$result_fcr = $stmt_fcr->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_fcr)) {
    $fcr_data = $result_fcr[0];
    
    // Display FCR Statistics
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'ESTADISTICAS DE CONVERSION:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, sprintf('Total de Animales en el Hato: %d', $fcr_data['total_animales_hato']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Animales con Datos Suficientes para ICA: %d (%.1f%%)', 
        $fcr_data['animales_con_ica'],
        ($fcr_data['animales_con_ica'] / $fcr_data['total_animales_hato']) * 100
    ), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Consumo Total de Alimento: %.2f kg', $fcr_data['consumo_total']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Ganancia Total de Peso: %.2f kg', $fcr_data['ganancia_total']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('ICA Global del Hato: %.2f', $fcr_data['fcr_global']), 0, 1, 'L');
    $pdf->Ln(2);
    $pdf->Cell(0, 6, 'RANGOS DE ICA:', 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('ICA Promedio: %.2f', $fcr_data['fcr_promedio']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('ICA Minimo: %.2f', $fcr_data['fcr_minimo']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('ICA Maximo: %.2f', $fcr_data['fcr_maximo']), 0, 1, 'L');
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, sprintf('Notas:
- El Indice de Conversion Alimenticia (ICA) se calcula como: Alimento Consumido / Ganancia de Peso
- Un ICA mas bajo indica mejor eficiencia en la conversion de alimento a peso
- El ICA Global representa la eficiencia general del hato
- De los %d animales en el hato, solo %d tienen datos suficientes para calcular el ICA
- Se consideran solo periodos con registros completos de peso y consumo
- Los calculos se basan en promedios mensuales de peso y consumo total de alimento
- Solo se incluyen animales con ganancia de peso positiva
- El analisis requiere al menos dos pesajes y registros de consumo en el periodo', 
        $fcr_data['total_animales_hato'],
        $fcr_data['animales_con_ica']
    ), 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay suficientes datos para calcular el Indice de Conversion Alimenticia', 0, 1);
    $pdf->Ln(2);
}

// Add Monthly Molasses Consumption Statistics
$pdf->AddPage();
$pdf->ChapterTitle('Consumo Melaza');

// SQL to get monthly molasses consumption with daily calculations and costs
$sql_molasses_monthly = "WITH MonthlyMolasses AS (
    SELECT 
        DATE_FORMAT(vh_melaza_fecha_inicio, '%Y-%m-01') as primer_dia_mes,
        vh_melaza_tagid,
        AVG(vh_melaza_racion) as consumo_diario_promedio,
        AVG(vh_melaza_costo) as costo_promedio
    FROM vh_melaza
    GROUP BY DATE_FORMAT(vh_melaza_fecha_inicio, '%Y-%m-01'), vh_melaza_tagid
),
MonthlyStats AS (
    SELECT 
        primer_dia_mes as mes,
        COUNT(DISTINCT vh_melaza_tagid) as total_animales,
        ROUND(SUM(consumo_diario_promedio), 2) as consumo_diario_total,
        ROUND(AVG(consumo_diario_promedio), 2) as promedio_diario_por_animal,
        ROUND(AVG(costo_promedio), 2) as costo_promedio_mes
    FROM MonthlyMolasses
    GROUP BY primer_dia_mes
)
SELECT 
    mes,
    total_animales,
    consumo_diario_total,
    consumo_diario_total * DAY(LAST_DAY(mes)) as consumo_total_mes,
    promedio_diario_por_animal,
    promedio_diario_por_animal * DAY(LAST_DAY(mes)) as promedio_mensual_por_animal,
    costo_promedio_mes,
    ROUND(consumo_diario_total * DAY(LAST_DAY(mes)) * costo_promedio_mes, 2) as costo_total_mes
FROM MonthlyStats
ORDER BY mes DESC
LIMIT 12";

$stmt_molasses_monthly = $conn->prepare($sql_molasses_monthly);
$stmt_molasses_monthly->execute();
$result_molasses_monthly = $stmt_molasses_monthly->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_molasses_monthly)) {
    $header = array('Mes', '# Animales', 'C. Diario', 'C. Mensual', 'Diario x Animal', 'Mensual x Animal', 'Precio Prom.', 'Total');
    $data = array();
    
    // Statistics tracking
    $total_consumption = 0;
    $total_cost = 0;
    $total_months = 0;
    $min_daily_per_animal = PHP_FLOAT_MAX;
    $max_daily_per_animal = 0;
    $total_daily_per_animal = 0;
    
    foreach ($result_molasses_monthly as $row) {
        // Format the month to Spanish format
        $date = DateTime::createFromFormat('Y-m-d', $row['mes']);
        $mes_espanol = strftime('%B %Y', $date->getTimestamp());
        $mes_espanol = ucfirst(mb_strtolower($mes_espanol, 'UTF-8'));
        
        $data[] = array(
            $mes_espanol,
            $row['total_animales'],
            number_format($row['consumo_diario_total'], 2),
            number_format($row['consumo_total_mes'], 2),
            number_format($row['promedio_diario_por_animal'], 2),
            number_format($row['promedio_mensual_por_animal'], 2),
            number_format($row['costo_promedio_mes'], 2),
            number_format($row['costo_total_mes'], 2)
        );
        
        // Track statistics
        $total_consumption += $row['consumo_total_mes'];
        $total_cost += $row['costo_total_mes'];
        $total_daily_per_animal += $row['promedio_diario_por_animal'];
        $min_daily_per_animal = min($min_daily_per_animal, $row['promedio_diario_por_animal']);
        $max_daily_per_animal = max($max_daily_per_animal, $row['promedio_diario_por_animal']);
        $total_months++;
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics summary
    if ($total_months > 0) {
        $avg_daily_per_animal = $total_daily_per_animal / $total_months;
        $avg_monthly_consumption = $total_consumption / $total_months;
        $avg_monthly_cost = $total_cost / $total_months;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, sprintf('Consumo Mensual Promedio: %.2f kg', $avg_monthly_consumption), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Promedio Diario por Animal: %.2f kg', $avg_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Minimo Diario por Animal: %.2f kg', $min_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Maximo Diario por Animal: %.2f kg', $max_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Costo Mensual Promedio: $%.2f', $avg_monthly_cost), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, sprintf('Costo Total (12 meses): $%.2f', $total_cost), 0, 1, 'L');
    }
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Notas:
- El consumo se calcula como un promedio diario por animal por mes.
- Si hay varios registros para un animal en el mismo mes, se usa el promedio.
- El consumo diario total es la suma de los promedios diarios de todos los animales.
- El consumo mensual se calcula multiplicando el consumo diario por los dias del mes.
- El promedio por animal representa el consumo individual promedio.
- Los precios mostrados son promedios mensuales por kilogramo.
- El costo total incluye el consumo mensual multiplicado por el precio promedio.
- Las estadisticas muestran la tendencia de consumo en los ultimos 12 meses.', 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de melaza para generar estadisticas mensuales', 0, 1);
    $pdf->Ln(2);
}

// Add Monthly Salt Consumption Statistics
$pdf->AddPage();
$pdf->ChapterTitle('Consumo Sal');

// SQL to get monthly salt consumption with daily calculations and costs
$sql_salt_monthly = "WITH MonthlySalt AS (
    SELECT 
        DATE_FORMAT(vh_sal_fecha_inicio, '%Y-%m-01') as primer_dia_mes,
        vh_sal_tagid,
        AVG(vh_sal_racion) as consumo_diario_promedio,
        AVG(vh_sal_costo) as costo_promedio
    FROM vh_sal
    GROUP BY DATE_FORMAT(vh_sal_fecha_inicio, '%Y-%m-01'), vh_sal_tagid
),
MonthlyStats AS (
    SELECT 
        primer_dia_mes as mes,
        COUNT(DISTINCT vh_sal_tagid) as total_animales,
        ROUND(SUM(consumo_diario_promedio), 2) as consumo_diario_total,
        ROUND(AVG(consumo_diario_promedio), 2) as promedio_diario_por_animal,
        ROUND(AVG(costo_promedio), 2) as costo_promedio_mes
    FROM MonthlySalt
    GROUP BY primer_dia_mes
)
SELECT 
    mes,
    total_animales,
    consumo_diario_total,
    consumo_diario_total * DAY(LAST_DAY(mes)) as consumo_total_mes,
    promedio_diario_por_animal,
    promedio_diario_por_animal * DAY(LAST_DAY(mes)) as promedio_mensual_por_animal,
    costo_promedio_mes,
    ROUND(consumo_diario_total * DAY(LAST_DAY(mes)) * costo_promedio_mes, 2) as costo_total_mes
FROM MonthlyStats
ORDER BY mes DESC
LIMIT 12";

$stmt_salt_monthly = $conn->prepare($sql_salt_monthly);
$stmt_salt_monthly->execute();
$result_salt_monthly = $stmt_salt_monthly->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_salt_monthly)) {
    $header = array('Mes', '# Animales', 'C. Diario', 'C. Mensual', 'Diario x Animal', 'Mensual x Animal', 'Precio Prom.', 'Total');
    $data = array();
    
    // Statistics tracking
    $total_consumption = 0;
    $total_cost = 0;
    $total_months = 0;
    $min_daily_per_animal = PHP_FLOAT_MAX;
    $max_daily_per_animal = 0;
    $total_daily_per_animal = 0;
    
    foreach ($result_salt_monthly as $row) {
        // Format the month to Spanish format
        $date = DateTime::createFromFormat('Y-m-d', $row['mes']);
        $mes_espanol = strftime('%B %Y', $date->getTimestamp());
        $mes_espanol = ucfirst(mb_strtolower($mes_espanol, 'UTF-8'));
        
        $data[] = array(
            $mes_espanol,
            $row['total_animales'],
            number_format($row['consumo_diario_total'], 2),
            number_format($row['consumo_total_mes'], 2),
            number_format($row['promedio_diario_por_animal'], 2),
            number_format($row['promedio_mensual_por_animal'], 2),
            number_format($row['costo_promedio_mes'], 2),
            number_format($row['costo_total_mes'], 2)
        );
        
        // Track statistics
        $total_consumption += $row['consumo_total_mes'];
        $total_cost += $row['costo_total_mes'];
        $total_daily_per_animal += $row['promedio_diario_por_animal'];
        $min_daily_per_animal = min($min_daily_per_animal, $row['promedio_diario_por_animal']);
        $max_daily_per_animal = max($max_daily_per_animal, $row['promedio_diario_por_animal']);
        $total_months++;
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics summary
    if ($total_months > 0) {
        $avg_daily_per_animal = $total_daily_per_animal / $total_months;
        $avg_monthly_consumption = $total_consumption / $total_months;
        $avg_monthly_cost = $total_cost / $total_months;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
        $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, sprintf('Consumo Mensual Promedio: %.2f kg', $avg_monthly_consumption), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Promedio Diario por Animal: %.2f kg', $avg_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Minimo Diario por Animal: %.2f kg', $min_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Maximo Diario por Animal: %.2f kg', $max_daily_per_animal), 0, 1, 'L');
        $pdf->Cell(0, 6, sprintf('Costo Mensual Promedio: $%.2f', $avg_monthly_cost), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, sprintf('Costo Total (12 meses): $%.2f', $total_cost), 0, 1, 'L');
    }
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Notas:
- El consumo se calcula como un promedio diario por animal por mes.
- Si hay varios registros para un animal en el mismo mes, se usa el promedio.
- El consumo diario total es la suma de los promedios diarios de todos los animales.
- El consumo mensual se calcula multiplicando el consumo diario por los dias del mes.
- El promedio por animal representa el consumo individual promedio.
- Los precios mostrados son promedios mensuales por kilogramo.
- El costo total incluye el consumo mensual multiplicado por el precio promedio.
- Las estadisticas muestran la tendencia de consumo en los ultimos 12 meses.', 0, 'L');
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de consumo de sal para generar estadisticas mensuales', 0, 1);
    $pdf->Ln(2);
}

// Add Vaccination Summary
$pdf->AddPage();
$pdf->ChapterTitle('Vacunas');

// SQL to get vaccination counts
$sql_vacc_summary = "
WITH AllAnimals AS (
    SELECT DISTINCT tagid FROM vacuno WHERE estatus = 'Activo'
),
VaccinationCounts AS (
    SELECT 
        (SELECT COUNT(*) FROM AllAnimals) as total_animals,
        (SELECT COUNT(DISTINCT vh_aftosa_tagid) FROM vh_aftosa) as aftosa_count,
        (SELECT COUNT(DISTINCT vh_brucelosis_tagid) FROM vh_brucelosis) as brucelosis_count,
        (SELECT COUNT(DISTINCT vh_cbr_tagid) FROM vh_cbr) as cbr_count,
        (SELECT COUNT(DISTINCT vh_ibr_tagid) FROM vh_ibr) as ibr_count,
        (SELECT COUNT(DISTINCT vh_carbunco_tagid) FROM vh_carbunco) as carbunco_count,
        (SELECT COUNT(DISTINCT vh_garrapatas_tagid) FROM vh_garrapatas) as garrapatas_count,
        (SELECT COUNT(DISTINCT vh_parasitos_tagid) FROM vh_parasitos) as parasitos_count,
        (SELECT COALESCE(SUM(vh_aftosa_costo * vh_aftosa_dosis), 0) FROM vh_aftosa) as aftosa_cost,
        (SELECT COALESCE(SUM(vh_brucelosis_costo * vh_brucelosis_dosis), 0) FROM vh_brucelosis) as brucelosis_cost,
        (SELECT COALESCE(SUM(vh_cbr_costo * vh_cbr_dosis), 0) FROM vh_cbr) as cbr_cost,
        (SELECT COALESCE(SUM(vh_ibr_costo * vh_ibr_dosis), 0) FROM vh_ibr) as ibr_cost,
        (SELECT COALESCE(SUM(vh_carbunco_costo * vh_carbunco_dosis), 0) FROM vh_carbunco) as carbunco_cost,
        (SELECT COALESCE(SUM(vh_garrapatas_costo * vh_garrapatas_dosis), 0) FROM vh_garrapatas) as garrapatas_cost,
        (SELECT COALESCE(SUM(vh_parasitos_costo * vh_parasitos_dosis), 0) FROM vh_parasitos) as parasitos_cost
)
SELECT 
    total_animals,
    aftosa_count,
    total_animals - aftosa_count as aftosa_pending,
    aftosa_cost,
    brucelosis_count,
    total_animals - brucelosis_count as brucelosis_pending,
    brucelosis_cost,
    cbr_count,
    total_animals - cbr_count as cbr_pending,
    cbr_cost,
    ibr_count,
    total_animals - ibr_count as ibr_pending,
    ibr_cost,
    carbunco_count,
    total_animals - carbunco_count as carbunco_pending,
    carbunco_cost,
    garrapatas_count,
    total_animals - garrapatas_count as garrapatas_pending,
    garrapatas_cost,
    parasitos_count,
    total_animals - parasitos_count as parasitos_pending,
    parasitos_cost
FROM VaccinationCounts";

$stmt_vacc = $conn->prepare($sql_vacc_summary);
$stmt_vacc->execute();
$result_vacc = $stmt_vacc->fetchAll(PDO::FETCH_ASSOC);
$vacc_data = $result_vacc[0];

// Create summary table
$header = array('Tratamiento', 'Animales Tratados', 'Animales Pendientes', 'Costo Total');
$data = array(
    array('Aftosa', $vacc_data['aftosa_count'], $vacc_data['aftosa_pending'], '$' . number_format($vacc_data['aftosa_cost'], 2)),
    array('Brucelosis', $vacc_data['brucelosis_count'], $vacc_data['brucelosis_pending'], '$' . number_format($vacc_data['brucelosis_cost'], 2)),
    array('CBR', $vacc_data['cbr_count'], $vacc_data['cbr_pending'], '$' . number_format($vacc_data['cbr_cost'], 2)),
    array('IBR', $vacc_data['ibr_count'], $vacc_data['ibr_pending'], '$' . number_format($vacc_data['ibr_cost'], 2)),
    array('Carbunco', $vacc_data['carbunco_count'], $vacc_data['carbunco_pending'], '$' . number_format($vacc_data['carbunco_cost'], 2)),
    array('Garrapatas', $vacc_data['garrapatas_count'], $vacc_data['garrapatas_pending'], '$' . number_format($vacc_data['garrapatas_cost'], 2)),
    array('Parasitos', $vacc_data['parasitos_count'], $vacc_data['parasitos_pending'], '$' . number_format($vacc_data['parasitos_cost'], 2))
);

$pdf->SimpleTable($header, $data);

// Calculate total vaccination cost
$total_vacc_cost = $vacc_data['aftosa_cost'] + 
                   $vacc_data['brucelosis_cost'] + 
                   $vacc_data['cbr_cost'] + 
                   $vacc_data['ibr_cost'] + 
                   $vacc_data['carbunco_cost'] + 
                   $vacc_data['garrapatas_cost'] + 
                   $vacc_data['parasitos_cost'];

// Add total cost line
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(3);
$pdf->Cell(0, 6, sprintf('Costo Total en Tratamientos: $%.2f', $total_vacc_cost), 0, 1, 'R');

// Add explanatory note
$pdf->SetFont('Arial', 'I', 9);
$pdf->Ln(2);
$pdf->MultiCell(0, 5, sprintf('Nota: Basado en un total de %d animales activos en el sistema. Los animales pendientes son aquellos que no tienen ningun registro historico del tratamiento correspondiente. Los costos totales incluyen todos los tratamientos historicos realizados.', $vacc_data['total_animals']), 0, 'L');

// Add Pregnancy Duration Statistics
$pdf->AddPage();
$pdf->ChapterTitle('Preñez');

// SQL to calculate pregnancy duration including current pregnancies
$sql_preg_duration = "SELECT 
    g.vh_gestacion_tagid,
    v.nombre,
    g.vh_gestacion_numero,
    g.vh_gestacion_fecha,
    p.vh_parto_fecha,
    CASE 
        WHEN p.vh_parto_fecha IS NOT NULL THEN 'Completada'
        ELSE 'En Curso'
    END as estado,
    DATEDIFF(COALESCE(p.vh_parto_fecha, CURDATE()), g.vh_gestacion_fecha) as dias_gestacion
FROM vh_gestacion g
LEFT JOIN vh_parto p ON g.vh_gestacion_tagid = p.vh_parto_tagid 
    AND g.vh_gestacion_numero = p.vh_parto_numero
LEFT JOIN vacuno v ON g.vh_gestacion_tagid = v.tagid
ORDER BY g.vh_gestacion_tagid, g.vh_gestacion_fecha DESC";

$stmt_preg_duration = $conn->prepare($sql_preg_duration);
$stmt_preg_duration->execute();
$result_preg_duration = $stmt_preg_duration->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_preg_duration)) {
    $header = array('Tag ID', 'Nombre', 'Gest. Nro.', 'F. Gestacion', 'F. Parto', 'Estado', 'Dias');
    $data = array();
    $total_days_completed = 0;
    $count_completed = 0;
    $current_tag = '';
    $tag_stats = array();
    
    foreach ($result_preg_duration as $row) {
        $parto_fecha = $row['vh_parto_fecha'] ? $row['vh_parto_fecha'] : 'En Curso';
        $data[] = array(
            $row['vh_gestacion_tagid'],
            $row['nombre'],
            $row['vh_gestacion_numero'],
            $row['vh_gestacion_fecha'],
            $parto_fecha,
            $row['estado'],
            $row['dias_gestacion']
        );
        
        // Collect statistics per animal
        $tagid = $row['vh_gestacion_tagid'];
        if (!isset($tag_stats[$tagid])) {
            $tag_stats[$tagid] = array(
                'total_days' => 0,
                'count' => 0,
                'nombre' => $row['nombre']
            );
        }
        
        // Only include completed pregnancies in the statistics
        if ($row['estado'] === 'Completada') {
            $total_days_completed += $row['dias_gestacion'];
            $count_completed++;
            $tag_stats[$tagid]['total_days'] += $row['dias_gestacion'];
            $tag_stats[$tagid]['count']++;
        }
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add overall statistics
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
    
    if ($count_completed > 0) {
        $average_days = round($total_days_completed / $count_completed, 1);
        $pdf->Cell(0, 6, sprintf('Promedio General de Duracion (Gestaciones Completadas): %s dias', $average_days), 0, 1, 'L');
    }
    
    // Add per-animal statistics
    $pdf->Ln(2);
    $pdf->Cell(0, 6, 'PROMEDIOS POR ANIMAL (Solo Gestaciones Completadas):', 0, 1, 'L');
    foreach ($tag_stats as $tagid => $stats) {
        if ($stats['count'] > 0) {
            $avg = round($stats['total_days'] / $stats['count'], 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, sprintf('Tag ID: %s - %s: %s dias (de %d gestaciones)', 
                $tagid, 
                $stats['nombre'],
                $avg,
                $stats['count']
            ), 0, 1, 'L');
        }
    }
    
    // Add explanatory note
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(2);
    $pdf->MultiCell(0, 5, 'Nota: La duracion se calcula como la diferencia en dias entre la fecha de confirmacion de gestacion y la fecha del parto. Para gestaciones en curso, se utiliza la fecha actual para calcular los dias transcurridos. Los promedios solo consideran las gestaciones completadas.', 0, 'L');
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de gestaciones', 0, 1);
    $pdf->Ln(2);
}

// Add Open Days Statistics
$pdf->AddPage();
$pdf->ChapterTitle('Dias Abiertos');

// SQL to calculate open days between birth and next pregnancy for all animals
$sql_open_days = "WITH OrderedEvents AS (
    SELECT 
        vh_parto_tagid as tagid,
        vh_parto_fecha as fecha,
        vh_parto_numero as numero,
        'Parto' as tipo
    FROM vh_parto
    
    UNION ALL
    
    SELECT 
        vh_gestacion_tagid as tagid,
        vh_gestacion_fecha as fecha,
        vh_gestacion_numero as numero,
        'Gestacion' as tipo
    FROM vh_gestacion
),
NextPregnancy AS (
    SELECT 
        e1.tagid,
        e1.fecha as fecha_parto,
        e1.numero as parto_numero,
        MIN(e2.fecha) as fecha_siguiente_gestacion,
        DATEDIFF(MIN(e2.fecha), e1.fecha) as dias_abiertos
    FROM OrderedEvents e1
    LEFT JOIN OrderedEvents e2 ON e1.tagid = e2.tagid 
        AND e2.tipo = 'Gestacion'
        AND e2.fecha > e1.fecha
    WHERE e1.tipo = 'Parto'
    GROUP BY e1.tagid, e1.fecha, e1.numero
)
SELECT 
    np.tagid,
    v.nombre,
    v.etapa,
    np.parto_numero,
    np.fecha_parto,
    np.fecha_siguiente_gestacion,
    CASE 
        WHEN np.fecha_siguiente_gestacion IS NOT NULL THEN np.dias_abiertos
        WHEN np.fecha_parto IS NOT NULL THEN DATEDIFF(CURDATE(), np.fecha_parto)
    END as dias_abiertos,
    CASE 
        WHEN np.fecha_siguiente_gestacion IS NOT NULL THEN 'Cerrado'
        WHEN np.fecha_parto IS NOT NULL THEN 'Abierto'
    END as estado
FROM NextPregnancy np
LEFT JOIN vacuno v ON np.tagid = v.tagid
WHERE v.genero = 'Hembra'
ORDER BY np.tagid, np.fecha_parto DESC";

$stmt_open_days = $conn->prepare($sql_open_days);
$stmt_open_days->execute();
$result_open_days = $stmt_open_days->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_open_days)) {
    $header = array('Tag ID', 'Nombre', 'Etapa', 'Parto Nro.', 'F. Parto', 'F. Nueva Gestacion', 'Dias Abiertos', 'Estado');
    $data = array();
    $total_days_closed = 0;
    $count_closed = 0;
    $tag_stats = array();
    
    foreach ($result_open_days as $row) {
        $siguiente_gestacion = $row['fecha_siguiente_gestacion'] ? $row['fecha_siguiente_gestacion'] : 'Pendiente';
        $data[] = array(
            $row['tagid'],
            $row['nombre'],
            $row['etapa'],
            $row['parto_numero'],
            $row['fecha_parto'],
            $siguiente_gestacion,
            $row['dias_abiertos'],
            $row['estado']
        );
        
        // Collect statistics per animal
        $tagid = $row['tagid'];
        if (!isset($tag_stats[$tagid])) {
            $tag_stats[$tagid] = array(
                'nombre' => $row['nombre'],
                'etapa' => $row['etapa'],
                'total_days' => 0,
                'count' => 0,
                'open_periods' => 0,
                'current_open_days' => null
            );
        }
        
        // Track statistics
        if ($row['estado'] === 'Cerrado') {
            $tag_stats[$tagid]['total_days'] += $row['dias_abiertos'];
            $tag_stats[$tagid]['count']++;
            $total_days_closed += $row['dias_abiertos'];
            $count_closed++;
        } else {
            $tag_stats[$tagid]['open_periods']++;
            if ($tag_stats[$tagid]['current_open_days'] === null || 
                $row['dias_abiertos'] > $tag_stats[$tagid]['current_open_days']) {
                $tag_stats[$tagid]['current_open_days'] = $row['dias_abiertos'];
            }
        }
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add overall statistics
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'ESTADISTICAS GENERALES:', 0, 1, 'L');
    
    if ($count_closed > 0) {
        $average_days = round($total_days_closed / $count_closed, 1);
        $pdf->Cell(0, 6, sprintf('Promedio General de Dias Abiertos (Periodos Cerrados): %s dias', $average_days), 0, 1, 'L');
    }
    
    // Add per-animal statistics
    $pdf->Ln(2);
    $pdf->Cell(0, 6, 'PROMEDIOS POR ANIMAL:', 0, 1, 'L');
    foreach ($tag_stats as $tagid => $stats) {
        $pdf->SetFont('Arial', '', 10);
        
        // Show average for closed periods if any
        if ($stats['count'] > 0) {
            $avg = round($stats['total_days'] / $stats['count'], 1);
            $pdf->Cell(0, 6, sprintf('Tag ID: %s - %s (%s)', $tagid, $stats['nombre'], $stats['etapa']), 0, 1, 'L');
            $pdf->Cell(0, 6, sprintf('   Promedio Periodos Cerrados: %s dias (de %d periodos)', 
                $avg, $stats['count']), 0, 1, 'L');
        }
        
        // Show current open period if any
        if ($stats['current_open_days'] !== null) {
            $pdf->Cell(0, 6, sprintf('   Periodo Abierto Actual: %d dias', 
                $stats['current_open_days']), 0, 1, 'L');
        }
        
        $pdf->Ln(1);
    }
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(2);
    $pdf->MultiCell(0, 5, 'Notas:
- Dias abiertos: Periodo entre un parto y la siguiente confirmacion de gestacion.
- Estado "Cerrado": El animal ya tiene confirmada la siguiente gestacion.
- Estado "Abierto": El animal aun no tiene confirmada la siguiente gestacion.
- Para periodos abiertos, se calcula usando la fecha actual.
- Los promedios de periodos cerrados solo consideran gestaciones confirmadas.
- Se muestran unicamente animales hembra con historial de partos.', 0, 'L');
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay registros de partos para calcular dias abiertos', 0, 1);
    $pdf->Ln(2);
}

// Add section for females with no pregnancy records
$pdf->AddPage();
$pdf->ChapterTitle('Descartes');

// SQL to find females with no pregnancy records
$sql_no_preg = "SELECT 
    v.tagid,
    v.nombre,
    v.fecha_nacimiento,
    TIMESTAMPDIFF(MONTH, v.fecha_nacimiento, CURDATE()) as edad_meses
FROM vacuno v
LEFT JOIN vh_gestacion g ON v.tagid = g.vh_gestacion_tagid
WHERE v.genero = 'Hembra' 
    AND g.vh_gestacion_tagid IS NULL
    AND v.estatus = 'Activo'
ORDER BY v.fecha_nacimiento ASC";

$stmt_no_preg = $conn->prepare($sql_no_preg);
$stmt_no_preg->execute();
$result_no_preg = $stmt_no_preg->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_no_preg)) {
    $header = array('Tag ID', 'Nombre', 'F. Nacimiento', 'Edad (Meses)');
    $data = array();
    $count_by_age = array(
        'menos_12' => 0,
        '12_24' => 0,
        'mas_24' => 0
    );
    
    foreach ($result_no_preg as $row) {
        $data[] = array(
            $row['tagid'],
            $row['nombre'],
            $row['fecha_nacimiento'],
            $row['edad_meses']
        );
        
        // Count animals by age range
        if ($row['edad_meses'] < 12) {
            $count_by_age['menos_12']++;
        } elseif ($row['edad_meses'] <= 24) {
            $count_by_age['12_24']++;
        } else {
            $count_by_age['mas_24']++;
        }
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add summary statistics
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'RESUMEN POR EDAD:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, sprintf('Menores de 12 meses: %d animales', $count_by_age['menos_12']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Entre 12 y 24 meses: %d animales', $count_by_age['12_24']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Mayores de 24 meses: %d animales', $count_by_age['mas_24']), 0, 1, 'L');
    
    // Add explanatory note
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(2);
    $pdf->MultiCell(0, 5, 'Nota: Esta lista muestra las hembras activas que no tienen ningun registro de gestacion en el sistema. Las edades se calculan en meses desde la fecha de nacimiento hasta la fecha actual. Animales mayores de 24 meses sin registro de gestacion podrian requerir atencion especial.', 0, 'L');
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'Todas las hembras activas tienen al menos un registro de gestacion', 0, 1);
    $pdf->Ln(2);
}

// Add section for animals with extended time since last birth
$pdf->AddPage();
$pdf->ChapterTitle('sin parir');

// SQL to find animals with more than 365 days since last birth
$sql_extended_period = "WITH LastBirth AS (
    SELECT 
        vh_parto_tagid,
        MAX(vh_parto_fecha) as vh_parto_fecha,
        COUNT(*) as total_partos
    FROM vh_parto
    GROUP BY vh_parto_tagid
)
SELECT 
    v.tagid,
    v.nombre,
    v.etapa,
    lb.vh_parto_fecha,
    DATEDIFF(CURDATE(), lb.vh_parto_fecha) as dias_desde_parto,
    lb.total_partos
FROM vacuno v
JOIN LastBirth lb ON v.tagid = lb.vh_parto_tagid
LEFT JOIN vh_parto p ON v.tagid = p.vh_parto_tagid 
    AND p.vh_parto_fecha > lb.vh_parto_fecha
WHERE v.genero = 'Hembra' 
    AND v.estatus = 'Activo'
    AND DATEDIFF(CURDATE(), lb.vh_parto_fecha) > 365
    AND p.vh_parto_tagid IS NULL
ORDER BY dias_desde_parto DESC";

$stmt_extended = $conn->prepare($sql_extended_period);
$stmt_extended->execute();
$result_extended = $stmt_extended->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result_extended)) {
    $header = array('Tag ID', 'Nombre', 'Etapa', 'Ultimo Parto', 'Dias Sin Parir', 'Total Partos');
    $data = array();
    
    // Statistics counters
    $count_by_days = array(
        '365_540' => 0,  // 1-1.5 years
        '541_730' => 0,  // 1.5-2 years
        'over_730' => 0  // over 2 years
    );
    
    foreach ($result_extended as $row) {
        $data[] = array(
            $row['tagid'],
            $row['nombre'],
            $row['etapa'],
            $row['vh_parto_fecha'],
            $row['dias_desde_parto'],
            $row['total_partos']
        );
        
        // Count by days range
        if ($row['dias_desde_parto'] <= 540) {
            $count_by_days['365_540']++;
        } elseif ($row['dias_desde_parto'] <= 730) {
            $count_by_days['541_730']++;
        } else {
            $count_by_days['over_730']++;
        }
    }
    
    $pdf->SimpleTable($header, $data);
    
    // Add statistics
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'RESUMEN POR PERIODO:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, sprintf('Entre 365 y 517 dias sin parir: %d animales', $count_by_days['365_540']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Entre 518 y 720 dias sin parir: %d animales', $count_by_days['541_730']), 0, 1, 'L');
    $pdf->Cell(0, 6, sprintf('Mas de 720 dias sin parir: %d animales', $count_by_days['over_730']), 0, 1, 'L');
    
    // Add explanatory notes
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Ln(2);
    $pdf->MultiCell(0, 5, 'Notas:
- Esta tabla muestra hembras activas con mas de 365 dias desde su ultimo parto.
- Solo se incluyen animales que no tienen una gestacion registrada despues de su ultimo parto.
- Los dias sin parir se calculan desde el ultimo parto hasta la fecha actual.
- Animales con mas de 540 dias sin parir requieren atencion especial.
- Considerar revision veterinaria para animales con periodos extendidos sin parir.', 0, 'L');
    $pdf->Ln(2);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'No hay animales con mas de 365 dias desde su ultimo parto sin nueva gestacion', 0, 1);
    $pdf->Ln(2);
}


// Function to generate podcast content
function generatePodcastContent($animal, $tagid, $conn) {
    try {
        $content = "Reporte Veterinario del Animal con Tag ID " . $animal['tagid'] . ".\n\n";
        
        // Basic animal information
        $content .= "Información General del Animal:\n";
        $content .= "Nombre: " . $animal['nombre'] . ".\n";
        $content .= "Fecha de Nacimiento: " . $animal['fecha_nacimiento'] . ".\n";
        $content .= "Género: " . $animal['genero'] . ".\n";
        $content .= "Raza: " . $animal['raza'] . ".\n";
        $content .= "Etapa: " . $animal['etapa'] . ".\n";
        $content .= "Grupo: " . $animal['grupo'] . ".\n";
        $content .= "Estatus: " . $animal['estatus'] . ".\n\n";
        
        // Get weight history
        $sql_weight = "SELECT vh_peso_fecha, vh_peso_animal FROM vh_peso WHERE vh_peso_tagid = ? ORDER BY vh_peso_fecha DESC LIMIT 5";
        $stmt_weight = $conn->prepare($sql_weight);
        $stmt_weight->execute([$tagid]);
        $weights = $stmt_weight->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($weights)) {
            $content .= "Historial de Pesos Recientes:\n";
            foreach ($weights as $weight) {
                $content .= "Fecha: " . $weight['vh_peso_fecha'] . ", Peso: " . $weight['vh_peso_animal'] . " kilogramos.\n";
            }
            $content .= "\n";
        }
        
        $content .= "Este reporte fue generado automáticamente por ANIMALIA de Registroca.\n";
        $content .= "Para información más detallada, consulte el reporte completo en formato PDF.\n";
        $content .= "Gracias por su atención.";
        
        return $content;
    } catch (Exception $e) {
        error_log('Error in generatePodcastContent: ' . $e->getMessage());
        return "Error generando contenido del podcast: " . $e->getMessage();
    }
}

// At the end of the file:
// Clean any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Sanitize animal name for filename (remove special characters and spaces)
$sanitized_name = preg_replace('/[^a-zA-Z0-9]/', '_', $animal['nombre']);
$sanitized_name = trim($sanitized_name, '_'); // Remove leading/trailing underscores

// Generate filename with timestamp to avoid conflicts
$filename = $sanitized_name . '_' . $tagid . '_' . date('Y-m-d_His') . '.pdf';
$filepath = __DIR__ . '/reports/' . $filename;

try {
    // Make sure reports directory exists
    $reportsDir = __DIR__ . '/reports';
    if (!file_exists($reportsDir)) {
        mkdir($reportsDir, 0777, true);
    }

    // Log the file path for debugging
    error_log("Attempting to generate PDF at: " . $filepath);
    error_log("Reports directory: " . $reportsDir);
    error_log("Directory exists: " . (file_exists($reportsDir) ? 'Yes' : 'No'));
    error_log("Directory writable: " . (is_writable($reportsDir) ? 'Yes' : 'No'));

    // First save the PDF to file
    $pdf->Output('F', $filepath);
    
    // Verify the file was created and is a PDF
    if (!file_exists($filepath)) {
        error_log("PDF file was not created at: " . $filepath);
        throw new Exception('Failed to create PDF file');
    }
    
    if (filesize($filepath) === 0) {
        error_log("PDF file is empty at: " . $filepath);
        unlink($filepath); // Delete empty file
        throw new Exception('Generated PDF file is empty');
    }
    
    // Log success
    error_log("PDF generated successfully: " . $filepath);
    error_log("File size: " . filesize($filepath) . " bytes");
    
    // Verify the file is readable
    if (!is_readable($filepath)) {
        error_log("Generated PDF file is not readable: " . $filepath);
        throw new Exception('Generated PDF file is not readable');
    }
    
    // Check if the share file exists
    $share_file = __DIR__ . '/vacuno_share.php';
    if (!file_exists($share_file)) {
        error_log("Share file not found: " . $share_file);
        throw new Exception('Share file not found');
    }
    
    // Check if this is an AJAX request (from JavaScript)
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        // Upload to ChatPDF if requested
        $uploadResult = ['success' => false, 'message' => 'ChatPDF upload not requested'];
        
        // Check if ChatPDF upload is requested (from the chat system)
        if (isset($_GET['upload_to_chatpdf']) && $_GET['upload_to_chatpdf'] == '1') {
            $uploadResult = uploadToChatPDF($filepath, $filename);
        }
        
        // Check if podcast generation is requested
        $podcastContent = null;
        if (isset($_GET['generate_podcast']) && $_GET['generate_podcast'] == '1') {
            try {
                $podcastContent = generatePodcastContent($animal, $tagid, $conn);
            } catch (Exception $e) {
                error_log('Error generating podcast content: ' . $e->getMessage());
                $podcastContent = "Error generando contenido del podcast: " . $e->getMessage();
            }
        }
        
        // Return JSON response for AJAX requests
        header('Content-Type: application/json');
        $response = [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'upload_result' => $uploadResult,
            'message' => 'PDF generated successfully'
        ];
        
        if ($podcastContent) {
            $response['podcast_content'] = $podcastContent;
        }
        
        echo json_encode($response);
        exit;
    } else {
        // Redirect for direct browser requests
        $redirect_url = 'vacuno_share.php?file=' . urlencode($filename) . '&tagid=' . urlencode($tagid);
        error_log("Redirecting to: " . $redirect_url);
        
        // Ensure no output has been sent
        if (headers_sent()) {
            error_log("Headers already sent, cannot redirect");
            throw new Exception('Headers already sent, cannot redirect');
        }
        
        header('Location: ' . $redirect_url);
        exit;
    }
} catch (Exception $e) {
    // Log error
    error_log('PDF Generation Error: ' . $e->getMessage());
    error_log('Error occurred at: ' . $e->getFile() . ':' . $e->getLine());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    if (file_exists($filepath)) {
        error_log("Cleaning up failed file: " . $filepath);
        unlink($filepath); // Clean up any failed file
    }
    
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error generating PDF: ' . $e->getMessage() . '. Please try again.']);
        exit;
    } else {
        die('Error generating PDF: ' . $e->getMessage() . '. Please try again.');
    }
}
