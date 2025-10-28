<?php
require_once './pdo_conexion.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if file is provided
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die('Error: No file specified');
}

// Sanitize input and prevent directory traversal
$filename = basename($_GET['file']); // Only get the filename, no path
$filepath = __DIR__ . '/reports/' . $filename;

// Debug information
error_log("Attempting to download file: " . $filepath);

// Basic security check - only allow PDF files
if (!preg_match('/^[a-zA-Z0-9_-]+\.pdf$/', $filename)) {
    die('Error: Invalid filename format');
}

// Verify file exists
if (!file_exists($filepath)) {
    error_log("File not found: " . $filepath);
    die('Error: File not found - ' . htmlspecialchars($filepath));
}

// Verify file is readable
if (!is_readable($filepath)) {
    error_log("File not readable: " . $filepath);
    die('Error: File not readable');
}

// Get file size
$filesize = filesize($filepath);
if ($filesize === false || $filesize === 0) {
    error_log("Invalid file size for: " . $filepath . " Size: " . ($filesize === false ? 'false' : '0'));
    die('Error: Invalid file size');
}

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Read file in binary mode
if ($fileContents = file_get_contents($filepath)) {
    echo $fileContents;
} else {
    error_log("Failed to read file: " . $filepath);
    die('Error: Failed to read file');
}
exit; 