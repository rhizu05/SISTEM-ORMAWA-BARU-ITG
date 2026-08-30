<?php
require_once 'config.php';
$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS logo_ormawa VARCHAR(255)";
if ($conn->query($sql)) {
    echo "Column logo_ormawa added.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
