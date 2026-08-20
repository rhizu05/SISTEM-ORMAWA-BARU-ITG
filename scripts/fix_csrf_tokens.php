<?php
/**
 * CSRF Form Fix Script - Based on previous grep analysis
 * 
 * This script adds CSRF tokens to all POST forms identified in the grep analysis.
 */

define('APP_RUNNING', true);
define('ROOT_PATH', __DIR__);

// List of POST forms identified from grep analysis
$forms_to_fix = [
    // Critical forms - HIGH PRIORITY
    ['file' => 'app/views/auth/login.php', 'line' => 285, 'description' => 'Login form - CRITICAL'],
    
    // User management forms
    ['file' => 'app/views/admin/atur_saldo.php', 'line' => 49, 'description' => 'Set user balance'],
    ['file' => 'app/views/admin/atur_sistem.php', 'line' => 217, 'description' => 'System settings'],
    ['file' => 'app/views/admin/edit_user.php', 'line' => 50, 'description' => 'Edit user'],
    ['file' => 'app/views/admin/tambah_user.php', 'line' => 32, 'description' => 'Add user'],
    
    // Verification forms
    ['file' => 'app/views/verifikator/arsip_surat.php', 'line' => 108, 'description' => 'Archive delete'],
    ['file' => 'app/views/verifikator/arsip_surat.php', 'line' => 168, 'description' => 'Input nomor surat'],
    ['file' => 'app/views/verifikator/ajukan_pencairan.php', 'line' => 123, 'description' => 'Submit for disbursement'],
    ['file' => 'app/views/verifikator/buat_surat_peringatan.php', 'line' => 56, 'description' => 'Create warning letter'],
    ['file' => 'app/views/verifikator/manage_aspirasi.php', 'line' => 60, 'description' => 'Manage aspirations'],
    ['file' => 'app/views/verifikator/manage_regulasi.php', 'line' => 121, 'description' => 'Manage regulations'],
    ['file' => 'app/views/verifikator/verifikasi.php', 'line' => 143, 'description' => 'Verify proposal'],
    ['file' => 'app/views/verifikator/verifikasi_lpj.php', 'line' => 89, 'description' => 'Verify LPJ'],
    ['file' => 'app/views/verifikator/verifikasi_barang_bkkh.php', 'line' => 87, 'description' => 'Verify items (BKKH)'],
    ['file' => 'app/views/verifikator/verifikasi_tempat.php', 'line' => 128, 'description' => 'Verify place'],
    
    // Bendahara forms
    ['file' => 'app/views/bendahara/proses.php', 'line' => 70, 'description' => 'Process disbursement'],
    ['file' => 'app/views/bendahara/verifikasi_lpj.php', 'line' => 93, 'description' => 'Verify LPJ (Bendahara)'],
    
    // Sarpras forms
    ['file' => 'app/views/sarpras/manage_barang.php', 'line' => 25, 'description' => 'Manage items'],
    ['file' => 'app/views/sarpras/verifikasi_barang.php', 'line' => 130, 'description' => 'Verify items'],
    ['file' => 'app/views/sarpras/verifikasi_ruangan.php', 'line' => 97, 'description' => 'Verify rooms'],
    
    // Ormawa forms
    ['file' => 'app/views/ormawa/buat_lpj_otomatis.php', 'line' => 103, 'description' => 'Create LPJ automatically'],
    ['file' => 'app/views/ormawa/buat_proposal.php', 'line' => 103, 'description' => 'Create proposal'],
    ['file' => 'app/views/ormawa/buat_surat_lain.php', 'line' => 82, 'description' => 'Create other letters'],
    ['file' => 'app/views/ormawa/detail.php', 'line' => 316, 'description' => 'Follow-up form'],
    ['file' => 'app/views/ormawa/edit.php', 'line' => 80, 'description' => 'Edit proposal'],
    ['file' => 'app/views/ormawa/edit_proposal.php', 'line' => 107, 'description' => 'Edit proposal (alternate)'],
    ['file' => 'app/views/ormawa/jadwal_rapat.php', 'line' => 89, 'description' => 'Delete meeting schedule'],
    ['file' => 'app/views/ormawa/jadwal_rapat.php', 'line' => 119, 'description' => 'Add meeting schedule'],
    ['file' => 'app/views/ormawa/peminjaman_barang.php', 'line' => 125, 'description' => 'Borrow items'],
    ['file' => 'app/views/ormawa/peminjaman_tempat.php', 'line' => 88, 'description' => 'Borrow place'],
    ['file' => 'app/views/ormawa/pengumuman.php', 'line' => 40, 'description' => 'Delete announcement'],
    ['file' => 'app/views/ormawa/pengumuman.php', 'line' => 119, 'description' => 'Add announcement'],
    ['file' => 'app/views/ormawa/pusat_informasi.php', 'line' => 75, 'description' => 'Delete news'],
    ['file' => 'app/views/ormawa/pusat_informasi.php', 'line' => 210, 'description' => 'Add news'],
    ['file' => 'app/views/ormawa/revisi_lpj.php', 'line' => 74, 'description' => 'Revise LPJ'],
    ['file' => 'app/views/ormawa/tambah_pengajuan.php', 'line' => 221, 'description' => 'Add submission'],
    ['file' => 'app/views/ormawa/upload_lpj.php', 'line' => 129, 'description' => 'Upload LPJ'],
    
    // Shared forms
    ['file' => 'app/views/shared/aspirasi_publik.php', 'line' => 82, 'description' => 'Public aspiration'],
    ['file' => 'app/views/shared/profil.php', 'line' => 40, 'description' => 'Profile update'],
];

