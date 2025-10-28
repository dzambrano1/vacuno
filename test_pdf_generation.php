<?php
/**
 * Test script for generateAndUploadPDF function
 */

require_once './generateAndUploadPDF.php';

// Test data
$testTagId = '12345'; // Replace with an actual tag ID from your database
$reportType = 'complete';
$options = [
    'cleanup' => false // Keep the file for inspection
];

echo "<h2>Testing generateAndUploadPDF Function</h2>";
echo "<p>Tag ID: {$testTagId}</p>";
echo "<p>Report Type: {$reportType}</p>";
echo "<hr>";

// Call the function
$result = generateAndUploadPDF($testTagId, $reportType, $options);

// Display results
echo "<h3>Result:</h3>";
echo "<pre>";
print_r($result);
echo "</pre>";

if ($result['success']) {
    echo "<h3>Success!</h3>";
    echo "<p>PDF generated: " . $result['filename'] . "</p>";
    echo "<p>File path: " . $result['filepath'] . "</p>";
    
    if (isset($result['upload_result']) && $result['upload_result']['success']) {
        echo "<p>ChatPDF Upload: Success</p>";
        echo "<p>Source ID: " . ($result['upload_result']['sourceId'] ?? 'N/A') . "</p>";
    } else {
        echo "<p>ChatPDF Upload: Failed</p>";
        echo "<p>Error: " . ($result['upload_result']['error'] ?? 'Unknown error') . "</p>";
    }
} else {
    echo "<h3>Error!</h3>";
    echo "<p>Error: " . $result['error'] . "</p>";
    echo "<p>Message: " . $result['message'] . "</p>";
}
?>
