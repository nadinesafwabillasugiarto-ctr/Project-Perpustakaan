<?php
require_once "config/auth.php";
require_once "koneksi.php";

$id = (int) ($_GET["id"] ?? 0);
$query = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$b = mysqli_fetch_assoc(mysqli_stmt_get_result($query));

if (!$b) {
    set_flash("error", "Data buku tidak ditemukan.");
    header("Location: dashboard.php");
    exit;
}

tampil_kepala("Edit buku");
?>
<main class="container py-4 halaman-form">
    <a href="dashboard.php" class="tautan-balik"><i class="bi bi-arrow-left"></i> Kembali ke koleksi</a>
    <div class="lembar">
        <h1>Edit buku</h1>
        <?php tampil_form_buku("proses_edit.php", $b, "Simpan perubahan", $daftar_kategori); ?>
    </div>
</main>
<?php tampil_kaki(); ?>
