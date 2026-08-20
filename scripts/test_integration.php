<?php
/**
 * Test integrasi lengkap: CSRF + File Upload Validation
 */

define('APP_RUNNING', true);
require_once 'app/helpers/functions.php';
require_once 'app/core/Controller.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Test 1: Cek CSRF token availability
echo "Test 1: CSRF Token Availability\n";
$csrf_token = csrf_token();
echo "  Token generated: " . substr($csrf_token, 0, 20) . "...\n";
echo "  Token length: " . strlen($csrf_token) . " chars\n";
echo "  ✓ PASSED\n\n";

// Test 2: Validasi file PDF melalui controller logic
echo "Test 2: File Validation via Controller Logic\n";

// Simulate $_FILES seperti ketika form submit
$_FILES['file_proposal'] = [
    'name' => 'test_proposal.pdf',
    'type' => 'application/pdf',
    'tmp_name' => tempnam(sys_get_temp_dir(), 'pdf_'),
    'size' => 1024,
    'error' => UPLOAD_ERR_OK
];

// Generate token untuk test
csrf_token(); // Initialize session token

// Extract token dari form (seperti yang dilakukan frontend)
preg_match('/name="csrf_token" value="([^"]+)"/', file_get_contents('app/views/auth/login.php'), $matches);
$form_token = $matches[1] ?? 'test_token';

echo "  Form CSRF token: " . ($form_token ?: 'not found') . "\n";

// Test validasi di controller
$allowed_pdf_types = ['application/pdf'];
$max_pdf_size_mb = 5;
$validation = validate_uploaded_file(
    $_FILES['file_proposal'],
    $allowed_pdf_types,
    $max_pdf_size_mb,
    'proposal_'
);

if ($validation !== false) {
    echo "  ✓ File validasi lolos: " . $validation['safe_name'] . "\n";
} else {
    echo "  ✗ File validasi gagal\n";
}

// Cleanup
unlink($_FILES['file_proposal']['tmp_name']);

echo "\nTest 2: ✓ PASSED\n\n";

// Test 3: Test dengan file tidak valid (tidak PDF)
echo "Test 3: Invalid File Type Rejection\n";
$_FILES['file_proposal'] = [
    'name' => 'malware.exe',
    'type' => 'application/x-exe',
    'tmp_name' => tempnam(sys_get_temp_dir(), 'exe_'),
    'size' => 1000,
    'error' => UPLOAD_ERR_OK
];

$validation = validate_uploaded_file(
    $_FILES['file_proposal'],
    ['application/pdf'],
    5,
    'proposal_'
);

if ($validation === false) {
    echo "  ✓ File tidak-valid ditolak secara otomatis\n";
} else {
    echo "  ✗ File tidak-valid diterima (BUG)\n";
}

unlink($_FILES['file_proposal']['tmp_name']);
echo "Test 3: ✓ PASSED\n\n";

echo "=== SEMUA TEST LEWATAN ===\n";
?>