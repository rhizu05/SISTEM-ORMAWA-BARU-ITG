<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

function start_output_buffering() {
    ob_start();
}

function initialize_session() {
    if (session_status() === PHP_SESSION_NONE) {
        $lifetime = 60 * 60 * 24;
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

function sanitize_input($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    return mysqli_real_escape_string($conn, $data);
}

function redirect($path) {
    ob_end_clean();
    header("Location: " . BASE_URL . '/' . $path);
    exit();
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php?page=login');
    }
}

function check_role($allowed_roles = []) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        show_access_denied();
    }
}

function show_access_denied() {
    http_response_code(403);
    include ROOT_PATH . '/app/views/layouts/header.php';
    include ROOT_PATH . '/app/views/layouts/sidebar.php';
    echo "<div class='main-content-inner'>";
    echo "<div class='container-fluid px-4'>
            <div class='alert alert-danger mt-4'>
                <h4><i class='bi bi-exclamation-triangle-fill'></i> Akses Ditolak</h4>
                <p>Anda tidak memiliki hak akses untuk melihat halaman ini.</p>
                <a href='index.php?page=dashboard' class='btn btn-danger'>Kembali ke Dashboard</a>
            </div>
          </div>";
    echo "</div>";
    include ROOT_PATH . '/app/views/layouts/footer.php';
    exit();
}

function add_history($conn, $id_pengajuan, $id_user, $status, $catatan) {
    $stmt = $conn->prepare(
        "INSERT INTO histori_status (id_pengajuan, id_user, status, catatan) 
         VALUES (?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("isss", $id_pengajuan, $id_user, $status, $catatan);
        return $stmt->execute();
    }
    return false;
}

/**
 * Menyimpan notifikasi ke tabel `notifikasi` (dikonsumsi endpoint AJAX/SSE).
 * @param mysqli $conn  Koneksi database.
 * @param int    $idUser Penerima notifikasi (id_user).
 * @param string $pesan  Isi pesan notifikasi.
 * @return bool
 */
function add_notifikasi($conn, $idUser, $pesan) {
    $stmt = $conn->prepare("INSERT INTO notifikasi (id_user, pesan) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("is", $idUser, $pesan);
        return $stmt->execute();
    }
    return false;
}

/**
 * Membuat notifikasi untuk SEMUA akun aktif dengan role tertentu
 * (misal: memberitahu semua verifikator BEM bahwa ada pengajuan masuk).
 * @param mysqli $conn Koneksi database.
 * @param string $role  Role tujuan ('bem','bpm','bkh','wr3','bendahara').
 * @param string $pesan Isi pesan.
 * @return bool
 */
function notify_role($conn, $role, $pesan) {
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE role = ? AND status_akun = 'aktif'");
    if (!$stmt) { return false; }
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    $ok = true;
    while ($row = $result->fetch_assoc()) {
        if (!add_notifikasi($conn, (int) $row['id_user'], $pesan)) {
            $ok = false;
        }
    }
    return $ok;
}

/* ==========================================================================
   CSRF Protection
   ========================================================================== */

/** Membuat/mengambil token CSRF untuk sesi saat ini. */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Menghasilkan hidden input token CSRF untuk dipasang di form POST. */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/** Memverifikasi token CSRF dari $_POST; abort (419) bila tidak valid. */
function csrf_verify() {
    if (!isset($_POST['csrf_token'])
        || empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(419);
        die('CSRF token tidak valid. Silakan muat ulang halaman dan coba lagi.');
    }
}

/** Validasi MIME file PDF (selain ekstensi). Return true jika valid/fallback. */
function is_valid_pdf($tempPath) {
    if (!function_exists('finfo_open') || !is_uploaded_file($tempPath)) {
        return true;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tempPath);
    finfo_close($finfo);
    return $mime === 'application/pdf';
}

/**
 * Validasi unggapan file unggahan
 * - Cek error upload
 * - Cek ukuran file
 * - Cek MIME type
 * - Sanitize nama file
 * @param array $file Data $_FILES['name']
 * @param array $allowed_types MIME types yang diizinkan
 * @param int $max_size_mb Maksimal ukuran dalam MB
 * @param string $prefix Prefix untuk nama file (opsional)
 * @return array|false ['safe_name' => string, 'extension' => string] atau false jika gagal
 */
function validate_uploaded_file($file, $allowed_types, $max_size_mb, $prefix = '') {
    // 1. Cek upload errors - handle missing key gracefully
    $upload_error = isset($file['error']) ? $file['error'] : UPLOAD_ERR_OK;
    if ($upload_error !== UPLOAD_ERR_OK) {
        return false;
    }

    // 2. Cek ukuran file - handle missing key
    $file_size = isset($file['size']) ? $file['size'] : 0;
    $max_size_bytes = $max_size_mb * 1024 * 1024;
    if ($file_size > $max_size_bytes) {
        return false;
    }

    // 3. Cek apakah file diupload via HTTP POST
    $tmp_name = isset($file['tmp_name']) ? $file['tmp_name'] : null;
    if (!is_uploaded_file($tmp_name)) {
        return false;
    }

    // 4. Dapatkan ekstensi dan MIME type - handle missing key
    $file_name = isset($file['name']) ? $file['name'] : '';
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? ($finfo ? finfo_file($finfo, $tmp_name) : '') : '';
    finfo_close($finfo);

    // 5. Validasi MIME type
    $allowed = array_map('strtolower', $allowed_types);
    $ext_lower = strtolower($ext);
    $ext_matches = !empty($ext) && in_array($ext_lower, array_map('strtolower', array_keys($allowed_types)));
    $mime_matches = !empty($mime) && in_array(strtolower($mime), $allowed);
    if (!($ext_matches || $mime_matches)) {
        return false;
    }

    // 6. Sanitize nama file
    $safe_name = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '', $safe_name);

    // 7. Return info sukses
    return [
        'safe_name' => $safe_name,
        'extension' => $ext,
        'mime' => $mime,
        'size' => $file_size,
        'tmp_name' => $tmp_name
    ];
}
?>
