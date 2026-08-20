<?php
/**
 * File: sidebar.php
 * Deskripsi: Bagian sidebar navigasi dengan fitur tema terang/redup dan logo sistem dinamis.
 */

// === PERBAIKAN: Mengambil data dari session untuk efisiensi ===
$konfigurasi = $_SESSION['konfigurasi'] ?? [];
$nama_aplikasi = htmlspecialchars($konfigurasi['nama_aplikasi'] ?? 'SI-Keuangan');
$logo_file = $konfigurasi['logo_sistem'] ?? 'default_logo.png';

$logo_path = 'https://placehold.co/200x60/FFFFFF/000000?text=No+Logo';
$potential_path = 'uploads/sistem/' . $logo_file;
if (!empty($logo_file) && file_exists($potential_path)) {
    $logo_path = $potential_path;
}
// === AKHIR PERBAIKAN ===

$currentPage = $_GET['page'] ?? 'dashboard';
$user_role = $_SESSION['user_role'] ?? null;
$user_status = $_SESSION['status_akun'] ?? 'nonaktif';

// === PENAMBAHAN BARU: Mendefinisikan judul halaman di sini agar bisa dipakai di beberapa tempat ===
$page_title_map = [
    'dashboard' => 'Dashboard', 'tambah' => 'Buat Pengajuan', 'riwayat' => 'Riwayat Pengajuan',
    'detail' => 'Detail Pengajuan', 'edit' => 'Revisi Pengajuan', 'upload_lpj' => 'Upload LPJ',
    'revisi_lpj' => 'Revisi LPJ', 'verifikasi' => 'Verifikasi Proposal', 'verifikasi_lpj' => 'Verifikasi LPJ',
    'manage_users' => 'Manajemen Pengguna', 'tambah_user' => 'Tambah Pengguna', 'edit_user' => 'Edit Pengguna',
    'ajukan_pencairan' => 'Ajukan Pencairan', 'arsip_surat' => 'Arsip Surat Balasan',
    'proses' => 'Proses Pencairan Dana', 'manage_saldo' => 'Manajemen Saldo', 'atur_saldo' => 'Atur Saldo Pengguna',
    'profil' => 'Atur Profil', 'atur_sistem' => 'Pengaturan Sistem', 'arsip_lpj' => 'Arsip LPJ', 'peminjaman_tempat' => 'Peminjaman Tempat', 'verifikasi_tempat' => 'Verifikasi Peminjaman Tempat', 'buat_proposal' => 'Buat Proposal Otomatis', 'arsip_proposal' => 'Arsip Proposal', 'edit_proposal' => 'Edit Proposal', 'buat_surat_lain' => 'Buat Surat Lainnya', 'arsip_surat_lain' => 'Arsip Surat Lainnya', 'buat_lpj_otomatis' => 'Buat LPJ Otomatis', 'arsip_lpj_otomatis' => 'Arsip LPJ Otomatis', 'arsip_digital' => 'Pusat Arsip Digital Persuratan',
    'sarpras_verifikasi_ruangan' => 'Verifikasi Ruangan (Sarpras)',
    'sarpras_verifikasi_barang' => 'Verifikasi Barang (Sarpras)',
    'manage_barang' => 'Manajemen Inventaris Barang',
    'pusat_informasi' => 'Pusat Informasi & Berita',
    'jadwal_rapat' => 'Jadwal Rapat',
    'manage_aspirasi' => 'Kelola Aspirasi (BPM)',
    'buat_surat_peringatan' => 'Buat Surat Peringatan (BPM/BKKH)'
];
$current_title = $page_title_map[$currentPage] ?? '';
?>

<!-- CSS tema & layout dipindah ke assets/css/app.css (konsolidasi) -->

