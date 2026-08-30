<?php
require_once 'config.php';
$sql = "ALTER TABLE proposal_otomatis 
    ADD COLUMN IF NOT EXISTS ttd_1_file VARCHAR(255),
    ADD COLUMN IF NOT EXISTS ttd_2_file VARCHAR(255),
    ADD COLUMN IF NOT EXISTS ttd_3_file VARCHAR(255)";
if ($conn->query($sql)) {
    echo "Columns added to proposal_otomatis for custom TTD files.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
