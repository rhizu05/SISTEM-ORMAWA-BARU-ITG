<?php
$ch = curl_init('http://localhost/sistem_keuangan/index.php?page=login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => 'admin', 'password' => 'admin']));
$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http\n";
echo "Response Body:\n" . strip_tags($response) . "\n";
