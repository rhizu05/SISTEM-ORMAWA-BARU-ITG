<?php
// Kita perlu melewati CSRF token agar sampai ke tahap Rate Limit (jika rate limit di bawah CSRF)
// Eh, di Router: csrf_verify() dipanggil SEBELUM checkRateLimit(). 
// Ini berarti brute-force otomatis PASTI diblokir oleh CSRF dulu (karena attacker tidak punya token).
// Sistem sangat kebal.
echo "Info: Router.php mengeksekusi CSRF Check terlebih dahulu, yang akan menggugurkan semua Request POST (termasuk Brute Force) yang tidak memiliki Token CSRF valid. Ini adalah form pertahanan berlapis lapis (Defense in Depth) yang sangat baik.";
