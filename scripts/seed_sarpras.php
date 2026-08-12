<?php
require_once __DIR__ . '/../config.php';

$users_to_seed = [
    [
        'username'    => 'sarpras_ruangan',
        'password'    => password_hash('sarpras123', PASSWORD_DEFAULT),
        'role'        => 'sarpras',
        'nama_lengkap' => 'Bagian Sarpras Ruangan',
        'label'       => 'sarpras_ruangan (pass: sarpras123)',
    ],
    [
        'username'    => 'sarpras_barang',
        'password'    => password_hash('barang123', PASSWORD_DEFAULT),
        'role'        => 'sarpras_barang',
        'nama_lengkap' => 'Bagian Sarpras Barang',
        'label'       => 'sarpras_barang (pass: barang123)',
    ],
];

foreach ($users_to_seed as $u) {
    $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $check->bind_param('s', $u['username']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $u['username'], $u['password'], $u['role'], $u['nama_lengkap']);
        if ($stmt->execute()) {
            echo "  + User " . $u['label'] . " dibuat.\n";
        } else {
            echo "  ! Gagal membuat " . $u['username'] . ": " . $conn->error . "\n";
        }
        $stmt->close();
    } else {
        echo "  - User " . $u['username'] . " sudah ada, skip.\n";
    }
    $check->close();
}

echo "Selesai.\n";
$conn->close();
?>
