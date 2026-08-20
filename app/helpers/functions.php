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
?>
