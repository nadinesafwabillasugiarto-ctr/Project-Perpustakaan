<?php
require_once "config/auth.php";
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: tambah_buku.php");
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
$sampul = null;
if (!$errors) {
    $errUpload = "";
    $sampul = upload_sampul($_FILES["sampul"] ?? null, $errUpload);
    if ($sampul === false) {
        $errors[] = $errUpload;
    }
}

if ($errors) {
    $_SESSION["old"] = ["judul" => $data["judul"], "kategori" => $data["kategori"], "penulis" => $data["penulis"],
                        "tahun_terbit" => $data["tahun"], "stok" => $data["stok"], "sampul" => ""];
    set_flash("error", implode(" ", $errors));
    header("Location: tambah_buku.php");
    exit;
}

$query = mysqli_prepare($koneksi, "INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($query, "sssiis", $data["judul"], $data["kategori"], $data["penulis"], $data["tahun"], $data["stok"], $sampul);
mysqli_stmt_execute($query);

set_flash("success", "Buku “" . $data["judul"] . "” berhasil ditambahkan.");
header("Location: dashboard.php");
exit;
