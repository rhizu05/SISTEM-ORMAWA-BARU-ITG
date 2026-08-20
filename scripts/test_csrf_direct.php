<?php
/**
 * Direct CSRF functions test
 */

define('APP_RUNNING', true);

// Include functions directly
require_once 'app/helpers/functions.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== CSRF FUNCTIONS TEST ===\n\n";

// Test 1: Generate token
$token1 = csrf_token();
echo "1. Generated Token: " . substr($token1, 0, 20) . "...\n";
echo "   Token Length: " . strlen($token1) . " chars\n";
echo "   Session Token: " . (isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 20) . "..." : "NOT SET") . "\n\n";

// Test 2: Generate another token (should be same)
$token2 = csrf_token();
echo "2. Second Token Call: " . substr($token2, 0, 20) . "...\n";
echo "   Tokens Match: " . ($token1 === $token2 ? "✓ YES" : "✗ NO") . "\n\n";

// Test 3: csrf_field() output
echo "3. csrf_field() Output:\n";
echo "   " . htmlspecialchars(csrf_field()) . "\n\n";

// Test 4: Manual verification
$_POST['csrf_token'] = $token1;
echo "4. Manual Verification Test:\n";

// Capture output buffer to prevent die() from stopping script
ob_start();
$old_response_code = http_response_code();

try {
    csrf_verify();
    $output = ob_get_clean();
    echo "   ✓ csrf_verify() passed\n";
    echo "   Output: " . ($output ?: "(no output)") . "\n";
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "   ✗ Exception: " . $e->getMessage() . "\n";
    echo "   Output: " . ($output ?: "(no output)") . "\n";
}

// Restore response code
if (function_exists('http_response_code')) {
    http_response_code($old_response_code);
}

// Test 5: Invalid token
ob_start();
$_POST['csrf_token'] = 'invalid_token_here';
echo "\n5. Invalid Token Test:\n";
csrf_verify();
$output = ob_get_clean();
echo "   Output: " . ($output ?: "(no output/died)") . "\n";

echo "\n=== TEST COMPLETE ===\n";
?>