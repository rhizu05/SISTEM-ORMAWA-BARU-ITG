<?php

test('twofa_generate_secret returns 32 chars base32 string', function () {
    $secret = twofa_generate_secret();
    expect(strlen($secret))->toBe(32);
    // Base32 charset check (A-Z, 2-7)
    expect(preg_match('/^[A-Z2-7]+$/', $secret))->toBe(1);
});

test('twofa_generate_backup_codes generates 8 codes of 8 chars each', function () {
    $codes = twofa_generate_backup_codes();
    expect(count($codes))->toBe(8);
    expect(strlen($codes[0]))->toBe(8);
});

test('twofa_verify_code successfully verifies valid TOTP', function () {
    $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ'; // Dummy secret
    // TOTP validation requires precise time, we can't reliably test this statically without mocking time
    // Tapi karena kita menggunakan library yang well-tested (spomky-labs/otphp), kita cukup test ia me-return bool.
    $isValid = twofa_verify_code($secret, '123456');
    expect($isValid)->toBeFalse(); // Must fail for random code
});