<div class="page-wrapper">
    <aside class="sidebar d-flex flex-column p-3" id="sidebar"
       data-app-name="<?php echo htmlspecialchars($nama_aplikasi); ?>"
       data-page-title="<?php echo htmlspecialchars($current_title); ?>"
       data-favicon="<?php echo htmlspecialchars($logo_path); ?>">
        <!-- === PERUBAHAN: Logo dan Nama Aplikasi kembali ke sidebar === -->
        <a href="index.php?page=dashboard" class="d-flex align-items-center mb-3 text-white text-decoration-none">
            <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo Sistem" class="sidebar-brand-logo me-2">
            <span class="sidebar-brand-text"><?php echo $nama_aplikasi; ?></span>
        </a>
        <hr class="text-secondary mt-0 mb-3">

        <ul class="nav nav-pills flex-column mb-auto">
            <!-- LOGIKA MENU ANDA TIDAK DIUBAH SAMA SEKALI -->
            <li class="nav-item">
                <a href="index.php?page=dashboard" class="nav-link text-white <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <?php if ($user_role): ?>
                <?php if (in_array($user_role, ['ormawa', 'bem', 'bpm'])):
                    if ($user_status === 'aktif'): ?>
                        <li>
                            <a href="index.php?page=tambah" class="nav-link text-white <?php echo ($currentPage == 'tambah') ? 'active' : ''; ?>">
                                <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=peminjaman_tempat" class="nav-link text-white <?php echo ($currentPage == 'peminjaman_tempat') ? 'active' : ''; ?>">
                                <i class="bi bi-building me-2"></i> Peminjaman Tempat
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=peminjaman_barang" class="nav-link text-white <?php echo ($currentPage == 'peminjaman_barang') ? 'active' : ''; ?>">
                                <i class="bi bi-tools me-2"></i> Peminjaman Barang
                            </a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link text-white dropdown-toggle <?php echo (in_array($currentPage, ['buat_proposal', 'arsip_proposal', 'edit_proposal', 'buat_surat_lain', 'arsip_surat_lain'])) ? 'active' : ''; ?>" 
                           data-bs-toggle="collapse" href="#menuPersuratan" role="button" aria-expanded="false">
                            <i class="bi bi-file-earmark-text me-2"></i> Persuratan Digital
                        </a>
                        <div class="collapse <?php echo (in_array($currentPage, ['buat_proposal', 'arsip_proposal', 'edit_proposal', 'buat_surat_lain', 'arsip_surat_lain'])) ? 'show' : ''; ?>" id="menuPersuratan">
                            <ul class="nav flex-column ms-3 mt-1">
                                <li class="nav-item">
                                    <a href="index.php?page=buat_proposal" class="nav-link text-white py-1 <?php echo ($currentPage == 'buat_proposal') ? 'fw-bold' : ''; ?>">
                                        <i class="bi bi-magic me-2"></i> Buat Proposal
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=buat_surat_lain" class="nav-link text-white py-1 <?php echo ($currentPage == 'buat_surat_lain') ? 'fw-bold' : ''; ?>">
                                        <i class="bi bi-file-earmark-plus me-2"></i> Buat Surat Lain
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=buat_lpj_otomatis" class="nav-link text-white py-1 <?php echo ($currentPage == 'buat_lpj_otomatis') ? 'fw-bold' : ''; ?>">
                                        <i class="bi bi-check2-square me-2"></i> Buat LPJ
                                    </a>
                                </li>
                                <li class="nav-item border-top mt-2 pt-2">
                                    <a href="index.php?page=arsip_digital" class="nav-link text-white py-1 <?php echo ($currentPage == 'arsip_digital') ? 'active bg-primary rounded' : ''; ?>">
                                        <i class="bi bi-collection-play me-2"></i> ðŸ“‚ Arsip Digital
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="index.php?page=riwayat" class="nav-link text-white <?php echo (in_array($currentPage, ['riwayat', 'detail', 'edit', 'upload_lpj', 'revisi_lpj'])) ? 'active' : ''; ?>">
                            <i class="bi bi-clock-history me-2"></i> Riwayat
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=arsip_lpj" class="nav-link text-white <?php echo ($currentPage == 'arsip_lpj') ? 'active' : ''; ?>">
                            <i class="bi bi-archive-fill me-2"></i> Arsip LPJ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=pusat_informasi" class="nav-link text-white <?php echo ($currentPage == 'pusat_informasi') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-megaphone-fill me-2"></i> Pusat Informasi & Berita
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=jadwal_rapat" class="nav-link text-white <?php echo ($currentPage == 'jadwal_rapat') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-calendar-check-fill me-2"></i> Jadwal Rapat
                        </a>
                    </li>
                    <?php if ($user_role === 'bpm'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=buat_surat_peringatan" class="nav-link text-white <?php echo ($currentPage == 'buat_surat_peringatan') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-file-earmark-medical-fill me-2 text-danger"></i> Buat Surat Peringatan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=manage_aspirasi" class="nav-link text-white <?php echo ($currentPage == 'manage_aspirasi') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-chat-left-text-fill me-2 text-warning"></i> Kelola Aspirasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=manage_regulasi" class="nav-link text-white <?php echo ($currentPage == 'manage_regulasi') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-journal-text me-2"></i> Kelola Regulasi
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; // End Ormawa/BEM/BPM block ?>

                <!-- MENU BAGIAN SARPRAS RUANGAN -->
                <?php if ($user_role == 'sarpras'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=sarpras_verifikasi_ruangan" class="nav-link text-white <?php echo ($currentPage == 'sarpras_verifikasi_ruangan') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-building-check me-2"></i> Verifikasi Ruangan
                        </a>
                    </li>
                <?php endif; ?>

                <!-- MENU BAGIAN SARPRAS BARANG -->
                <?php if ($user_role == 'sarpras_barang'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=sarpras_verifikasi_barang" class="nav-link text-white <?php echo ($currentPage == 'sarpras_verifikasi_barang') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-tools me-2"></i> Verifikasi Barang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=manage_barang" class="nav-link text-white <?php echo ($currentPage == 'manage_barang') ? 'active bg-primary' : ''; ?>">
                            <i class="bi bi-box-seam me-2"></i> Master Barang
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="index.php?page=profil" class="nav-link text-white <?php echo ($currentPage == 'profil') ? 'active bg-primary' : ''; ?>">
                        <i class="bi bi-person-circle me-2"></i> Profil
                    </a>
                </li>
                
                <?php if (in_array($user_role, ['bem', 'bpm', 'bkh', 'wr3', 'admin'])): ?>
                <?php endif; ?>

                <?php if (in_array($user_role, ['bkh', 'wr3', 'admin'])): ?>
                    <li>
        <a href="index.php?page=manage_saldo" class="nav-link text-white <?php echo ($currentPage == 'manage_saldo' || $currentPage == 'atur_saldo') ? 'active' : ''; ?>">
            <i class="bi bi-cash-coin me-2"></i> 
            <?php 
                echo ($user_role === 'wr3') ? 'Rincian Saldo' : 'Manajemen Saldo'; 
            ?>
        </a>
    </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['bkh', 'admin'])): 
                    $isManageUserPage = in_array($currentPage, ['manage_users', 'tambah_user', 'edit_user']);
                ?>
                    <li>
                        <a href="index.php?page=manage_users" class="nav-link text-white <?php echo $isManageUserPage ? 'active' : ''; ?>">
                            <i class="bi bi-people-fill me-2"></i> Manajemen User
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=atur_sistem" class="nav-link text-white <?php echo ($currentPage == 'atur_sistem') ? 'active' : ''; ?>">
                            <i class="bi bi-gear-fill me-2"></i> Manajemen Sistem
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=arsip_surat" class="nav-link text-white <?php echo ($currentPage == 'arsip_surat') ? 'active' : ''; ?>">
                            <i class="bi bi-archive-fill me-2"></i> Arsip Surat
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=buat_surat_peringatan" class="nav-link text-white <?php echo ($currentPage == 'buat_surat_peringatan') ? 'active bg-primary rounded' : ''; ?>">
                            <i class="bi bi-file-earmark-medical-fill me-2 text-danger"></i> Buat Surat Peringatan
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=verifikasi_tempat" class="nav-link text-white <?php echo ($currentPage == 'verifikasi_tempat') ? 'active' : ''; ?>">
                            <i class="bi bi-building-check me-2"></i> Verifikasi Tempat
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

        <hr class="text-secondary">
        
    </aside>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <div class="content-wrapper" id="content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <div class="container-fluid">
                <!-- === PERUBAHAN: Tombol toggle kembali seperti semula === -->
                <button class="btn " type="button" id="sidebar-toggler">
                    <i class="bi bi-list fs-4"></i>
                </button>
    <div class="theme-switcher">
    <input class="d-none" type="checkbox" role="switch" id="theme-toggle">
    <label class="form-check-label" for="theme-toggle" style="cursor: pointer;">
    <i class="bi bi-sun-fill theme-icon-current"></i> </label></div>
                <!-- Lonceng Notifikasi (realtime via SSE) -->
                <div class="dropdown me-2">
                    <button class="btn btn-link position-relative p-0 text-body" type="button" id="notif-bell" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                        <i class="bi bi-bell fs-4"></i>
                        <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow notif-menu" style="width: 340px;" aria-labelledby="notif-bell">
                        <div class="dropdown-header bg-primary text-white py-2">
                            <i class="bi bi-bell-fill me-1"></i> Notifikasi
                        </div>
                        <div id="notif-list" class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;"></div>
                    </div>
                </div>
                <!-- Judul Halaman ditampilkan kembali di tengah -->
               
                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                            $default_avatar = 'assets/images/default-avatar.svg';
                            $foto_profil_path = $default_avatar;
                            if (!empty($_SESSION['foto_profil'])) {
                                $potential_foto = 'uploads/profil/' . $_SESSION['foto_profil'];
                                if (file_exists($potential_foto)) {
                                    $foto_profil_path = $potential_foto;
                                }
                            }
                        ?>
                        <img src="<?php echo $foto_profil_path; ?>" alt="Foto" width="32" height="32" class="rounded-circle me-2" style="object-fit: cover;">
                        <strong><?php echo isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Pengguna'; ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                        <li><a class="dropdown-item" href="index.php?page=profil"><i class="bi bi-person-fill me-2"></i>Atur Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="main-content-inner">

