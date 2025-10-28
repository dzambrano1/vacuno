<?php
// Simple PDF viewer script
$filename = $_GET['file'] ?? '';

if (empty($filename)) {
    http_response_code(400);
    echo "No file specified";
    exit;
}

// Security: Only allow PDF files and prevent directory traversal
if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.pdf$/', $filename)) {
    http_response_code(400);
    echo "Invalid filename";
    exit;
}

$filepath = __DIR__ . '/reports/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    echo "File not found: " . htmlspecialchars($filename);
    exit;
}

// Set proper headers for PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the file
readfile($filepath);
?>
