<?php
$ch = curl_init('http://localhost/sistem_keuangan/index.php?page=login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
echo "Headers:\n";
echo substr($response, 0, strpos($response, "\r\n\r\n"));
