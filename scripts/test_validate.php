<?php
/**
 * Test script untuk validate_uploaded_file function
 */

define('APP_RUNNING', true);
require_once 'app/helpers/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== TEST validate_uploaded_file ===\n\n";

// Test 1: Valid PDF (mimes)
echo "Test 1: Valid PDF simulation\n";
// Create a temp file with PDF content
$tempFile = tempnam(sys_get_temp_dir(), 'pdf_test');
file_put_contents($tempFile, '%PDF-1.4 test content');

$result = validate_uploaded_file(
    ['name' => 'test.pdf', 'type' => 'application/pdf', 'size' => 1000, 'tmp_name' => $tempFile],
    ['application/pdf'],
    5,
    'proposal_'
);

if ($result !== false) {
    echo "  ✓ Valid PDF accepted\n";
    echo "  Safe name: " . $result['safe_name'] . "\n";
    echo "  Extension: " . $result['extension'] . "\n";
    echo "  MIME: " . $result['mime'] . "\n";
} else {
    echo "  ✗ Valid PDF rejected\n";
}

// Test 2: Invalid file type
echo "\nTest 2: Invalid file type (exe)\n";
$tempFile2 = tempnam(sys_get_temp_dir(), 'exe_test');
file_put_contents($tempFile2, 'MZ\x90\x00 test executable');

$result2 = validate_uploaded_file(
    ['name' => 'malware.exe', 'type' => 'application/x-exe', 'size' => 1000, 'tmp_name' => $tempFile2],
    ['application/pdf'],
    5,
    'proposal_'
);

if ($result2 === false) {
    echo "  ✓ Invalid type rejected\n";
} else {
    echo "  ✗ Invalid type accepted (BUG)\n";
}

// Test 3: Oversized file
echo "\nTest 3: Oversized file\n";
$tempFile3 = tempnam(sys_get_temp_dir(), 'big_test');
$largeContent = str_repeat('A', 6 * 1024 * 1024 + 1); // 6MB + 1 byte
file_put_contents($tempFile3, $largeContent);

$result3 = validate_uploaded_file(
    ['name' => 'big.pdf', 'type' => 'application/pdf', 'size' => filesize($tempFile3), 'tmp_name' => $tempFile3],
    ['application/pdf'],
    5, // 5MB max
    'proposal_'
);

if ($result3 === false) {
    echo "  ✓ Oversized rejected\n";
} else {
    echo "  ✗ Oversized accepted (BUG)\n";
}

// Test 4: Upload error
echo "\nTest 4: Upload error simulation\n";
// File dengan error UPLOAD_ERR_NO_FILE = 4
$result4 = validate_uploaded_file(
    ['name' => '', 'type' => '', 'size' => 0, 'tmp_name' => '', 'error' => 4],
    ['application/pdf'],
    5,
    'proposal_'
);

if ($result4 === false) {
    echo "  ✓ Upload error handled\n";
} else {
    echo "  ✗ Upload error not handled\n";
}

// Cleanup
unlink($tempFile);
unlink($tempFile2);
unlink($tempFile3);

echo "\n=== TEST SELESAI ===\n";
?>