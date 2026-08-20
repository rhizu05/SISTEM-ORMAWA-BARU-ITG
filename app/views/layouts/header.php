<?php
/**
 * File: header.php
 * Deskripsi: Bagian atas template HTML dengan layout baru yang elegan dan responsif.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Keuangan</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS Kustom Terpusat (design tokens + layout) -->
    <link rel="stylesheet" href="assets/css/app.css">

    <!-- Setel tema sebelum render agar tidak ada flash (FOUC) -->
    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
            
            // Make CSRF token available globally for AJAX requests
            window.CSRF_TOKEN = "<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>";
        })();
    </script>
</head>
<body>

