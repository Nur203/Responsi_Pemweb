<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = $user['id'];
$bulan = date('Y-m');

// Total keseluruhan
$st = mysqli_prepare($koneksi, "SELECT COALESCE(SUM(kwh),0) kwh, COALESCE(SUM(biaya),0) biaya, COALESCE(SUM(emisi),0) emisi, COUNT(*) n FROM pemakaian WHERE user_id = ?");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

// Bulan ini
$st = mysqli_prepare($koneksi, "SELECT COALESCE(SUM(kwh),0) kwh, COALESCE(SUM(biaya),0) biaya, COALESCE(SUM(emisi),0) emisi FROM pemakaian WHERE user_id = ? AND DATE_FORMAT(tanggal,'%Y-%m') = ?");
mysqli_stmt_bind_param($st, 'is', $uid, $bulan);
mysqli_stmt_execute($st);
$bln = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

// Perangkat terboros
$st = mysqli_prepare($koneksi, "SELECT nama_perangkat, SUM(kwh) kwh FROM pemakaian WHERE user_id = ? GROUP BY nama_perangkat ORDER BY kwh DESC LIMIT 5");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$top = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

// Tren 6 bulan terakhir
$tren = [];
for ($i = 5; $i >= 0; $i--) {
    $tren[date('Y-m', strtotime("first day of -$i month"))] = 0;
}
$st = mysqli_prepare($koneksi, "SELECT DATE_FORMAT(tanggal,'%Y-%m') p, SUM(kwh) kwh FROM pemakaian WHERE user_id = ? AND tanggal >= DATE_SUB(DATE_FORMAT(CURDATE(),'%Y-%m-01'), INTERVAL 5 MONTH) GROUP BY p");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$r = mysqli_stmt_get_result($st);
while ($row = mysqli_fetch_assoc($r)) {
    if (isset($tren[$row['p']])) $tren[$row['p']] = (float)$row['kwh'];
}

// Target aktif bulan ini
$st = mysqli_prepare($koneksi, "SELECT * FROM target WHERE user_id = ? AND periode = ? ORDER BY id DESC LIMIT 1");
mysqli_stmt_bind_param($st, 'is', $uid, $bulan);
mysqli_stmt_execute($st);
$target = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

// Pohon setara: 1 pohon menyerap sekitar 21 kg CO2 per tahun
$pohon = $total['emisi'] / 21;

$judul_halaman = 'Dashboard';
$halaman_aktif = 'index';
include __DIR__ . '/includes/header.php';
?>
<h1>Dashboard</h1>
<p class="muted">Halo, <?= e($user['nama']) ?>. Berikut pemakaian listrikmu pada <?= e(nama_bulan($bulan)) ?>.</p>

<section class="hero">
    <div class="readout">
        <h2>Pemakaian listrik bulan ini</h2>
        <div class="readout-row">
            <?= meter($bln['kwh']) ?>
            <span class="readout-unit">kWh</span>
        </div>
        <p class="readout-note">Sejak mulai mencatat: <?= angka($total['kwh']) ?> kWh dari <?= (int)$total['n'] ?> catatan</p>
    </div>
    <dl class="figures">
        <div>
            <dt>Estimasi biaya bulan ini</dt>
            <dd><?= rupiah($bln['biaya']) ?></dd>
            <div class="sub">Total sejak mulai: <?= rupiah($total['biaya']) ?></div>
        </div>
        <div>
            <dt>Emisi karbon bulan ini</dt>
            <dd><?= angka($bln['emisi']) ?> <small>kg CO&#8322;</small></dd>
            <div class="sub">Total sejak mulai: <?= angka($total['emisi']) ?> kg CO&#8322;</div>
        </div>
    </dl>
</section>

<section class="grid grid-2">
    <div class="card">
        <h2>Pemakaian 6 bulan terakhir (kWh)</h2>
        <canvas id="chartTren" height="230" role="img" aria-label="Grafik batang pemakaian listrik 6 bulan terakhir"
                data-labels='<?= e(json_encode(array_keys($tren))) ?>'
                data-values='<?= e(json_encode(array_values($tren))) ?>'></canvas>
    </div>
    <div class="card">
        <h2>Target bulan ini</h2>
        <?php if ($target):
            $pct = $target['batas_kwh'] > 0 ? ($bln['kwh'] / $target['batas_kwh']) * 100 : 0;
            $cls = $pct >= 100 ? 'danger' : ($pct >= 80 ? 'warn' : 'ok');
        ?>
            <p><strong><?= e($target['judul']) ?></strong></p>
            <div class="progress" role="progressbar" aria-valuenow="<?= min(100, round($pct)) ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar <?= $cls ?>" style="width: <?= min(100, round($pct)) ?>%"></div></div>
            <p class="muted"><?= angka($bln['kwh']) ?> dari batas <?= angka($target['batas_kwh']) ?> kWh (<?= round($pct) ?>%)</p>
        <?php else: ?>
            <p class="muted">Kamu belum menetapkan batas pemakaian untuk bulan ini.</p>
            <a class="btn btn-primary" href="target.php">Buat target</a>
        <?php endif; ?>
        <hr>
        <p class="muted">Total emisimu setara dengan serapan karbon sekitar <strong><?= angka($pohon, 1) ?></strong> pohon selama setahun.</p>
    </div>
</section>

<section class="card">
    <h2>Perangkat paling boros</h2>
    <?php if ($top): $maks = max(array_column($top, 'kwh')); ?>
        <?php foreach ($top as $t): ?>
            <div class="bar-row">
                <span class="bar-name"><?= e($t['nama_perangkat']) ?></span>
                <div class="bar-track"><div class="bar-fill" style="width: <?= $maks > 0 ? round($t['kwh'] / $maks * 100) : 0 ?>%"></div></div>
                <span class="bar-val"><?= angka($t['kwh']) ?> kWh</span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="muted">Belum ada data. <a href="pemakaian_tambah.php">Tambah pemakaian pertamamu</a> untuk mulai melihat ringkasan.</p>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
