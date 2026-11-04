<?php
require_once "config/auth.php";
require_once "koneksi.php";

// Pencarian (GET)
$cari = trim($_GET["cari"] ?? "");
if ($cari !== "") {
    $like = "%" . $cari . "%";
    $query = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE judul LIKE ? OR penulis LIKE ? OR kategori LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($query, "sss", $like, $like, $like);
    mysqli_stmt_execute($query);
    $hasil = mysqli_stmt_get_result($query);
} else {
    $hasil = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id DESC");
}
$daftar_buku = mysqli_fetch_all($hasil, MYSQLI_ASSOC);

$ringkas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total, COALESCE(SUM(stok > 0), 0) AS ada FROM buku"));
$total = (int) $ringkas["total"];
$ada = (int) $ringkas["ada"];
$dipinjam = $total - $ada;

tampil_kepala("Dashboard");
?>
<main class="container py-4">
    <section class="strip">
        <div><span class="strip-angka"><?= $total ?></span> judul tercatat</div>
        <div><span class="strip-angka"><?= $ada ?></span> siap dipinjam</div>
        <div><span class="strip-angka"><?= $dipinjam ?></span> sedang keluar</div>
    </section>

    <div class="toolbar">
        <h1>Koleksi buku</h1>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="search" name="cari" class="form-control" placeholder="Cari judul, penulis, kategori" value="<?= e($cari) ?>" aria-label="Cari buku">
                <button class="btn btn-garis-gelap" aria-label="Cari"><i class="bi bi-search"></i></button>
            </form>
            <div class="saklar" role="group" aria-label="Pilih tampilan">
                <button type="button" data-tampil="tabel" title="Tampilan tabel"><i class="bi bi-table"></i></button>
                <button type="button" data-tampil="katalog" title="Tampilan katalog"><i class="bi bi-grid"></i></button>
            </div>
            <a href="tambah_buku.php" class="btn btn-tinta"><i class="bi bi-plus-lg"></i> Tambah buku</a>
        </div>
    </div>

    <?php if (!$daftar_buku): ?>
        <div class="kosong">
            <?= $cari !== "" ? "Tidak ada buku yang cocok dengan “" . e($cari) . "”." : "Rak masih kosong. Tambahkan buku pertama Anda." ?>
        </div>
    <?php else: ?>

    <!-- Tampilan 1: tabel HTML -->
    <div class="tampil-tabel table-responsive">
        <table class="table tabel-buku">
            <thead>
                <tr><th>No</th><th>Sampul</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tahun</th><th>Stok</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($daftar_buku as $i => $b): $s = label_status($b["stok"]); ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?php if ($b["sampul"] && is_file(FOLDER_UPLOAD . $b["sampul"])): ?>
                            <img class="mini-sampul" src="uploads/<?= e($b["sampul"]) ?>" alt="Sampul <?= e($b["judul"]) ?>">
                        <?php else: ?>
                            <div class="mini-sampul kosong-sampul"><i class="bi bi-book"></i></div>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold"><?= e($b["judul"]) ?></td>
                    <td><?= e($b["kategori"]) ?></td>
                    <td><?= e($b["penulis"]) ?></td>
                    <td><?= e($b["tahun_terbit"]) ?></td>
                    <td><?= (int) $b["stok"] ?></td>
                    <td><span class="cap <?= $s["kelas"] ?>"><?= $s["teks"] ?></span></td>
                    <td class="text-end text-nowrap">
                        <a href="edit_buku.php?id=<?= (int) $b["id"] ?>" class="btn btn-sm btn-garis-gelap"><i class="bi bi-pencil"></i> Edit</a>
                        <form action="hapus_buku.php" method="POST" class="d-inline form-hapus" data-judul="<?= e($b["judul"]) ?>">
                            <input type="hidden" name="id" value="<?= (int) $b["id"] ?>">
                            <button class="btn btn-sm btn-bahaya"><i class="bi bi-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tampilan 2: katalog kartu -->
    <div class="tampil-katalog">
        <?php foreach ($daftar_buku as $b): $s = label_status($b["stok"]); $warna = $warna_kategori[$b["kategori"]] ?? "#868e96"; ?>
        <article class="kartu-katalog" style="--tab: <?= e($warna) ?>">
            <div class="tab-kategori"><?= e($b["kategori"]) ?></div>
            <div class="isi-kartu">
                <?php if ($b["sampul"] && is_file(FOLDER_UPLOAD . $b["sampul"])): ?>
                    <img class="sampul-besar" src="uploads/<?= e($b["sampul"]) ?>" alt="Sampul <?= e($b["judul"]) ?>">
                <?php else: ?>
                    <div class="sampul-besar kosong-sampul"><i class="bi bi-book"></i></div>
                <?php endif; ?>
                <div class="teks-kartu">
                    <h3><?= e($b["judul"]) ?></h3>
                    <p><?= e($b["penulis"]) ?>, <?= e($b["tahun_terbit"]) ?></p>
                    <p class="small">Stok: <?= (int) $b["stok"] ?></p>
                    <span class="cap <?= $s["kelas"] ?>"><?= $s["teks"] ?></span>
                </div>
            </div>
            <div class="aksi-kartu">
                <a href="edit_buku.php?id=<?= (int) $b["id"] ?>" class="btn btn-sm btn-garis-gelap"><i class="bi bi-pencil"></i> Edit</a>
                <form action="hapus_buku.php" method="POST" class="d-inline form-hapus" data-judul="<?= e($b["judul"]) ?>">
                    <input type="hidden" name="id" value="<?= (int) $b["id"] ?>">
                    <button class="btn btn-sm btn-bahaya"><i class="bi bi-trash"></i> Hapus</button>
                </form>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
<?php tampil_kaki(); ?>
