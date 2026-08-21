<?php
/**
 * PHPStan Bootstrap
 * Dieksekusi sebelum PHPStan menganalisis struktur class dan function
 */

define('APP_RUNNING', true);
define('ROOT_PATH', dirname(__DIR__));

// Mock database connection global agar tidak error saat file di-include
class MockMySQLiResult {
    public $num_rows = 1;
    public function fetch_assoc() { return []; }
    public function fetch_all() { return []; }
}

class MockMySQLiStmt {
    public function bind_param() { return true; }
    public function execute() { return true; }
    public function get_result() { return new MockMySQLiResult(); }
    public function close() { return true; }
}

class MockMySQLi {
    public $connect_error = null;
    public $error = '';
    public function query() { return new MockMySQLiResult(); }
    public function prepare() { return new MockMySQLiStmt(); }
    public function real_escape_string($str) { return $str; }
}

// Set $conn ke global scope
$conn = new MockMySQLi();
$GLOBALS['conn'] = $conn;

// Mock $_SESSION
$_SESSION = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
