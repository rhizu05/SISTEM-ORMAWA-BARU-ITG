<?php
require_once __DIR__ . '/../config.php';
$res = $conn->query("DESCRIBE users");
while($row = $res->fetch_assoc()){
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$conn->close();
?>
