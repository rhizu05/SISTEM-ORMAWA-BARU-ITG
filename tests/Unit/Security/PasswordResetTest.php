<?php
require_once ROOT_PATH . '/config.php';
require_once ROOT_PATH . '/app/helpers/mailer.php';

test('create_password_reset_token menghasilkan 64 karakter token plain', function () {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    $token = create_password_reset_token($conn, 1, 30);
    
    // bin2hex(random_bytes(32)) akan menghasilkan 64 karakter
    expect(strlen($token))->toBe(64);
    
    // Token hanya mengandung karakter hex (a-f, 0-9)
    expect(preg_match('/^[a-f0-9]+$/', $token))->toBe(1);
});

test('send_email mock mode berfungsi dan menulis log', function() {
    $to = 'test@example.com';
    $name = 'Test User';
    $subject = 'Test Subject';
    $body = "<a href='http://localhost/reset?token=12345'>Reset</a>";
    
    $result = send_email($to, $name, $subject, $body);
    
    expect($result)->toBeTrue();
    
    $log_path = dirname(ROOT_PATH) . '/storage/logs/email_mock.log';
    if(file_exists($log_path)) {
        $log_content = file_get_contents($log_path);
        expect(strpos($log_content, $to) !== false)->toBeTrue();
        expect(strpos($log_content, 'http://localhost/reset?token=12345') !== false)->toBeTrue();
    }
});
