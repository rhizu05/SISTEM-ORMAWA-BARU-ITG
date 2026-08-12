<?php
require_once __DIR__ . '/../config.php';
$res = $conn->query("SHOW COLUMNS FROM pengajuan");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
