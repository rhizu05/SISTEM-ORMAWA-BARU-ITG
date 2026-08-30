<?php
require_once 'config.php';

$stmt = $conn->prepare("UPDATE pengajuan SET status = 'Diajukan ke Bendahara', nomor_surat = '123/TEST/BKKH/2026' WHERE id_user_ormawa = 8 AND status = 'Disetujui WR3, Siap Diajukan ke Bendahara'");
if ($stmt->execute()) {
    echo "Berhasil memajukan ke Bendahara.";
} else {
    echo "Error: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
