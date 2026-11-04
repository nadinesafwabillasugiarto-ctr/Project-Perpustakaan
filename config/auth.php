<?php
// Penjaga halaman: hanya pengguna yang sudah login yang boleh lanjut.
// Dipanggil dengan require_once "config/auth.php"; di awal halaman terproteksi.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["login"])) {
    $_SESSION["flash"] = ["tipe" => "warning", "pesan" => "Silakan login terlebih dahulu."];
    header("Location: index.php");
    exit;
}
