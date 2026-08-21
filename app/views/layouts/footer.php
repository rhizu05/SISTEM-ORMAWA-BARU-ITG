<?php
/**
 * File: footer.php
 * Deskripsi: Bagian bawah template HTML dengan JavaScript untuk sidebar interaktif.
 */
$nama_aplikasi = $nama_aplikasi ?? 'SI-Keuangan';
?>
 <div class="card-footer text-center text-secondary small">
            &copy; <?php echo date('Y'); ?>  <span ><?php echo $nama_aplikasi; ?></span> Institut Teknologi Garut
        </div>
        </div> <!-- Menutup .main-content-inner -->
    </div> <!-- Menutup .content-wrapper -->
</div> <!-- Menutup .page-wrapper -->

<!-- Bootstrap 5 JS Bundle (termasuk Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Kustom Terpusat (sidebar, tema, notifikasi) -->
<script src="assets/js/app.js"></script>

<!-- Dashboard Analytics -->
<script src="assets/js/dashboard.js"></script>

</body>
</html>

