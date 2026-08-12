<?php
require_once __DIR__ . '/../config.php';
$r = $conn->query("SELECT username, role FROM users WHERE username = 'sarpras_ruangan'");
print_r($r->fetch_assoc());
?>
