-- ============================================================
-- db_pengajuan.sql
-- Schema lengkap + data demo Sistem Keuangan
-- Dijalankan otomatis oleh scripts/setup_complete.php
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- TABEL INTI
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
  `id_user`       INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_lengkap`  VARCHAR(100) NOT NULL,
  `username`      VARCHAR(50)  NOT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `role`          ENUM('admin','ormawa','bem','bpm','bkh','wr3','bendahara','sarpras','sarpras_barang') NOT NULL,
  `twofa_secret`  VARCHAR(255) DEFAULT NULL,
  `twofa_enabled` TINYINT(1)   NOT NULL DEFAULT 0,
  `twofa_backup_codes` JSON    DEFAULT NULL,
  `twofa_confirmed_at` DATETIME DEFAULT NULL,
  `foto_profil`   VARCHAR(255) DEFAULT NULL,
  `status_akun`   ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `saldo`         DECIMAL(15,2) DEFAULT 0.00,
  `nama_ketua`       VARCHAR(100) DEFAULT NULL,
  `nama_sekretaris`  VARCHAR(100) DEFAULT NULL,
  `nama_bendahara`   VARCHAR(100) DEFAULT NULL,
  `ttd_ketua`        VARCHAR(255) DEFAULT NULL,
  `ttd_sekretaris`   VARCHAR(255) DEFAULT NULL,
  `ttd_bendahara`    VARCHAR(255) DEFAULT NULL,
  `logo_ormawa`      VARCHAR(255) DEFAULT NULL,
  `alamat`           TEXT        DEFAULT NULL,
  `telepon`          VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password semua user demo: "password123" (bcrypt)
INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_akun`, `saldo`) VALUES
(1,  'Administrator',      'admin',           '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'admin',      'aktif', 0.00),
(2,  'BEM ITG',            'bem',             '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'bem',        'aktif', 0.00),
(3,  'BPM ITG',            'bpm',             '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'bpm',        'aktif', 0.00),
(4,  'BKKH ITG',           'bkkh',            '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'bkh',        'aktif', 0.00),
(5,  'Wakil Rektor 3',     'wr3',             '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'wr3',        'aktif', 0.00),
(6,  'Bendahara ITG',      'bendahara',       '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'bendahara',  'aktif', 0.00),
(7,  'Himatif ITG',        'himatif',         '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'ormawa',     'aktif', 0.00),
(8,  'Hima Informatika 2', 'hima_si',         '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'ormawa',     'aktif', 0.00),
(9,  'Sarpras Ruangan',    'sarpras_ruangan', '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'sarpras',    'aktif', 0.00),
(10, 'Sarpras Barang',     'sarpras_barang',  '$2y$10$rXbMTb1JdXsp2QzqBgGnpeDpZOJ0KWBeEgIdZXZESm/QetniQZLQS', 'sarpras_barang', 'aktif', 0.00);

-- ============================================================

CREATE TABLE IF NOT EXISTS `konfigurasi` (
  `id`                INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_konfigurasi`  VARCHAR(100) NOT NULL,
  `nilai_konfigurasi` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_konfigurasi` (`nama_konfigurasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `konfigurasi` (`nama_konfigurasi`, `nilai_konfigurasi`) VALUES
('nama_aplikasi', 'SI-Keuangan'),
('logo_sistem',   NULL),
('kop_logo',      NULL),
('kop_baris1',    'NAMA INSTITUSI'),
('kop_baris2',    'BIRO KETENAGAAN KEMAHASISWAAN DAN HUBUNGAN MASYARAKAT (BKKH)'),
('kop_baris3',    'Alamat Institusi'),
('kop_baris4',    'Telp. -, Email: -');

-- ============================================================

CREATE TABLE IF NOT EXISTS `pengajuan` (
  `id_pengajuan`        INT(11)      NOT NULL AUTO_INCREMENT,
  `id_user_ormawa`      INT(11)      NOT NULL,
  `nama_kegiatan`       VARCHAR(255) NOT NULL,
  `dana_diajukan`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `judul_kegiatan`      VARCHAR(255) DEFAULT NULL,
  `deskripsi_kegiatan`  TEXT         DEFAULT NULL,
  `tanggal_pengajuan`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nominal_pengajuan`   DECIMAL(15,2) DEFAULT 0.00,
  `file_proposal`       VARCHAR(255) DEFAULT NULL,
  `file_lpj`            VARCHAR(255) DEFAULT NULL,
  `tanggal_upload_lpj`  DATE         DEFAULT NULL,
  `status`              ENUM('Draft','Diajukan Ke BEM','Ditolak BEM','Diajukan Ke BPM','Ditolak BPM','Verifikasi BKKH','Ditolak BKKH','Verifikasi WR3','Ditolak WR3','Disetujui WR3, Siap Diajukan ke Bendahara','Diajukan ke Bendahara','Dana Cair','LPJ Diajukan','LPJ Ditolak BKKH','LPJ Diverifikasi','Selesai') NOT NULL DEFAULT 'Draft',
  `catatan_revisi`      TEXT         DEFAULT NULL,
  `unique_code`         VARCHAR(64)  DEFAULT NULL,
  `nomor_surat`         VARCHAR(100) DEFAULT NULL,
  `notif_cair_terlihat` TINYINT(1)   NOT NULL DEFAULT 0,
  `tanggal_update`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengajuan`),
  KEY `id_user_ormawa` (`id_user_ormawa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `histori_status` (
  `id_histori`     INT(11)      NOT NULL AUTO_INCREMENT,
  `id_pengajuan`   INT(11)      NOT NULL,
  `status`         VARCHAR(100) NOT NULL,
  `tanggal_update` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user`        INT(11)      NOT NULL,
  `catatan`        TEXT         DEFAULT NULL,
  `unique_code`    VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_histori`),
  KEY `id_pengajuan` (`id_pengajuan`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `dana` (
  `id_dana`       INT(11)       NOT NULL AUTO_INCREMENT,
  `id_pengajuan`  INT(11)       NOT NULL,
  `nominal_cair`  DECIMAL(15,2) NOT NULL,
  `tanggal_cair`  DATETIME      DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dana`),
  KEY `id_pengajuan` (`id_pengajuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `notifikasi` (
  `id_notif`    INT(11)                    NOT NULL AUTO_INCREMENT,
  `id_user`     INT(11)                    NOT NULL,
  `pesan`       TEXT                       NOT NULL,
  `status_baca` ENUM('belum','sudah')      DEFAULT 'belum',
  `waktu`       TIMESTAMP                  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notif`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABEL INFORMASI & KOMUNIKASI
-- ============================================================

CREATE TABLE IF NOT EXISTS `aspirasi` (
  `id_aspirasi`   INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_pelapor`  VARCHAR(100) DEFAULT 'Anonim',
  `email_pelapor` VARCHAR(100) DEFAULT NULL,
  `kategori`      ENUM('Fasilitas','Layanan Kampus','Ormawa','Lainnya') NOT NULL,
  `subjek`        VARCHAR(255) DEFAULT NULL,
  `isi_aspirasi`  TEXT         NOT NULL,
  `tanggal_masuk` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`        ENUM('Pending','Diterima','Ditindaklanjuti','Selesai') DEFAULT 'Pending',
  `tanggapan_bpm` TEXT         DEFAULT NULL,
  PRIMARY KEY (`id_aspirasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `pengumuman` (
  `id_pengumuman`  INT(11)                  NOT NULL AUTO_INCREMENT,
  `judul`          VARCHAR(255)             NOT NULL,
  `isi`            TEXT                     NOT NULL,
  `file_lampiran`  VARCHAR(255)             DEFAULT NULL,
  `tanggal_upload` DATETIME                 DEFAULT CURRENT_TIMESTAMP,
  `id_user_upload` INT(11)                  NOT NULL,
  `status`         ENUM('Aktif','Arsip')    DEFAULT 'Aktif',
  PRIMARY KEY (`id_pengumuman`),
  KEY `id_user_upload` (`id_user_upload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `jadwal_rapat` (
  `id_rapat`        INT(11)                                    NOT NULL AUTO_INCREMENT,
  `judul_rapat`     VARCHAR(255)                               NOT NULL,
  `deskripsi`       TEXT                                       DEFAULT NULL,
  `tanggal_rapat`   DATE                                       NOT NULL,
  `jam_rapat`       TIME                                       NOT NULL,
  `lokasi`          VARCHAR(255)                               NOT NULL,
  `link_meeting`    VARCHAR(255)                               DEFAULT NULL,
  `id_penyelenggara` INT(11)                                   NOT NULL,
  `target_peserta`  VARCHAR(255)                               DEFAULT NULL,
  `status`          ENUM('Direncanakan','Selesai','Dibatalkan') DEFAULT 'Direncanakan',
  `created_at`      TIMESTAMP                                  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rapat`),
  KEY `id_penyelenggara` (`id_penyelenggara`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `regulasi` (
  `id_regulasi`    INT(11)                                        NOT NULL AUTO_INCREMENT,
  `judul`          VARCHAR(255)                                   NOT NULL,
  `deskripsi`      TEXT                                           DEFAULT NULL,
  `file_path`      VARCHAR(255)                                   DEFAULT NULL,
  `kategori`       ENUM('Undang-Undang','Pengumuman','Pedoman')   DEFAULT 'Undang-Undang',
  `tgl_upload`     TIMESTAMP                                      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user_upload` INT(11)                                        DEFAULT NULL,
  PRIMARY KEY (`id_regulasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABEL SARPRAS
-- ============================================================

CREATE TABLE IF NOT EXISTS `master_ruangan` (
  `id_ruangan`   INT(11)                     NOT NULL AUTO_INCREMENT,
  `nama_ruangan` VARCHAR(100)                NOT NULL,
  `kapasitas`    INT(11)                     NOT NULL,
  `fasilitas`    TEXT                        DEFAULT NULL,
  `status_aktif` ENUM('aktif','nonaktif')    DEFAULT 'aktif',
  PRIMARY KEY (`id_ruangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `master_ruangan` (`nama_ruangan`, `kapasitas`, `fasilitas`) VALUES
('Aula Gedung Rektorat',  500,  'AC, Sound System, Proyektor, Kursi'),
('Ruang Rapat Mahasiswa', 30,   'AC, Meja Rapat, Papan Tulis, Proyektor'),
('Lapangan Olahraga Utama', 1000, 'Garis Lapangan, Tribun, Lampu Sorot');

-- ============================================================

CREATE TABLE IF NOT EXISTS `peminjaman_tempat` (
  `id_peminjaman`      INT(11)      NOT NULL AUTO_INCREMENT,
  `id_user_ormawa`     INT(11)      NOT NULL,
  `id_ruangan`         INT(11)      NOT NULL,
  `tgl_mulai`          DATE         NOT NULL,
  `tgl_selesai`        DATE         NOT NULL,
  `jam_mulai`          TIME         NOT NULL,
  `jam_selesai`        TIME         NOT NULL,
  `nama_kegiatan`      VARCHAR(255) NOT NULL,
  `deskripsi_kegiatan` TEXT         DEFAULT NULL,
  `status`             ENUM('Menunggu BKKH','Disetujui','Ditolak') DEFAULT 'Menunggu BKKH',
  `status_bkkh`        ENUM('Pending','Diverifikasi','Ditolak')    DEFAULT 'Pending',
  `status_sarpras`     ENUM('Pending','Disetujui','Ditolak')       DEFAULT 'Pending',
  `catatan_penolakan`  TEXT         DEFAULT NULL,
  `catatan_sarpras`    TEXT         DEFAULT NULL,
  `tgl_pengajuan`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_peminjaman`),
  KEY `id_user_ormawa` (`id_user_ormawa`),
  KEY `id_ruangan` (`id_ruangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `master_barang` (
  `id_barang`     INT(11)                  NOT NULL AUTO_INCREMENT,
  `nama_barang`   VARCHAR(255)             NOT NULL,
  `deskripsi`     TEXT                     DEFAULT NULL,
  `stok_total`    INT(11)                  DEFAULT 0,
  `stok_tersedia` INT(11)                  DEFAULT 0,
  `status_aktif`  ENUM('aktif','nonaktif') DEFAULT 'aktif',
  PRIMARY KEY (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `master_barang` (`nama_barang`, `deskripsi`, `stok_total`, `stok_tersedia`) VALUES
('Proyektor Epson',       'Proyektor untuk presentasi',       5,   5),
('Kursi Lipat',           'Kursi lipat untuk acara outdoor',  100, 100),
('Sound System Portable', 'Sound system untuk acara kecil',   3,   3),
('Meja Lipat',            'Meja lipat serbaguna',             20,  20),
('Kabel Roll',            'Kabel ekstensi 10 meter',          10,  10);

-- ============================================================

CREATE TABLE IF NOT EXISTS `peminjaman_barang` (
  `id_peminjaman_barang` INT(11)      NOT NULL AUTO_INCREMENT,
  `id_user_ormawa`       INT(11)      NOT NULL,
  `nama_kegiatan`        VARCHAR(255) NOT NULL,
  `tgl_mulai`            DATE         NOT NULL,
  `tgl_selesai`          DATE         NOT NULL,
  `kebutuhan_barang`     TEXT         DEFAULT NULL,
  `status_bkkh`          ENUM('Pending','Diverifikasi','Ditolak') DEFAULT 'Pending',
  `status_sarpras`       ENUM('Pending','Disetujui','Ditolak')    DEFAULT 'Pending',
  `catatan_penolakan`    TEXT         DEFAULT NULL,
  `tgl_pengajuan`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_peminjaman_barang`),
  KEY `id_user_ormawa` (`id_user_ormawa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABEL DOKUMEN OTOMATIS
-- ============================================================

CREATE TABLE IF NOT EXISTS `proposal_otomatis` (
  `id_proposal`    INT(11)                   NOT NULL AUTO_INCREMENT,
  `id_user_ormawa` INT(11)                   NOT NULL,
  `nama_kegiatan`  VARCHAR(255)              NOT NULL,
  `latar_belakang` TEXT                      DEFAULT NULL,
  `tujuan`         TEXT                      DEFAULT NULL,
  `sasaran`        TEXT                      DEFAULT NULL,
  `penutup`        TEXT                      DEFAULT NULL,
  `tgl_dibuat`     TIMESTAMP                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status`         ENUM('Draft','Final')     DEFAULT 'Draft',
  `ttd_1_key`      VARCHAR(20)               DEFAULT 'ketua',
  `ttd_2_key`      VARCHAR(20)               DEFAULT 'sekretaris',
  `ttd_3_key`      VARCHAR(20)               DEFAULT 'ketua',
  `ttd_1_file`     VARCHAR(255)              DEFAULT NULL,
  `ttd_2_file`     VARCHAR(255)              DEFAULT NULL,
  `ttd_3_file`     VARCHAR(255)              DEFAULT NULL,
  PRIMARY KEY (`id_proposal`),
  KEY `id_user_ormawa` (`id_user_ormawa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `proposal_rab` (
  `id_rab`        INT(11)       NOT NULL AUTO_INCREMENT,
  `id_proposal`   INT(11)       NOT NULL,
  `rincian`       VARCHAR(255)  NOT NULL,
  `volume`        INT(11)       NOT NULL,
  `satuan`        VARCHAR(50)   DEFAULT NULL,
  `harga_satuan`  DECIMAL(15,2) NOT NULL,
  `total_harga`   DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id_rab`),
  KEY `id_proposal` (`id_proposal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `proposal_panitia` (
  `id_panitia`      INT(11)      NOT NULL AUTO_INCREMENT,
  `id_proposal`     INT(11)      NOT NULL,
  `jabatan`         VARCHAR(100) NOT NULL,
  `nama_mahasiswa`  VARCHAR(255) NOT NULL,
  `nim`             VARCHAR(20)  DEFAULT NULL,
  PRIMARY KEY (`id_panitia`),
  KEY `id_proposal` (`id_proposal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `lpj_otomatis` (
  `id_lpj`                 INT(11)               NOT NULL AUTO_INCREMENT,
  `id_user_ormawa`         INT(11)               NOT NULL,
  `nama_kegiatan`          VARCHAR(255)          NOT NULL,
  `pendahuluan`            TEXT                  DEFAULT NULL,
  `pelaksanaan_kegiatan`   TEXT                  DEFAULT NULL,
  `hasil_kegiatan`         TEXT                  DEFAULT NULL,
  `hambatan_kendala`       TEXT                  DEFAULT NULL,
  `saran_rekomendasi`      TEXT                  DEFAULT NULL,
  `penutup`                TEXT                  DEFAULT NULL,
  `tgl_dibuat`             TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ttd_1_key`              VARCHAR(50)           DEFAULT 'ketua',
  `ttd_2_key`              VARCHAR(50)           DEFAULT 'sekretaris',
  `ttd_3_key`              VARCHAR(50)           DEFAULT 'bendahara',
  `ttd_1_file`             VARCHAR(255)          DEFAULT NULL,
  `ttd_2_file`             VARCHAR(255)          DEFAULT NULL,
  `ttd_3_file`             VARCHAR(255)          DEFAULT NULL,
  `status`                 ENUM('Draft','Final') DEFAULT 'Final',
  PRIMARY KEY (`id_lpj`),
  KEY `id_user_ormawa` (`id_user_ormawa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `lpj_anggaran` (
  `id_anggaran`    INT(11)       NOT NULL AUTO_INCREMENT,
  `id_lpj`         INT(11)       NOT NULL,
  `uraian`         VARCHAR(255)  DEFAULT NULL,
  `estimasi_dana`  DECIMAL(15,2) DEFAULT NULL,
  `realisasi_dana` DECIMAL(15,2) DEFAULT NULL,
  `keterangan`     VARCHAR(255)  DEFAULT NULL,
  PRIMARY KEY (`id_anggaran`),
  KEY `id_lpj` (`id_lpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `lpj_lampiran` (
  `id_lampiran`   INT(11)                             NOT NULL AUTO_INCREMENT,
  `id_lpj`        INT(11)                             NOT NULL,
  `nama_file`     VARCHAR(255)                        NOT NULL,
  `tipe_lampiran` ENUM('Kwitansi','Dokumentasi')      NOT NULL,
  `keterangan`    VARCHAR(255)                        DEFAULT NULL,
  PRIMARY KEY (`id_lampiran`),
  KEY `id_lpj` (`id_lpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE IF NOT EXISTS `surat_otomatis` (
  `id_surat`         INT(11)                                               NOT NULL AUTO_INCREMENT,
  `id_user_ormawa`   INT(11)                                               NOT NULL,
  `jenis_surat`      ENUM('Undangan','Tugas','Permohonan','Keterangan','Peringatan') NOT NULL,
  `nomor_surat`      VARCHAR(100)                                          DEFAULT NULL,
  `perihal`          VARCHAR(255)                                          DEFAULT NULL,
  `tujuan_surat`     VARCHAR(255)                                          DEFAULT NULL,
  `isi_json`         TEXT                                                  NOT NULL,
  `tgl_dibuat`       TIMESTAMP                                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ttd_key`          VARCHAR(20)                                           DEFAULT 'ketua',
  `ttd_nama_kustom`  VARCHAR(100)                                          DEFAULT NULL,
  `ttd_file_kustom`  VARCHAR(255)                                          DEFAULT NULL,
  `status`           ENUM('Draft','Final')                                 DEFAULT 'Final',
  PRIMARY KEY (`id_surat`),
  KEY `id_user_ormawa` (`id_user_ormawa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- FOREIGN KEYS
-- ============================================================

ALTER TABLE `dana`
  ADD CONSTRAINT `dana_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id_pengajuan`) ON DELETE CASCADE;

ALTER TABLE `histori_status`
  ADD CONSTRAINT `fk_histori_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id_pengajuan`) ON DELETE CASCADE;

ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `pengumuman`
  ADD CONSTRAINT `pengumuman_ibfk_1` FOREIGN KEY (`id_user_upload`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `jadwal_rapat`
  ADD CONSTRAINT `jadwal_rapat_ibfk_1` FOREIGN KEY (`id_penyelenggara`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `peminjaman_tempat`
  ADD CONSTRAINT `peminjaman_tempat_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_tempat_ibfk_2` FOREIGN KEY (`id_ruangan`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE CASCADE;

ALTER TABLE `peminjaman_barang`
  ADD CONSTRAINT `peminjaman_barang_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `proposal_otomatis`
  ADD CONSTRAINT `proposal_otomatis_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `proposal_rab`
  ADD CONSTRAINT `proposal_rab_ibfk_1` FOREIGN KEY (`id_proposal`) REFERENCES `proposal_otomatis` (`id_proposal`) ON DELETE CASCADE;

ALTER TABLE `proposal_panitia`
  ADD CONSTRAINT `proposal_panitia_ibfk_1` FOREIGN KEY (`id_proposal`) REFERENCES `proposal_otomatis` (`id_proposal`) ON DELETE CASCADE;

ALTER TABLE `lpj_otomatis`
  ADD CONSTRAINT `lpj_otomatis_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `lpj_anggaran`
  ADD CONSTRAINT `lpj_anggaran_ibfk_1` FOREIGN KEY (`id_lpj`) REFERENCES `lpj_otomatis` (`id_lpj`) ON DELETE CASCADE;

ALTER TABLE `lpj_lampiran`
  ADD CONSTRAINT `lpj_lampiran_ibfk_1` FOREIGN KEY (`id_lpj`) REFERENCES `lpj_otomatis` (`id_lpj`) ON DELETE CASCADE;

ALTER TABLE `surat_otomatis`
  ADD CONSTRAINT `surat_otomatis_ibfk_1` FOREIGN KEY (`id_user_ormawa`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

-- ============================================================
-- TABEL RATE LIMITING (Phase 3 Security)
-- ============================================================

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `ip_address`   VARCHAR(45)  NOT NULL,
  `username`     VARCHAR(100) DEFAULT NULL,
  `attempted_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success`      TINYINT(1)   NOT NULL DEFAULT 0,
  `user_agent`   TEXT         DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_ip_time`   (`ip_address`, `attempted_at`),
  INDEX `idx_user_time` (`username`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
