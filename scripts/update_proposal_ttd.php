<?php
require_once __DIR__ . '/../config.php';
$sql = "ALTER TABLE proposal_otomatis 
    ADD COLUMN IF NOT EXISTS ttd_1_key VARCHAR(20) DEFAULT 'ketua',
    ADD COLUMN IF NOT EXISTS ttd_2_key VARCHAR(20) DEFAULT 'sekretaris',
    ADD COLUMN IF NOT EXISTS ttd_3_key VARCHAR(20) DEFAULT 'ketua'";
if ($conn->query($sql)) {
    echo "Columns added to proposal_otomatis.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