echo "========================================\n";
echo "CSRF Form Fix Script\n";
echo "========================================\n\n";

echo "Total forms to fix: " . count($forms_to_fix) . "\n\n";

$fixed_count = 0;
$already_fixed_count = 0;
$errors = [];

foreach ($forms_to_fix as $form) {
    $file_path = $form['file'];
    
    if (!file_exists($file_path)) {
        $errors[] = "File not found: " . $form['file'];
        continue;
    }
    
    $content = file_get_contents($file_path);
    $lines = explode("\n", $content);
    
    $line_index = $form['line'] - 1; // Convert to 0-index
    
    if (!isset($lines[$line_index])) {
        $errors[] = "Line " . $form['line'] . " not found in " . $form['file'];
        continue;
    }
    
    // Check if this line already has CSRF token
    $has_csrf = false;
    for ($i = max(0, $line_index); $i < min(count($lines), $line_index + 5); $i++) {
        if (strpos($lines[$i], '<?php echo csrf_field(); ?>') !== false ||
            strpos($lines[$i], "<?php echo csrf_field(); ?>") !== false) {
            $has_csrf = true;
            break;
        }
    }
    
    if ($has_csrf) {
        echo "✓ Already has CSRF: " . $form['file'] . " (Line " . $form['line'] . ")\n";
        $already_fixed_count++;
    } else {
        // Add CSRF token after the form opening tag
        $lines[$line_index] = $lines[$line_index] . "\n    <?php echo csrf_field(); ?>";
        
        // Write back to file
        if (file_put_contents($file_path, implode("\n", $lines))) {
            echo "✓ Fixed: " . $form['file'] . " (Line " . $form['line'] . ") - " . $form['description'] . "\n";
            $fixed_count++;
        } else {
            $errors[] = "Failed to write: " . $form['file'];
        }
    }
}

echo "\n========================================\n";
echo "SUMMARY:\n";
echo "----------------------------------------\n";
echo "Total forms processed: " . count($forms_to_fix) . "\n";
echo "Forms already had CSRF: " . $already_fixed_count . "\n";
echo "Forms fixed: " . $fixed_count . "\n";
echo "Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\nERRORS:\n";
    echo "----------------------------------------\n";
    foreach ($errors as $error) {
        echo "• " . $error . "\n";
    }
}

echo "\n========================================\n";
echo "NOTE: Some forms may already have CSRF protection:\n";
echo "1. app/views/verifikator/verifikasi.php:143 (has csrf_field on line 144)\n";
echo "2. app/views/verifikator/verifikasi_lpj.php:89 (has csrf_field on line 90)\n";
echo "3. app/views/bendahara/proses.php:70 (has csrf_field on line 71)\n";
echo "========================================\n";