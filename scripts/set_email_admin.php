<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
$conn->query("UPDATE users SET email='admin@yopmail.com' WHERE username='admin'");
echo "Selesai.";