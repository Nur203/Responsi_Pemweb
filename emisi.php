<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$in = ['kwh' => '', 'faktor' => $user['faktor_emisi']];
$hasil = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $in = ['kwh' => $_POST['kwh'] ?? '', 'faktor' => $_POST['faktor'] ?? ''];
    if (!is_numeric($in['kwh']) || $in['kwh'] <= 0 || !is_numeric($in['faktor']) || $in['faktor'] < 0) {
        $error = 'Konsumsi dan faktor emisi harus berupa angka valid.';
    } else {
        $emisi = (float)$in['kwh'] * (float)$in['faktor'];
        $hasil = [
            'emisi' => $emisi,
            'pohon' => $emisi / 21,        // 1 pohon menyerap sekitar 21 kg CO2 per tahun
            'km'    => $emisi / 0.12,      // mobil sekitar 0,12 kg CO2 per km
        ];
    }
}

$judul_halaman = 'Kalkulator Emisi Karbon';
$halaman_aktif = 'emisi';
include __DIR__ . '/includes/header.php';
?>
<h1>Kalkulator Emisi Karbon</h1>
<p class="muted">Estimasi emisi CO&#8322; dari konsumsi listrik (kWh).</p>

<div class="grid grid-2">
    <div class="card form-card">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" id="formEmisi" novalidate>
            <?= csrf_field() ?>
            <label>Konsumsi Listrik (kWh)
                <input type="number" name="kwh" value="<?= e($in['kwh']) ?>" min="0.001" step="any" required>
            </label>
            <label>Faktor Emisi (kg CO&#8322;/kWh)
                <input type="number" name="faktor" value="<?= e($in['faktor']) ?>" min="0" step="any" required>
            </label>
            <button class="btn btn-primary" type="submit">Hitung Emisi</button>
        </form>
    </div>
    <div class="card">
        <h2>Hasil Perhitungan</h2>
        <?php if ($hasil): ?>
            <div class="result-list">
                <div><span>Estimasi emisi</span><strong><?= angka($hasil['emisi']) ?> kg CO&#8322;</strong></div>
                <div><span>Setara penyerapan pohon (1 tahun)</span><strong><?= angka($hasil['pohon'], 1) ?> pohon</strong></div>
                <div><span>Setara perjalanan mobil</span><strong><?= angka($hasil['km'], 0) ?> km</strong></div>
            </div>
            <p class="muted small">Rumus: emisi = kWh &times; faktor emisi. Angka kesetaraan bersifat pendekatan (1 pohon &asymp; 21 kg CO&#8322;/tahun; mobil &asymp; 0,12 kg CO&#8322;/km).</p>
        <?php else: ?>
            <p class="muted">Masukkan konsumsi kWh untuk melihat estimasi emisinya.</p>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
