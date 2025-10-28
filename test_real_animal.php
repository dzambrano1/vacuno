<?php
// Test script to check AJAX response from vacuno_report.php with real animal
$tagid = '9500'; // Real animal tagid from the reports directory

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/vacuno/vacuno_report.php?tagid=" . urlencode($tagid));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Requested-With: XMLHttpRequest'
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "cURL Error: " . $error . "\n";
}

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

// Try to decode JSON
$jsonData = json_decode($response, true);
if ($jsonData === null) {
    echo "JSON decode error: " . json_last_error_msg() . "\n";
} else {
    echo "JSON decoded successfully:\n";
    print_r($jsonData);
}
?>
