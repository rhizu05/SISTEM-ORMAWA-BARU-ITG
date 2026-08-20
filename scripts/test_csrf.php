<?php
/**
 * Test Script untuk CSRF Token Extraction dan Testing
 */

$html = file_get_contents('http://localhost/sistem_keuangan/index.php?page=login');

// Extract CSRF token dari form
preg_match('/name="csrf_token" value="([^"]+)"/', $html, $matches);

if (isset($matches[1])) {
    $csrf_token = $matches[1];
    echo "Extracted CSRF Token: " . $csrf_token . "\n\n";
    
    // Test POST dengan valid CSRF token
    $url = 'http://localhost/sistem_keuangan/index.php?page=login';
    $data = http_build_query([
        'username' => 'test',
        'password' => 'test',
        'csrf_token' => $csrf_token
    ]);
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    // Cek response
    if ($result === FALSE) {
        echo "POST request failed\n";
    } else {
        // Cek jika berhasil atau ada error message
        if (strpos($result, 'Username atau password salah') !== false) {
            echo "✓ CSRF VALIDATION PASSED: Form submission dengan token valid diterima\n";
            echo "  (Login failed karena credentials salah, tapi CSRF check passed)\n";
        } elseif (strpos($result, 'CSRF token tidak valid') !== false) {
            echo "✗ CSRF VALIDATION FAILED: Token dianggap tidak valid\n";
        } else {
            echo "Response: " . substr($result, 0, 200) . "...\n";
        }
    }
} else {
    echo "CSRF token not found in form\n";
}

// Test untuk window.CSRF_TOKEN di JavaScript
if (preg_match('/window\.CSRF_TOKEN\s*=\s*"([^"]+)"/', $html, $js_matches)) {
    echo "\n✓ JavaScript CSRF Token ditemukan: " . substr($js_matches[1], 0, 20) . "...\n";
} else {
    echo "\n✗ JavaScript CSRF Token tidak ditemukan\n";
}
?>