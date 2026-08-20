<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

class Router {

    private $conn;

    private $pageMap = [
        'login'                      => ['file' => 'app/views/auth/login.php',                          'roles' => []],
        'logout'                     => ['file' => 'app/views/auth/logout.php',                         'roles' => []],
        'dashboard'                  => ['file' => 'app/views/ormawa/dashboard.php',                    'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'sarpras', 'sarpras_barang', 'admin']],
        'tambah'                     => ['file' => 'app/views/ormawa/tambah_pengajuan.php',              'roles' => ['ormawa', 'bem', 'bpm']],
        'edit'                       => ['file' => 'app/views/ormawa/edit.php',                         'roles' => ['ormawa', 'bem', 'bpm']],
        'riwayat'                    => ['file' => 'app/views/ormawa/riwayat.php',                      'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'bendahara', 'admin']],
        'upload_lpj'                 => ['file' => 'app/views/ormawa/upload_lpj.php',                   'roles' => ['ormawa', 'bem', 'bpm']],
        'revisi_lpj'                 => ['file' => 'app/views/ormawa/upload_lpj.php',                   'roles' => ['ormawa', 'bem', 'bpm']],
        'arsip_lpj'                  => ['file' => 'app/views/ormawa/arsip_lpj.php',                    'roles' => ['ormawa', 'bem', 'bpm']],
        'arsip_digital'              => ['file' => 'app/views/ormawa/arsip_digital.php',                'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'peminjaman_tempat'          => ['file' => 'app/views/ormawa/peminjaman_tempat.php',            'roles' => ['ormawa', 'bem', 'bpm']],
        'buat_proposal'              => ['file' => 'app/views/ormawa/buat_proposal.php',                'roles' => ['ormawa', 'bem', 'bpm']],
        'arsip_proposal'             => ['file' => 'app/views/ormawa/arsip_proposal.php',               'roles' => ['ormawa', 'bem', 'bpm']],
        'edit_proposal'              => ['file' => 'app/views/ormawa/edit_proposal.php',                'roles' => ['ormawa', 'bem', 'bpm']],
        'buat_surat_lain'            => ['file' => 'app/views/ormawa/buat_surat_lain.php',              'roles' => ['ormawa', 'bem', 'bpm']],
        'arsip_surat_lain'           => ['file' => 'app/views/ormawa/arsip_surat_lain.php',             'roles' => ['ormawa', 'bem', 'bpm']],
        'view_surat_lain'            => ['file' => 'app/views/ormawa/view_surat_lain.php',              'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'buat_lpj_otomatis'          => ['file' => 'app/views/ormawa/buat_lpj_otomatis.php',            'roles' => ['ormawa', 'bem', 'bpm']],
        'arsip_lpj_otomatis'         => ['file' => 'app/views/ormawa/arsip_lpj_otomatis.php',           'roles' => ['ormawa', 'bem', 'bpm']],
        'view_lpj_otomatis'          => ['file' => 'app/views/ormawa/view_lpj_otomatis.php',            'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'view_peminjaman'            => ['file' => 'app/views/ormawa/view_peminjaman_cetak.php',        'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'view_proposal'              => ['file' => 'app/views/ormawa/view_proposal_otomatis.php',       'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'detail'                     => ['file' => 'app/views/ormawa/detail.php',                      'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'cetak_surat'                => ['file' => 'app/views/ormawa/cetak_surat.php',                  'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'bendahara', 'admin']],
        'verify_page'                => ['file' => 'app/views/shared/verify_page.php',                  'roles' => []],
        'surat_balasan'              => ['file' => 'app/views/ormawa/cetak_surat.php',                  'roles' => []],
        'verifikasi'                 => ['file' => 'app/views/verifikator/verifikasi.php',              'roles' => ['bem', 'bpm', 'bkh', 'wr3', 'admin']],
        'verifikasi_lpj'             => ['file' => 'app/views/verifikator/verifikasi_lpj.php',          'roles' => ['bkh', 'wr3', 'admin']],
        'verifikasi_tempat'          => ['file' => 'app/views/verifikator/verifikasi_tempat.php',       'roles' => ['bkh', 'admin']],
        'ajukan_pencairan'           => ['file' => 'app/views/verifikator/ajukan_pencairan.php',        'roles' => ['bkh', 'admin']],
        'arsip_surat'                => ['file' => 'app/views/verifikator/arsip_surat.php',             'roles' => ['bkh', 'admin']],
        'manage_users'               => ['file' => 'app/views/admin/manage_users.php',                  'roles' => ['bkh', 'admin']],
        'hapus_user'                 => ['file' => 'app/views/admin/hapus_user.php',                    'roles' => ['bkh', 'admin']],
        'manage_saldo'               => ['file' => 'app/views/admin/manage_saldo.php',                  'roles' => ['bkh', 'wr3', 'admin']],
        'tambah_user'                => ['file' => 'app/views/admin/tambah_user.php',                   'roles' => ['bkh', 'admin']],
        'edit_user'                  => ['file' => 'app/views/admin/edit_user.php',                     'roles' => ['bkh', 'admin']],
        'atur_saldo'                 => ['file' => 'app/views/admin/atur_saldo.php',                    'roles' => ['bkh', 'admin']],
        'proses'                     => ['file' => 'app/views/bendahara/proses.php',                    'roles' => ['bendahara']],
        'profil'                     => ['file' => 'app/views/shared/profil.php',                       'roles' => ['ormawa','bpm','bem','bkh','wr3','bendahara','admin','sarpras','sarpras_barang']],
        'atur_sistem'                => ['file' => 'app/views/admin/atur_sistem.php',                   'roles' => ['bkh', 'admin']],
        'input_nomor_surat'          => ['file' => null,                                                'roles' => ['bkh', 'admin']],
        'peminjaman_barang'          => ['file' => 'app/views/ormawa/peminjaman_barang.php',             'roles' => ['ormawa', 'bem', 'bpm']],
        'sarpras_verifikasi_ruangan' => ['file' => 'app/views/sarpras/verifikasi_ruangan.php',          'roles' => ['sarpras']],
        'sarpras_verifikasi_barang'  => ['file' => 'app/views/sarpras/verifikasi_barang.php',           'roles' => ['sarpras_barang']],
        'manage_barang'              => ['file' => 'app/views/sarpras/manage_barang.php',               'roles' => ['sarpras_barang']],
        'verifikasi_barang_bkkh'     => ['file' => 'app/views/verifikator/verifikasi_barang_bkkh.php',  'roles' => ['bkh', 'admin']],
        'manage_regulasi'            => ['file' => 'app/views/verifikator/manage_regulasi.php',         'roles' => ['bpm']],
        'pusat_informasi'            => ['file' => 'app/views/ormawa/pusat_informasi.php',              'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'jadwal_rapat'               => ['file' => 'app/views/ormawa/jadwal_rapat.php',                 'roles' => ['ormawa', 'bem', 'bpm', 'bkh', 'wr3', 'bendahara', 'admin']],
        'aspirasi'                   => ['file' => 'app/views/shared/aspirasi_publik.php',              'roles' => []],
        'manage_aspirasi'            => ['file' => 'app/views/verifikator/manage_aspirasi.php',         'roles' => ['bpm']],
        'buat_surat_peringatan'      => ['file' => 'app/views/verifikator/buat_surat_peringatan.php',   'roles' => ['bpm', 'bkh', 'admin']],
        'panduan'                    => ['file' => 'app/views/shared/panduan.php',                      'roles' => []],
    ];

    private $dashboardMap = [
        'ormawa'        => 'app/views/ormawa/dashboard.php',
        'bem'           => 'app/views/verifikator/dashboard.php',
        'bpm'           => 'app/views/verifikator/dashboard.php',
        'bkh'           => 'app/views/verifikator/dashboard.php',
        'wr3'           => 'app/views/verifikator/dashboard.php',
        'bendahara'     => 'app/views/bendahara/dashboard.php',
        'sarpras'       => 'app/views/sarpras/dashboard.php',
        'sarpras_barang'=> 'app/views/sarpras/dashboard.php',
        'admin'         => 'app/views/verifikator/dashboard.php',
    ];

    private $standalonePages = [
        'login', 'logout', 'cetak_surat', 'surat_balasan',
        'verify_page', 'aspirasi', 'view_surat_lain',
        'view_proposal', 'view_peminjaman', 'panduan',
    ];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function dispatch() {
        $page_action = $_GET['page'] ?? '';

        $this->handleGetActions($page_action);
        $this->handlePostActions($page_action);

        $page       = $_GET['page'] ?? 'dashboard';
        $pageConfig = $this->pageMap[$page] ?? null;

        if (!$pageConfig) {
            http_response_code(404);
            die("<h1>404 - Halaman Tidak Ditemukan</h1><p>Halaman '<b>" . htmlspecialchars($page) . "</b>' belum terdaftar.</p>");
        }

        $allowedRoles = $pageConfig['roles'];
        $isPublic     = empty($allowedRoles);
        $userRole     = $_SESSION['user_role'] ?? null;

        if (!$isPublic) {
            check_login();
            check_role($allowedRoles);
        }

        $contentFile = ($page === 'dashboard' && isset($this->dashboardMap[$userRole]))
            ? $this->dashboardMap[$userRole]
            : $pageConfig['file'];

        $isStandalone = in_array($page, $this->standalonePages);

        $this->render($contentFile, $isStandalone);
    }

    private function render($contentFile, $isStandalone) {
        global $conn;
        if (!$isStandalone) {
            include ROOT_PATH . '/app/views/layouts/header.php';
            include ROOT_PATH . '/app/views/layouts/sidebar.php';
            echo '<div class="main-content-inner">';
            $this->renderToastScript();
            $this->renderNotifications();
        }

        if ($contentFile && file_exists(ROOT_PATH . '/' . $contentFile)) {
            include ROOT_PATH . '/' . $contentFile;
        } elseif ($contentFile) {
            echo "Error: File konten tidak ditemukan di '<b>" . htmlspecialchars($contentFile) . "</b>'";
        }

        if (!$isStandalone) {
            echo '</div>';
            include ROOT_PATH . '/app/views/layouts/footer.php';
        }
    }

    private function handleGetActions($page_action) {
        if ($page_action === 'toggle_status' && isset($_GET['id'], $_GET['new_status'])) {
            (new UserController($this->conn))->toggleStatus();
        }

        if ($page_action === 'api_notifikasi_belum_baca') {
            (new NotifikasiController($this->conn))->belumBaca();
        }

        if ($page_action === 'api_kalender_peminjaman') {
            (new ApiController($this->conn))->kalenderPeminjaman();
        }
    }

    private function handlePostActions($page_action) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $controllers = [
            'tambah_user'         => [UserController::class,        'tambahUser'],
            'edit_user'           => [UserController::class,        'editUser'],
            'atur_saldo'          => [UserController::class,        'aturSaldo'],
            'tambah'              => [PengajuanController::class,   'tambah'],
            'edit'                => [PengajuanController::class,   'edit'],
            'ajukan_pencairan'    => [VerifikasiController::class,  'ajukanPencairan'],
            'verifikasi'          => [VerifikasiController::class,  'verifikasiProposal'],
            'verifikasi_lpj'      => [VerifikasiController::class,  'verifikasiLpj'],
            'verifikasi_bendahara'=> [BendaharaController::class,   'verifikasi'],
            'profil'              => [ProfilController::class,      'update'],
            'pusat_informasi'     => [InformasiController::class,   'handlePengumuman'],
            'jadwal_rapat'        => [InformasiController::class,   'handleJadwalRapat'],
            'aspirasi'            => [AspirasiController::class,    'submit'],
            'manage_aspirasi'     => [AspirasiController::class,    'tanggapi'],
            'tandai_notif_terlihat' => [NotifikasiController::class, 'tandaiTerlihat'],
            'tandai_notif_baca'     => [NotifikasiController::class, 'tandaiBaca'],
            'followup_pengajuan'    => [PengajuanController::class,  'followup'],
        ];

        if (isset($controllers[$page_action])) {
            [$class, $method] = $controllers[$page_action];
            (new $class($this->conn))->$method();
        }

        if ($page_action === 'input_nomor_surat') {
            (new VerifikasiController($this->conn))->simpanNomorSurat();
        }
    }

    private function renderToastScript() {
        echo '
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        </div>
    </div>
    ';
        echo '
    <script>
    function showToast(message, type) {
        const toastContainer = document.querySelector(".toast-container");
        if (!toastContainer) return;
        const icon        = type === "success" ? "bi-check-circle-fill" : "bi-exclamation-triangle-fill";
        const headerClass = type === "success" ? "bg-success" : "bg-danger";
        const toastId     = "toast-" + Math.random().toString(36).substr(2, 9);
        const toastHTML   = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${headerClass} text-white">
                    <i class="bi ${icon} me-2"></i>
                    <strong class="me-auto">Notifikasi Sistem</strong>
                    <small>Baru saja</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        toastContainer.insertAdjacentHTML("beforeend", toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toastElement.addEventListener("hidden.bs.toast", function () { toastElement.remove(); });
        toast.show();
    }
    </script>
    ';
    }

    private function renderNotifications() {
        $message    = '';
        $alert_type = '';

        if (isset($_GET['error'])) {
            $alert_type = 'danger';
            $errorMap   = [
                'file_kosong'        => 'Gagal mengajukan pengajuan. File proposal tidak ditemukan.',
                'bukan_pdf'          => 'Gagal mengajukan pengajuan. File harus berformat PDF.',
                'bukan_gambar'       => 'Gagal memperbarui profil. File harus berformat JPG atau PNG.',
                'logo_bukan_gambar'  => 'Gagal memperbarui logo. File yang diupload harus berupa gambar.',
                'logo_terlalu_besar' => 'Gagal memperbarui logo. Ukuran file tidak boleh melebihi 1MB.',
                'logo_format_salah'  => 'Gagal memperbarui logo. Hanya format JPG, PNG, JPEG, GIF, & SVG yang diizinkan.',
                'file_terlalu_besar' => 'Gagal memperbarui profil. Ukuran file tidak boleh melebihi 2MB.',
                'upload_gagal'       => 'Gagal mengunggah file. Silakan coba lagi.',
                'db_gagal'           => 'Gagal menyimpan data ke database. Silakan coba lagi.',
                'update_gagal'       => 'Gagal menyimpan data ke database. Silakan coba lagi.',
                'toggle_gagal'       => 'Gagal menyimpan data ke database. Silakan coba lagi.',
                'gagal_simpan'       => 'Gagal menyimpan data ke database. Silakan coba lagi.',
                'edit_disallowed'    => 'Pengajuan hanya bisa direvisi jika berstatus Ditolak.',
                'unauthorized'       => 'Anda tidak memiliki hak akses untuk aksi ini.',
                'status_salah'       => 'Status pengajuan tidak sesuai untuk aksi ini.',
                'status_tidak_sesuai'=> 'Status pengajuan tidak sesuai untuk aksi ini.',
                'saldo_tidak_cukup'  => 'Dana yang Anda ajukan melebihi sisa saldo Anda.',
                'invalid_id'         => 'ID pengajuan tidak valid. Aksi dibatalkan.',
                'db_prepare_gagal'   => 'Terjadi kesalahan pada persiapan query database.',
                'form_kosong'        => 'Mohon lengkapi semua field pada formulir.',
                'username_duplikat'  => 'Username sudah digunakan, silakan gunakan username lain.',
            ];
            $message = $errorMap[$_GET['error']] ?? 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';
        } elseif (isset($_GET['status']) || isset($_GET['success'])) {
            $alert_type = 'success';
            $statusKey  = $_GET['status'] ?? $_GET['success'];
            $statusMap  = [
                'tambah_sukses'     => 'Pengajuan berhasil ditambahkan. Silakan tunggu proses verifikasi.',
                'edit_sukses'       => 'Pengajuan berhasil direvisi dan diajukan kembali.',
                'cair_sukses'       => 'Proposal berhasil diteruskan ke Bendahara untuk proses pencairan.',
                'bendahara_sukses'  => 'Proposal berhasil diteruskan ke Bendahara untuk proses pencairan.',
                'verifikasi_sukses' => 'Verifikasi berhasil disimpan.',
                'update_sukses'     => 'Profil berhasil diperbarui.',
                'sukses'            => 'Pengaturan sistem telah berhasil diperbarui.',
                'logo_update_sukses'=> 'Logo sistem berhasil diperbarui.',
                'saldo_sukses'      => 'Saldo pengguna berhasil diperbarui.',
                'toggle_sukses'     => 'Status akun pengguna berhasil diubah.',
                'tambah_user_sukses'=> 'User baru berhasil ditambahkan.',
                'edit_user_sukses'  => 'Data user berhasil diperbarui.',
                'pengumuman_sukses' => 'Pengumuman berhasil ditambahkan.',
                'hapus_sukses'      => 'Data berhasil dihapus.',
                'rapat_sukses'      => 'Jadwal rapat berhasil ditambahkan.',
                'aspirasi_sukses'   => 'Aspirasi berhasil dikirim.',
                'tanggapan_sukses'  => 'Tanggapan berhasil disimpan.',
                'nomor_sukses'      => 'Nomor surat berhasil disimpan.',
                'followup_sukses'   => 'Follow-up berhasil dikirim ke verifikator.',
            ];
            $message = $statusMap[$statusKey] ?? 'Operasi berhasil.';
        }

        if (!empty($message)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('" . addslashes($message) . "', '" . $alert_type . "');
                });
            </script>";
        }
    }
}
?>
