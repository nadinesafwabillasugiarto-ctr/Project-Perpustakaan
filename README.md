# Rak Baca: Mini Digital Library

Aplikasi web pengelolaan data buku (CRUD) dengan autentikasi, session, cookie, dan upload file sampul.
Dibangun dengan PHP Native, MySQL (mysqli), Bootstrap 5, dan SweetAlert2.

Tugas Terstruktur Mata Kuliah Pemrograman Web, Program Studi Sistem Informasi.

## Fitur

- **Login** dengan form POST dan password ter-hash (`password_hash` / `password_verify`)
- **Session** membatasi akses dashboard dan halaman kelola buku (`config/auth.php`)
- **Remember Me** memakai cookie `remember_username` (30 hari) untuk mengisi otomatis username
- **Logout** menghapus session dan cookie, lalu kembali ke halaman login
- **CRUD buku**: tambah, tampil, edit, hapus, plus pencarian
- **Dua tampilan data**: tabel HTML dan katalog kartu (dapat dipilih di dashboard)
- **Upload sampul** `.jpg/.png`, maksimal 2 MB, divalidasi ekstensi, MIME, dan isi gambar
- **Hapus/ganti sampul** ikut menghapus file lama dari folder `uploads/`
- **Status ketersediaan** dengan percabangan if-else: stok > 0 "Tersedia", stok 0 "Dipinjam"

## Struktur folder

```
project_perpustakaan/
├── config/
│   └── auth.php              # Penjaga session untuk halaman terproteksi
├── setup/
│   ├── buat_admin.php        # Membuat akun admin awal
│   └── PENJELASAN_BUAT_ADMIN.txt
├── screenshots/              # Screenshot untuk README
├── uploads/                  # Penampung file sampul buku
├── koneksi.php               # Koneksi MySQL + fungsi bantu bersama
├── style.css                 # Tampilan aplikasi
├── index.php                 # Halaman login
├── proses_login.php          # Verifikasi login, session, cookie
├── logout.php                # Hapus session dan cookie
├── dashboard.php             # Tampil data (Read)
├── tambah_buku.php           # Form tambah
├── proses_tambah.php         # Insert + upload sampul
├── edit_buku.php             # Form edit
├── proses_edit.php           # Update + ganti sampul
├── hapus_buku.php            # Delete + unlink sampul
└── db_perpustakaan.sql       # Ekspor database
└── README.md      
```

## Cara menjalankan

1. Salin folder `project_perpustakaan` ke `htdocs` (XAMPP).
2. Jalankan Apache dan MySQL.
3. Import `db_perpustakaan.sql` lewat phpMyAdmin (database `db_perpustakaan` dibuat otomatis).
4. Sesuaikan kredensial di `koneksi.php` bila perlu.
5. Buka `http://localhost/project_perpustakaan/`.

**Akun awal:** `admin` / `admin123`

Jika tabel `users` kosong, jalankan `setup/buat_admin.php` sekali lewat browser, lalu hapus file tersebut.

## Struktur database

**Tabel `users`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11), PK, AUTO_INCREMENT | |
| username | varchar(50), UNIQUE | |
| password | varchar(255) | hash bcrypt |

**Tabel `buku`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | int(11), PK, AUTO_INCREMENT | |
| judul | varchar(150) | |
| kategori | varchar(100) | |
| penulis | varchar(100) | |
| tahun_terbit | year(4) | |
| stok | int(11), default 0 | 0 berarti Dipinjam |
| sampul | varchar(255), NULL | nama file di `uploads/` |

## Screenshot

| Halaman | Gambar |
|---|---|
| Login | ![Login](screenshots/1.%20login.png) |
| Dashboard | ![Dashboard](screenshots/2.%20dashboard_2.png) |
| Tambah buku | ![Tambah](screenshots/3.%20Halaman%20Tambah%20Buku.png) |
| Edit buku | ![Edit](screenshots/4.%20Halaman%20Edit%20Buku.png) |
| Hapus Buku | ![Hapus](screenshots/8.%20Hapus%20Buku.png) |
| Upload sampul | ![Upload](screenshots/5.%20Fitur%20Upload%20Sampul%20Buku.png) |
| Struktur tabel database | ![Struktur](screenshots/6.%20Struktur%20Database%20Buku.png) |
| Database Buku | ![Database](screenshots/7.%20Database%20Buku.png) |

## Video demo

[Tonton di YouTube](https://youtu.be/p14FB58CwaQ)

## Link Repository
https://github.com/nadinesafwabillasugiarto-ctr/Project-Perpustakaan
