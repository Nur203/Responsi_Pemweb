<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = $user['id'];

$mode = ($_GET['mode'] ?? 'bulan') === 'hari' ? 'hari' : 'bulan';
$format = $mode === 'hari' ? '%Y-%m-%d' : '%Y-%m';
$limit = $mode === 'hari' ? 30 : 12;

$sql = "SELECT DATE_FORMAT(tanggal,'$format') periode, SUM(kwh) kwh, SUM(biaya) biaya, SUM(emisi) emisi, COUNT(*) n
        FROM pemakaian WHERE user_id = ? GROUP BY periode ORDER BY periode DESC LIMIT $limit";
$st = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
$chart = array_reverse($rows);

$judul_halaman = 'Riwayat Pemakaian';
$halaman_aktif = 'riwayat';
include __DIR__ . '/includes/header.php';
?>
<div class="page-head">
    <h1>Riwayat Pemakaian</h1>
    <div class="tabs">
        <a class="<?= $mode === 'bulan' ? 'active' : '' ?>" href="riwayat.php?mode=bulan">Per Bulan</a>
        <a class="<?= $mode === 'hari' ? 'active' : '' ?>" href="riwayat.php?mode=hari">Per Hari</a>
    </div>
</div>

<div class="card">
    <h2>Grafik Konsumsi (kWh)</h2>
    <?php if ($chart): ?>
    <canvas id="chartRiwayat" height="260"
            data-labels='<?= e(json_encode(array_column($chart, 'periode'))) ?>'
            data-values='<?= e(json_encode(array_map('floatval', array_column($chart, 'kwh')))) ?>'></canvas>
    <?php else: ?>
        <p class="muted">Belum ada data untuk ditampilkan.</p>
    <?php endif; ?>
</div>

<div class="card table-wrap">
    <table>
        <thead><tr><th>Periode</th><th>Jumlah Catatan</th><th>kWh</th><th>Biaya</th><th>Emisi (kg CO&#8322;)</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
            <tr><td colspan="5" class="center muted">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['periode']) ?></td>
                <td><?= (int)$r['n'] ?></td>
                <td><?= angka($r['kwh']) ?></td>
                <td><?= rupiah($r['biaya']) ?></td>
                <td><?= angka($r['emisi']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
