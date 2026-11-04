<?php
require_once "config/auth.php";
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);

$query = mysqli_prepare($koneksi, "SELECT sampul FROM buku WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$buku = mysqli_fetch_assoc(mysqli_stmt_get_result($query));

if (!$buku) {
    set_flash("error", "Buku tidak ditemukan.");
    header("Location: dashboard.php");
    exit;
}

$query = mysqli_prepare($koneksi, "DELETE FROM buku WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    hapus_sampul($buku["sampul"]); // unlink file gambar
    set_flash("success", "Buku berhasil dihapus.");
} else {
    set_flash("error", "Gagal menghapus buku.");
}

header("Location: dashboard.php");
exit;
