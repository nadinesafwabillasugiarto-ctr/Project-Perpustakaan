<?php
/*
 * koneksi.php
 * Koneksi MySQL + fungsi bantu yang dipakai bersama oleh semua halaman
 * (escape output, flash message, validasi, upload file, kerangka HTML).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- 1. Koneksi database ----------
$host     = "localhost";
$username = "root";
$password = "";
$database = "db_perpustakaan";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// ---------- 2. Konstanta & data array ----------
define("FOLDER_UPLOAD", __DIR__ . "/uploads/");
define("MAKS_UKURAN", 2 * 1024 * 1024); // 2 MB

$daftar_kategori = ["Novel", "Teknologi", "Sains", "Sejarah", "Bisnis", "Pengembangan Diri", "Pendidikan", "Lainnya"];

// Warna tab kategori pada kartu katalog (array asosiatif)
$warna_kategori = [
    "Novel" => "#d6336c", "Teknologi" => "#1c7ed6", "Sains" => "#2f9e44", "Sejarah" => "#e8590c",
    "Bisnis" => "#7048e8", "Pengembangan Diri" => "#0c8599", "Pendidikan" => "#f08c00", "Lainnya" => "#868e96",
];

// ---------- 3. Fungsi bantu ----------
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, "UTF-8");
}

function set_flash($tipe, $pesan)
{
    $_SESSION["flash"] = ["tipe" => $tipe, "pesan" => $pesan];
}

// Percabangan if-else untuk label status ketersediaan
function label_status($stok)
{
    if ((int) $stok > 0) {
        return ["teks" => "Tersedia", "kelas" => "cap-tersedia"];
    } else {
        return ["teks" => "Dipinjam", "kelas" => "cap-dipinjam"];
    }
}

// Validasi input buku, mengembalikan array pesan error
function validasi_buku($d, $daftar_kategori)
{
    $err = [];
    if ($d["judul"] === "" || mb_strlen($d["judul"]) > 150) $err[] = "Judul wajib diisi (maks. 150 karakter).";
    if (!in_array($d["kategori"], $daftar_kategori, true))   $err[] = "Kategori tidak valid.";
    if ($d["penulis"] === "" || mb_strlen($d["penulis"]) > 100) $err[] = "Penulis wajib diisi (maks. 100 karakter).";
    if ($d["tahun"] < 1000 || $d["tahun"] > (int) date("Y") + 1) $err[] = "Tahun terbit tidak valid.";
    if ($d["stok"] < 0 || $d["stok"] > 9999)                  $err[] = "Stok harus 0 sampai 9999.";
    return $err;
}

// Upload sampul: return nama file baru, null (tidak ada file), atau false (gagal; pesan di $error)
function upload_sampul($file, &$error)
{
    if (!isset($file) || $file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file["error"] !== UPLOAD_ERR_OK) {
        $error = "Upload gagal (kode " . $file["error"] . ").";
        return false;
    }
    if ($file["size"] > MAKS_UKURAN) {
        $error = "Ukuran sampul maksimal 2 MB.";
        return false;
    }
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, ["jpg", "jpeg", "png"], true)) {
        $error = "Sampul harus berformat .jpg atau .png.";
        return false;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
    if (!in_array($mime, ["image/jpeg", "image/png"], true) || @getimagesize($file["tmp_name"]) === false) {
        $error = "Isi file bukan gambar JPG/PNG yang valid.";
        return false;
    }
    $namaBaru = uniqid() . bin2hex(random_bytes(3)) . "." . ($ext === "jpeg" ? "jpg" : $ext);
    if (!move_uploaded_file($file["tmp_name"], FOLDER_UPLOAD . $namaBaru)) {
        $error = "Gagal menyimpan file ke folder uploads.";
        return false;
    }
    return $namaBaru;
}

function hapus_sampul($nama)
{
    if (!empty($nama) && preg_match('/^[A-Za-z0-9_.-]+$/', $nama)) {
        $path = FOLDER_UPLOAD . $nama;
        if (is_file($path)) {
            unlink($path);
        }
    }
}

// ---------- 4. Kerangka HTML ----------
function tampil_kepala($judul, $dengan_nav = true)
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> | Rak Baca</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;800&family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/project_perpustakaan/css/style.css" rel="stylesheet">
</head>
<body>
<?php if ($dengan_nav): ?>
<header class="bilah">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="merek" href="dashboard.php"><span class="merek-kotak">R</span> Rak Baca</a>
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-sm-inline small">Masuk sebagai <strong><?= e($_SESSION["username"] ?? "") ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-garis"><i class="bi bi-box-arrow-right"></i> Keluar</a>
        </div>
    </div>
</header>
<?php endif;
}

function tampil_kaki()
{
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi hapus
document.querySelectorAll('.form-hapus').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
        ev.preventDefault();
        Swal.fire({
            title: 'Hapus buku ini?',
            text: f.dataset.judul + ' dan file sampulnya akan dihapus permanen.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d6336c', confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
        }).then(function (r) { if (r.isConfirmed) f.submit(); });
    });
});
// Pratinjau sampul + cek ukuran di sisi klien
var inp = document.getElementById('sampul');
if (inp) inp.addEventListener('change', function () {
    var file = this.files[0], img = document.getElementById('pratinjau');
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({icon: 'error', title: 'File terlalu besar', text: 'Maksimal 2 MB.'});
        this.value = ''; return;
    }
    img.src = URL.createObjectURL(file); img.classList.remove('d-none');
});
// Ganti tampilan tabel / katalog di dashboard
document.querySelectorAll('[data-tampil]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var mode = btn.dataset.tampil;
        document.body.dataset.tampil = mode;
        try { localStorage.setItem('tampil', mode); } catch (e) {}
        document.querySelectorAll('[data-tampil]').forEach(function (b) { b.classList.toggle('aktif', b === btn); });
    });
});
(function () {
    var simpan = null;
    try { simpan = localStorage.getItem('tampil'); } catch (e) {}
    var tombol = document.querySelector('[data-tampil="' + (simpan || 'tabel') + '"]');
    if (tombol) tombol.click();
})();
<?php if (!empty($_SESSION["flash"])): $f = $_SESSION["flash"]; unset($_SESSION["flash"]); ?>
Swal.fire({toast: true, position: 'top-end', icon: <?= json_encode($f["tipe"]) ?>, title: <?= json_encode($f["pesan"]) ?>, showConfirmButton: false, timer: 3500, timerProgressBar: true});
<?php endif; ?>
</script>
</body>
</html>
<?php
}

// Form buku dipakai bersama oleh tambah_buku.php dan edit_buku.php
function tampil_form_buku($aksi, $b, $tombol, $daftar_kategori)
{
    ?>
<form action="<?= e($aksi) ?>" method="POST" enctype="multipart/form-data" class="row g-3">
    <?php if (!empty($b["id"])): ?><input type="hidden" name="id" value="<?= (int) $b["id"] ?>"><?php endif; ?>
    <div class="col-12">
        <label for="judul" class="form-label">Judul buku</label>
        <input type="text" class="form-control" id="judul" name="judul" maxlength="150" value="<?= e($b["judul"]) ?>" required>
    </div>
    <div class="col-md-6">
        <label for="kategori" class="form-label">Kategori</label>
        <select class="form-select" id="kategori" name="kategori" required>
            <option value="" disabled <?= $b["kategori"] === "" ? "selected" : "" ?>>Pilih kategori</option>
            <?php foreach ($daftar_kategori as $k): ?>
                <option value="<?= e($k) ?>" <?= $b["kategori"] === $k ? "selected" : "" ?>><?= e($k) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="penulis" class="form-label">Penulis</label>
        <input type="text" class="form-control" id="penulis" name="penulis" maxlength="100" value="<?= e($b["penulis"]) ?>" required>
    </div>
    <div class="col-md-6">
        <label for="tahun" class="form-label">Tahun terbit</label>
        <input type="number" class="form-control" id="tahun" name="tahun" min="1000" max="<?= date("Y") + 1 ?>" value="<?= e($b["tahun_terbit"]) ?>" required>
    </div>
    <div class="col-md-6">
        <label for="stok" class="form-label">Stok (0 = sedang dipinjam)</label>
        <input type="number" class="form-control" id="stok" name="stok" min="0" max="9999" value="<?= e($b["stok"]) ?>" required>
    </div>
    <div class="col-12">
        <label for="sampul" class="form-label">Sampul buku (.jpg / .png, maks. 2 MB)</label>
        <input type="file" class="form-control" id="sampul" name="sampul" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        <?php if (!empty($b["sampul"]) && is_file(FOLDER_UPLOAD . $b["sampul"])): ?>
            <div class="form-text">Sampul saat ini. Kosongkan input jika tidak ingin menggantinya.</div>
            <img src="uploads/<?= e($b["sampul"]) ?>" class="pratinjau mt-2" alt="Sampul saat ini">
        <?php endif; ?>
        <img id="pratinjau" class="pratinjau mt-2 d-none" alt="Pratinjau sampul baru">
    </div>
    <div class="col-12 d-flex gap-2 pt-2">
        <button type="submit" class="btn btn-tinta"><?= e($tombol) ?></button>
        <a href="dashboard.php" class="btn btn-garis-gelap">Batal</a>
    </div>
</form>
<?php
}
