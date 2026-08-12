<?php
require_once __DIR__ . '/../config.php';
$sql = "ALTER TABLE surat_otomatis MODIFY COLUMN jenis_surat ENUM('Undangan','Tugas','Permohonan','Keterangan','Peringatan')";
if ($conn->query($sql)) {
    echo "Enum updated successfully.";
} else {
    echo "Error updating enum: " . $conn->error;
}
?>
