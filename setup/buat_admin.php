<?php
// Jalankan sekali lewat browser: http://localhost/project_perpustakaan/setup/buat_admin.php
// Setelah akun terbuat, hapus atau nonaktifkan file ini.

include __DIR__ . "/../koneksi.php";

$username = "admin";
$password = "admin123";
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Cegah akun ganda
$cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($cek, "s", $username);
mysqli_stmt_execute($cek);
if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
    die("Akun admin sudah ada.");
}

$query = mysqli_prepare($koneksi, "INSERT INTO users (username, password) VALUES (?, ?)");
mysqli_stmt_bind_param($query, "ss", $username, $passwordHash);

if (mysqli_stmt_execute($query)) {
    echo "Akun admin berhasil dibuat!";
} else {
    echo "Gagal membuat akun: " . mysqli_error($koneksi);
}
