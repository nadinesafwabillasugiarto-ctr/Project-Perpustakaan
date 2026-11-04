<?php
require_once "config/auth.php";
require_once "koneksi.php";

// Isi ulang form jika validasi sebelumnya gagal
$b = $_SESSION["old"] ?? ["judul" => "", "kategori" => "", "penulis" => "", "tahun_terbit" => "", "stok" => 1, "sampul" => ""];
unset($_SESSION["old"]);

tampil_kepala("Tambah buku");
?>
<main class="container py-4 halaman-form">
    <a href="dashboard.php" class="tautan-balik"><i class="bi bi-arrow-left"></i> Kembali ke koleksi</a>
    <div class="lembar">
        <h1>Tambah buku</h1>
        <?php tampil_form_buku("proses_tambah.php", $b, "Simpan buku", $daftar_kategori); ?>
    </div>
</main>
<?php tampil_kaki(); ?>
