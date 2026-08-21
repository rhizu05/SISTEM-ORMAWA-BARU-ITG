<?php
if (!defined('APP_RUNNING')) {
    define('APP_RUNNING', true);
}
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/app/helpers/functions.php';
require ROOT_PATH . '/app/helpers/session.php';
require ROOT_PATH . '/app/helpers/twofa.php';

// Konfigurasi db mock untuk testing fungsi
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
