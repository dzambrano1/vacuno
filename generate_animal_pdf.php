<?php
require('fpdf/fpdf.php');
require_once './pdo_conexion.php';

// Validate input
if (!isset($_GET['tagid']) || empty($_GET['tagid'])) {
    http_response_code(400);
    die('Error: No animal ID provided');
}

$tagid = $_GET['tagid'];

try {
    // Create database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get animal information
    $stmt = $conn->prepare("SELECT * FROM vacuno WHERE tagid = ?");
    $stmt->execute([$tagid]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$animal) {
        http_response_code(404);
        die('Error: Animal not found');
    }
    
    // Get additional information (you can add more queries as needed)
    $stmt = $conn->prepare("SELECT * FROM vacuno_historial WHERE tagid = ? ORDER BY fecha DESC");
    $stmt->execute([$tagid]);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create PDF
    class AnimalPDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'Reporte de Animal', 0, 1, 'C');
            $this->Ln(10);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }
    
    $pdf = new AnimalPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Basic Information
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Información Básica', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(50, 8, 'ID:', 0);
    $pdf->Cell(0, 8, $animal['tagid'], 0, 1);
    
    $pdf->Cell(50, 8, 'Nombre:', 0);
    $pdf->Cell(0, 8, $animal['nombre'], 0, 1);
    
    $pdf->Cell(50, 8, 'Raza:', 0);
    $pdf->Cell(0, 8, $animal['raza'], 0, 1);
    
    $pdf->Cell(50, 8, 'Fecha de Nacimiento:', 0);
    $pdf->Cell(0, 8, $animal['fecha_nacimiento'], 0, 1);
    
    $pdf->Cell(50, 8, 'Peso:', 0);
    $pdf->Cell(0, 8, $animal['peso'] . ' kg', 0, 1);
    
    $pdf->Cell(50, 8, 'Estado:', 0);
    $pdf->Cell(0, 8, $animal['estado'], 0, 1);
    
    // Historial
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Historial', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    if (!empty($historial)) {
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(40, 8, 'Fecha', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Tipo', 1, 0, 'C', true);
        $pdf->Cell(100, 8, 'Descripción', 1, 1, 'C', true);
        
        foreach ($historial as $registro) {
            $pdf->Cell(40, 8, $registro['fecha'], 1);
            $pdf->Cell(50, 8, $registro['tipo'], 1);
            $pdf->Cell(100, 8, $registro['descripcion'], 1, 1);
        }
    } else {
        $pdf->Cell(0, 8, 'No hay registros en el historial', 0, 1);
    }
    
    // Additional Information
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Información Adicional', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    $pdf->Cell(50, 8, 'Género:', 0);
    $pdf->Cell(0, 8, $animal['genero'], 0, 1);
    
    $pdf->Cell(50, 8, 'Color:', 0);
    $pdf->Cell(0, 8, $animal['color'], 0, 1);
    
    if (!empty($animal['observaciones'])) {
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Observaciones:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 8, $animal['observaciones'], 0, 'L');
    }
    
    // Output the PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="animal_' . $tagid . '.pdf"');
    $pdf->Output('I', 'animal_' . $tagid . '.pdf');
    
} catch(PDOException $e) {
    http_response_code(500);
    die("Error: " . $e->getMessage());
} 