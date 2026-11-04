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
$lama = mysqli_fetch_assoc(mysqli_stmt_get_result($query));

if (!$lama) {
    set_flash("error", "Data buku tidak ditemukan.");
    header("Location: dashboard.php");
    exit;
}

$data = [
    "judul"    => trim($_POST["judul"] ?? ""),
    "kategori" => $_POST["kategori"] ?? "",
    "penulis"  => trim($_POST["penulis"] ?? ""),
    "tahun"    => (int) ($_POST["tahun"] ?? 0),
    "stok"     => (int) ($_POST["stok"] ?? -1),
];

$errors = validasi_buku($data, $daftar_kategori);
$sampulBaru = null;
if (!$errors) {
    $errUpload = "";
    $sampulBaru = upload_sampul($_FILES["sampul"] ?? null, $errUpload);
    if ($sampulBaru === false) {
        $errors[] = $errUpload;
    }
}

if ($errors) {
    set_flash("error", implode(" ", $errors));
    header("Location: edit_buku.php?id=" . $id);
    exit;
}

// Ada sampul baru: pakai yang baru lalu hapus file lama. Tidak ada: pertahankan yang lama.
$sampulSimpan = $sampulBaru ?? $lama["sampul"];

$query = mysqli_prepare($koneksi, "UPDATE buku SET judul = ?, kategori = ?, penulis = ?, tahun_terbit = ?, stok = ?, sampul = ? WHERE id = ?");
mysqli_stmt_bind_param($query, "sssiisi", $data["judul"], $data["kategori"], $data["penulis"], $data["tahun"], $data["stok"], $sampulSimpan, $id);
mysqli_stmt_execute($query);

if ($sampulBaru !== null) {
    hapus_sampul($lama["sampul"]);
}

set_flash("success", "Data buku berhasil diperbarui.");
header("Location: dashboard.php");
exit;
