<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = $user['id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $judul = trim($_POST['judul'] ?? '');
        $periode = $_POST['periode'] ?? '';
        $batas = $_POST['batas_kwh'] ?? '';
        if ($judul === '' || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode) || !is_numeric($batas) || $batas <= 0) {
            $error = 'Judul, periode (bulan), dan batas kWh (lebih dari 0) wajib diisi dengan benar.';
        } else {
            $b = (float)$batas;
            $st = mysqli_prepare($koneksi, "INSERT INTO target (user_id, judul, periode, batas_kwh) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($st, 'issd', $uid, $judul, $periode, $b);
            mysqli_stmt_execute($st);
            flash('success', 'Target berhasil dibuat.');
            redirect('target.php');
        }
    } elseif ($aksi === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        $st = mysqli_prepare($koneksi, "DELETE FROM target WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($st, 'ii', $id, $uid);
        mysqli_stmt_execute($st);
        flash('success', 'Target dihapus.');
        redirect('target.php');
    }
}

// Ambil target beserta realisasi pemakaian pada periodenya
$st = mysqli_prepare($koneksi, "
    SELECT t.*, COALESCE((SELECT SUM(p.kwh) FROM pemakaian p WHERE p.user_id = t.user_id AND DATE_FORMAT(p.tanggal,'%Y-%m') = t.periode), 0) AS realisasi
    FROM target t WHERE t.user_id = ? ORDER BY t.periode DESC, t.id DESC");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$targets = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

$judul_halaman = 'Target Hemat Energi';
$halaman_aktif = 'target';
include __DIR__ . '/includes/header.php';
?>
<h1>Target Hemat Energi</h1>
<p class="muted">Tetapkan batas konsumsi listrik bulanan dan pantau progresnya.</p>

<div class="grid grid-2">
    <div class="card form-card">
        <h2>Buat Target Baru</h2>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" id="formTarget" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="tambah">
            <label>Judul Target
                <input type="text" name="judul" maxlength="120" placeholder="mis. Hemat listrik bulan ini" required>
            </label>
            <label>Periode (Bulan)
                <input type="month" name="periode" value="<?= date('Y-m') ?>" required>
            </label>
            <label>Batas Konsumsi (kWh)
                <input type="number" name="batas_kwh" min="1" step="any" required>
            </label>
            <button class="btn btn-primary" type="submit">Simpan Target</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Target</h2>
        <?php if (!$targets): ?><p class="muted">Belum ada target.</p><?php endif; ?>
        <?php foreach ($targets as $t):
            $pct = $t['batas_kwh'] > 0 ? ($t['realisasi'] / $t['batas_kwh']) * 100 : 0;
            $cls = $pct >= 100 ? 'danger' : ($pct >= 80 ? 'warn' : 'ok');
            $sisa = $t['batas_kwh'] - $t['realisasi'];
            $selesai = $t['periode'] < date('Y-m');
            if ($pct > 100) { $status = 'Melebihi target'; }
            elseif ($selesai) { $status = 'Target tercapai'; }
            else { $status = 'Dalam batas'; }
        ?>
        <div class="target-item">
            <div class="target-head">
                <div>
                    <strong><?= e($t['judul']) ?></strong>
                    <div class="muted small">Periode <?= e($t['periode']) ?></div>
                </div>
                <span class="badge badge-<?= $cls ?>"><?= e($status) ?></span>
            </div>
            <div class="progress"><div class="progress-bar <?= $cls ?>" style="width: <?= min(100, round($pct)) ?>%"></div></div>
            <div class="muted small">
                <?= angka($t['realisasi']) ?> / <?= angka($t['batas_kwh']) ?> kWh (<?= round($pct) ?>%)
                &middot; <?= $sisa >= 0 ? 'Sisa ' . angka($sisa) . ' kWh' : 'Lebih ' . angka(-$sisa) . ' kWh' ?>
            </div>
            <form method="post" class="inline" data-confirm="Hapus target ini?">
                <?= csrf_field() ?>
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
