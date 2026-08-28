<?php
$conn = new mysqli('localhost', 'root', '', '', 3306);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Drop & Create DB
$conn->query("DROP DATABASE IF EXISTS db_pengajuan");
$conn->query("CREATE DATABASE db_pengajuan");
$conn->select_db("db_pengajuan");

// Baca isi SQL file
$sql = file_get_contents(dirname(__DIR__) . '/scripts/db_pengajuan.sql');

// Eksekusi multi query
if ($conn->multi_query($sql)) {
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Database db_pengajuan berhasil di-reset dan di-import dengan skema terbaru!\n";
} else {
    echo "Gagal import SQL: " . $conn->error . "\n";
}
$conn->close();
