<?php
/**
 * Test multiple forms for CSRF protection
 */

define('APP_RUNNING', true);
require_once 'app/helpers/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current CSRF token
$csrf_token = csrf_token();

echo "=== FORM CSRF PROTECTION TEST ===\n\n";

$forms_to_test = [
    [
        'name' => 'Login Form',
        'url' => 'index.php?page=login',
        'data' => ['username' => 'test', 'password' => 'test']
    ],
    [
        'name' => 'Add User Form (Admin)',
        'url' => 'index.php?page=tambah_user',
        'data' => [
            'nama' => 'Test User',
            'username' => 'testuser',
            'password' => 'testpass',
            'role' => 'ormawa'
        ]
    ],
    [
        'name' => 'Profile Update Form',
        'url' => 'index.php?page=profil',
        'data' => ['nama' => 'Updated Name']
    ]
];

foreach ($forms_to_test as $form) {
    echo "Testing: " . $form['name'] . "\n";
    echo "URL: " . $form['url'] . "\n";
    
    // Test 1: Without CSRF token
    $data_without_token = $form['data'];
    echo "  - Without CSRF Token: ";
    
    // Simulate POST via file_get_contents
    $post_data = http_build_query($data_without_token);
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $post_data
        ]
    ]);
    
    $url = 'http://localhost/sistem_keuangan/' . $form['url'];
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        echo "HTTP Error\n";
    } elseif (strpos($response, 'CSRF token tidak valid') !== false) {
        echo "✓ CSRF Protection Active (blocked)\n";
    } else {
        echo "✗ CSRF Not Blocked\n";
    }
    
    // Test 2: With CSRF token
    $data_with_token = array_merge($form['data'], ['csrf_token' => $csrf_token]);
    echo "  - With CSRF Token: ";
    
    $post_data = http_build_query($data_with_token);
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $post_data
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        echo "HTTP Error\n";
    } elseif (strpos($response, 'CSRF token tidak valid') === false) {
        echo "✓ CSRF Token Accepted\n";
    } else {
        echo "✗ CSRF Still Blocked (might be other validation errors)\n";
    }
    
    echo "\n";
}

echo "=== BROWSER TEST CHECKLIST ===\n";
echo "Untuk test lengkap, buka browser dan test:\n";
echo "1. http://localhost/sistem_keuangan/index.php?page=login\n";
echo "   - Submit form tanpa token → Error 419\n";
echo "   - Submit dengan token → Processed (login failed credentials)\n";
echo "\n";
echo "2. Check JavaScript CSRF token:\n";
echo "   - Buka DevTools Console\n";
echo "   - Ketik: console.log(window.CSRF_TOKEN)\n";
echo "   - Harusnya tampilkan token string\n";
echo "\n";
echo "3. Check AJAX requests:\n";
echo "   - Buka Network tab\n";
echo "   - Trigger notification read\n";
echo "   - Check headers for X-CSRF-Token\n";
?>