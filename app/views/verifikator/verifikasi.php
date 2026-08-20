<?php
/**
 * File: roles/verifikator/verifikasi.php
 * Deskripsi: Halaman untuk verifikator (BEM, BPM, BKKH, WR3) memverifikasi proposal pengajuan.
 */

// Memeriksa peran pengguna yang diizinkan mengakses halaman ini
check_role(['bem', 'bpm', 'bkh', 'wr3', 'admin']);

// Mengambil ID pengajuan dari URL dan memastikan valid
$id_pengajuan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_pengajuan <= 0) {
    // Jika ID tidak valid, redirect ke dashboard
    redirect('index.php?page=dashboard&error=invalid_id');
}

// Mengambil data pengguna yang sedang login
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Logika POST verifikasi (setujui/tolak) dipindah ke VerifikasiController::verifikasiProposal
// (route ?page=verifikasi). View ini hanya menampilkan detail + form.

// 2. Mengambil data pengajuan untuk ditampilkan di halaman (GET Request)
$stmt_detail = $conn->prepare(
    "SELECT p.*, u.nama_lengkap AS nama_ormawa
     FROM pengajuan p
     JOIN users u ON p.id_user_ormawa = u.id_user
     WHERE p.id_pengajuan = ?"
);
if ($stmt_detail === false) {
    die("Gagal menyiapkan statement: " . $conn->error); // Tampilkan error jika prepare gagal
}
$stmt_detail->bind_param("i", $id_pengajuan);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$pengajuan = $result_detail->fetch_assoc();
$stmt_detail->close();

// Jika pengajuan tidak ditemukan
if (!$pengajuan) {
    echo "<div class='container-fluid px-4'><div class='alert alert-danger mt-4'>Pengajuan tidak ditemukan.</div></div>";
    return; // Hentikan eksekusi skrip
}

// Cek Otorisasi: Pastikan peran pengguna cocok dengan status pengajuan saat ini
$status_saat_ini = trim(strtolower($pengajuan['status'])); // Ambil status saat ini dan jadikan lowercase
$status_yang_diharapkan = '';

switch ($user_role) {
    case 'bem': $status_yang_diharapkan = 'diajukan ke bem'; break;
    case 'bpm': $status_yang_diharapkan = 'diajukan ke bpm'; break;
    case 'bkh': $status_yang_diharapkan = 'verifikasi bkkh'; break;
    case 'wr3': $status_yang_diharapkan = 'verifikasi wr3'; break;
}

// Jika status saat ini tidak cocok dengan yang diharapkan untuk peran ini
if ($status_saat_ini !== $status_yang_diharapkan) {
    echo "<div class='container-fluid px-4'><div class='alert alert-warning mt-4'>Anda tidak memiliki izin untuk memproses proposal ini pada tahap ini, atau proposal sudah diproses. Status saat ini: <strong>" . htmlspecialchars($pengajuan['status']) . "</strong></div></div>";
    return; // Hentikan eksekusi skrip
}

// Mendapatkan nama file proposal saja (tanpa path)
$nama_file_proposal = basename($pengajuan['file_proposal'] ?? '');
$path_proposal = 'uploads/proposal/' . $nama_file_proposal; // Bentuk path lengkap
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Verifikasi Proposal Pengajuan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Verifikasi Proposal</li>
    </ol>

    <?php
    // Menampilkan pesan error jika ada dari redirect sebelumnya
    if (isset($_GET['error'])) {
        $error_map = [
            'aksi_invalid' => 'Aksi yang dikirim tidak valid.',
            'catatan_kosong' => 'Catatan wajib diisi jika Anda menolak proposal.',
            'status_error' => 'Gagal menentukan status proposal selanjutnya.',
            'db_prepare_gagal' => 'Gagal menyiapkan perintah database.',
            'update_gagal' => 'Gagal memperbarui status proposal di database.',
        ];
        $error_message = $error_map[$_GET['error']] ?? 'Terjadi kesalahan tidak diketahui.';
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> ' . htmlspecialchars($error_message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
    ?>

    <div class="row">
        <!-- Kolom Kiri: Detail Pengajuan -->
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <i class="bi bi-info-circle-fill me-1"></i> Detail Pengajuan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%;">Nama Kegiatan</th>
                            <td>: <?php echo htmlspecialchars($pengajuan['nama_kegiatan']); ?></td>
                        </tr>
                        <tr>
                            <th>Ormawa Pengaju</th>
                            <td>: <?php echo htmlspecialchars($pengajuan['nama_ormawa']); ?></td>
                        </tr>
                        
                         <tr>
                            <th>Tanggal Kegiatan</th>
                            <td>: <?php echo isset($pengajuan['tanggal_kegiatan']) ? date('d F Y', strtotime($pengajuan['tanggal_kegiatan'])) : date('d F Y', strtotime($pengajuan['tanggal_pengajuan'])) ; ?></td>
                         </tr>
                        <tr>
                            <th>Dana Diajukan</th>
                            <td>: <strong>Rp <?php echo number_format($pengajuan['dana_diajukan'], 0, ',', '.'); ?></strong></td>
                        </tr>
                        <tr>
                            <th>File Proposal</th>
                            <td>:
                                <?php if (!empty($nama_file_proposal) && file_exists($path_proposal)): ?>
                                <a href="<?php echo htmlspecialchars($path_proposal); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye-fill me-1"></i> Lihat Proposal
                                </a>
                                <?php else: ?>
                                <span class="text-muted fst-italic">File tidak ditemukan atau belum diunggah.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Tindakan Verifikasi -->
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                 <div class="card-header bg-white py-3">
                    <i class="bi bi-check2-square me-1"></i> Tindakan Verifikasi
                </div>
                <div class="card-body">
                    <form action="index.php?page=verifikasi&id=<?php echo $id_pengajuan; ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Wajib diisi jika menolak)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="5" placeholder="Berikan alasan penolakan atau catatan tambahan jika disetujui..."></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                             <button type="submit" name="aksi" value="tolak" class="btn btn-danger me-md-2 mb-2 mb-md-0">
                                <i class="bi bi-x-circle-fill me-1"></i> Tolak
                            </button>
                            <button type="submit" name="aksi" value="setuju" class="btn btn-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Setujui & Lanjutkan
                            </button>
                        </div>
                         <a href="index.php?page=dashboard" class="btn btn-secondary mt-3 w-100">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
