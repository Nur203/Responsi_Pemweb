<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$in = ['daya' => '', 'jam' => '', 'hari' => 30, 'tarif' => $user['tarif_listrik']];
$hasil = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $in = [
        'daya'  => $_POST['daya'] ?? '',
        'jam'   => $_POST['jam'] ?? '',
        'hari'  => $_POST['hari'] ?? '',
        'tarif' => $_POST['tarif'] ?? '',
    ];
    if (!is_numeric($in['daya']) || $in['daya'] <= 0 || !is_numeric($in['jam']) || $in['jam'] <= 0 || $in['jam'] > 24
        || !is_numeric($in['hari']) || $in['hari'] < 1 || !is_numeric($in['tarif']) || $in['tarif'] < 0) {
        $error = 'Periksa kembali input: semua kolom harus angka valid (durasi maksimal 24 jam/hari).';
    } else {
        $hasil = hitung((float)$in['daya'], (float)$in['jam'], (float)$in['hari'], (float)$in['tarif'], $user['faktor_emisi']);
        $hasil['per_hari'] = $hasil['kwh'] / max(1, (float)$in['hari']);
    }
}

$judul_halaman = 'Kalkulator Konsumsi dan Biaya';
$halaman_aktif = 'kalkulator';
include __DIR__ . '/includes/header.php';
?>
<h1>Kalkulator Konsumsi dan Biaya</h1>
<p class="muted">Hitung konsumsi kWh dan estimasi biaya dari daya, durasi, dan tarif listrik.</p>

<div class="grid grid-2">
    <div class="card form-card">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" id="formKalkulator" novalidate>
            <?= csrf_field() ?>
            <label>Daya Perangkat (Watt)
                <input type="number" name="daya" value="<?= e($in['daya']) ?>" min="1" step="any" required>
            </label>
            <label>Durasi Pemakaian per Hari (jam)
                <input type="number" name="jam" value="<?= e($in['jam']) ?>" min="0.1" max="24" step="any" required>
            </label>
            <label>Jumlah Hari
                <input type="number" name="hari" value="<?= e($in['hari']) ?>" min="1" required>
            </label>
            <label>Tarif Listrik (Rp/kWh)
                <input type="number" name="tarif" value="<?= e($in['tarif']) ?>" min="0" step="any" required>
            </label>
            <button class="btn btn-primary" type="submit">Hitung</button>
        </form>
    </div>
    <div class="card">
        <h2>Hasil Perhitungan</h2>
        <?php if ($hasil): ?>
            <div class="result-list">
                <div><span>Konsumsi per hari</span><strong><?= angka($hasil['per_hari'], 3) ?> kWh</strong></div>
                <div><span>Total konsumsi</span><strong><?= angka($hasil['kwh'], 3) ?> kWh</strong></div>
                <div><span>Estimasi biaya</span><strong><?= rupiah($hasil['biaya']) ?></strong></div>
                <div><span>Estimasi emisi</span><strong><?= angka($hasil['emisi']) ?> kg CO&#8322;</strong></div>
            </div>
            <p class="muted small">Rumus: kWh = (Watt &times; jam &times; hari) / 1000; biaya = kWh &times; tarif.</p>
        <?php else: ?>
            <p class="muted">Isi form lalu tekan <em>Hitung</em> untuk melihat hasil.</p>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
