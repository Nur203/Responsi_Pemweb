<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = $user['id'];

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$st = mysqli_prepare($koneksi, "SELECT * FROM pemakaian WHERE user_id = ? AND nama_perangkat LIKE ? ORDER BY tanggal DESC, id DESC");
mysqli_stmt_bind_param($st, 'is', $uid, $like);
mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

$judul_halaman = 'Data Pemakaian';
$halaman_aktif = 'pemakaian';
include __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Data Pemakaian Energi</h1>
    <a class="btn btn-primary" href="pemakaian_tambah.php">+ Tambah Data</a>
</div>

<form method="get" class="search-form">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama perangkat...">
    <button class="btn" type="submit">Cari</button>
    <?php if ($q !== ''): ?><a class="btn btn-ghost" href="pemakaian.php">Reset</a><?php endif; ?>
</form>

<div class="card table-wrap">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th><th>Perangkat</th><th>Daya (W)</th><th>Jam/hari</th><th>Hari</th>
                <th>kWh</th><th>Biaya</th><th>Emisi (kg CO&#8322;)</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
            <tr><td colspan="9" class="center muted">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($r['tanggal']))) ?></td>
                <td><?= e($r['nama_perangkat']) ?><?php if ($r['catatan']): ?><br><small class="muted"><?= e($r['catatan']) ?></small><?php endif; ?></td>
                <td><?= angka($r['daya_watt'], 0) ?></td>
                <td><?= angka($r['durasi_jam'], 1) ?></td>
                <td><?= (int)$r['jumlah_hari'] ?></td>
                <td><?= angka($r['kwh']) ?></td>
                <td><?= rupiah($r['biaya']) ?></td>
                <td><?= angka($r['emisi']) ?></td>
                <td class="actions">
                    <a class="btn btn-sm" href="pemakaian_ubah.php?id=<?= (int)$r['id'] ?>">Ubah</a>
                    <form method="post" action="pemakaian_hapus.php" class="inline" data-confirm="Hapus data ini?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
