<?php
require_once "koneksi.php";

if (isset($_SESSION["login"])) {
    header("Location: dashboard.php");
    exit;
}

$username_cookie = $_COOKIE["remember_username"] ?? "";
tampil_kepala("Masuk", false);
?>
<div class="login-halaman">
    <section class="login-kiri">
        <p class="merek"><span class="merek-kotak">R</span> Rak Baca</p>
        <h1>Rapikan rak bukunya, simpan setiap kisahnya.</h1>
        <p class="login-sub">Kelola katalog perpustakaan mini dengan mudah: tambah, perbarui, dan pantau ketersediaan buku dalam satu layar.</p>
    </section>
    <section class="login-kanan">
        <form action="proses_login.php" method="POST" class="kartu-anggota">
            <div class="lubang" aria-hidden="true"></div>
            <h2 style="text-align: center;">Gerbang Pustaka</h2>
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control garis-bawah" id="username" name="username" value="<?= e($username_cookie) ?>" required autofocus>
            <label for="password" class="form-label mt-3">Password</label>
            <input type="password" class="form-control garis-bawah" id="password" name="password" required>
            <div class="form-check my-4">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" <?= $username_cookie ? "checked" : "" ?>>
                <label class="form-check-label" for="remember">Ingat username saya</label>
            </div>
            <button type="submit" class="btn btn-tinta w-100">Mulai Membaca</button>
        </form>
    </section>
</div>
<?php tampil_kaki(); ?>
