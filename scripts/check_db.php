<?php
require_once __DIR__ . '/../config.php';

$stmt = $conn->prepare("SELECT id_pengajuan, nama_kegiatan, status, nomor_surat FROM pengajuan WHERE id_user_ormawa = 8");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id_pengajuan'] . " | Nama: " . $row['nama_kegiatan'] . " | Status: " . $row['status'] . " | No Surat: " . $row['nomor_surat'] . "\n";
}
$stmt->close();
$conn->close();
?>
