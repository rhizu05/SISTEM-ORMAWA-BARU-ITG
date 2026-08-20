<?php
/**
 * Simple test untuk melihat error
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test basic CSRF functionality
require_once 'app/helpers/functions.php';

// Initialize session untuk testing
session_start();

// Generate token
$token1 = csrf_token();
echo "Generated Token 1: " . substr($token1, 0, 20) . "...\n";

// Verify token
$_POST['csrf_token'] = $token1;
echo "Testing csrf_verify()...\n";

try {
    csrf_verify();
    echo "✓ csrf_verify() passed with valid token\n";
} catch (Exception $e) {
    echo "✗ csrf_verify() failed: " . $e->getMessage() . "\n";
}

// Test dengan invalid token
$_POST['csrf_token'] = 'invalid_token';
echo "\nTesting dengan invalid token...\n";

try {
    csrf_verify();
    echo "✗ csrf_verify() should have failed but didn't\n";
} catch (Exception $e) {
    echo "✓ csrf_verify() correctly rejected invalid token\n";
}

// Test csrf_field() function
echo "\nGenerated csrf_field(): " . csrf_field() . "\n";

// Check if functions exist
echo "\nFunction Check:\n";
echo "csrf_token() exists: " . (function_exists('csrf_token') ? '✓' : '✗') . "\n";
echo "csrf_verify() exists: " . (function_exists('csrf_verify') ? '✓' : '✗') . "\n";
echo "csrf_field() exists: " . (function_exists('csrf_field') ? '✓' : '✗') . "\n";
?>