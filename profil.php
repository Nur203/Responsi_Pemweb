<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = $user['id'];
$errProfil = $errPass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'profil') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tarif = $_POST['tarif_listrik'] ?? '';
        $faktor = $_POST['faktor_emisi'] ?? '';

        if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errProfil = 'Nama wajib diisi dan email harus valid.';
        } elseif (!is_numeric($tarif) || $tarif < 0 || !is_numeric($faktor) || $faktor < 0) {
            $errProfil = 'Tarif dan faktor emisi harus angka tidak negatif.';
        } else {
            $st = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ? AND id <> ?");
            mysqli_stmt_bind_param($st, 'si', $email, $uid);
            mysqli_stmt_execute($st);
            mysqli_stmt_store_result($st);
            if (mysqli_stmt_num_rows($st) > 0) {
                $errProfil = 'Email sudah dipakai akun lain.';
            } else {
                $t = (float)$tarif; $f = (float)$faktor;
                $st = mysqli_prepare($koneksi, "UPDATE users SET nama=?, email=?, tarif_listrik=?, faktor_emisi=? WHERE id=?");
                mysqli_stmt_bind_param($st, 'ssddi', $nama, $email, $t, $f, $uid);
                mysqli_stmt_execute($st);

                // Hitung ulang data lama agar konsisten dengan tarif dan faktor baru
                if (isset($_POST['hitung_ulang'])) {
                    $st = mysqli_prepare($koneksi, "UPDATE pemakaian SET biaya = kwh * ?, emisi = kwh * ? WHERE user_id = ?");
                    mysqli_stmt_bind_param($st, 'ddi', $t, $f, $uid);
                    mysqli_stmt_execute($st);
                }
                flash('success', 'Profil dan pengaturan berhasil disimpan.');
                redirect('profil.php');
            }
        }
    } elseif ($aksi === 'password') {
        $lama = $_POST['password_lama'] ?? '';
        $baru = $_POST['password_baru'] ?? '';
        $konf = $_POST['password_konf'] ?? '';
        if (!password_verify($lama, $user['password'])) {
            $errPass = 'Password lama salah.';
        } elseif (strlen($baru) < 6) {
            $errPass = 'Password baru minimal 6 karakter.';
        } elseif ($baru !== $konf) {
            $errPass = 'Konfirmasi password baru tidak cocok.';
        } else {
            $hash = password_hash($baru, PASSWORD_DEFAULT);
            $st = mysqli_prepare($koneksi, "UPDATE users SET password=? WHERE id=?");
            mysqli_stmt_bind_param($st, 'si', $hash, $uid);
            mysqli_stmt_execute($st);
            flash('success', 'Password berhasil diganti.');
            redirect('profil.php');
        }
    }
}

$judul_halaman = 'Profil dan Pengaturan';
$halaman_aktif = 'profil';
include __DIR__ . '/includes/header.php';
?>
<h1>Profil dan Pengaturan</h1>

<div class="grid grid-2">
    <div class="card form-card">
        <h2>Profil &amp; Parameter Perhitungan</h2>
        <?php if ($errProfil): ?><div class="alert alert-danger"><?= e($errProfil) ?></div><?php endif; ?>
        <form method="post" id="formProfil" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="profil">
            <label>Nama
                <input type="text" name="nama" value="<?= e($_POST['nama'] ?? $user['nama']) ?>" required>
            </label>
            <label>Email
                <input type="email" name="email" value="<?= e($_POST['email'] ?? $user['email']) ?>" required>
            </label>
            <label>Tarif Listrik (Rp/kWh)
                <input type="number" name="tarif_listrik" value="<?= e($_POST['tarif_listrik'] ?? $user['tarif_listrik']) ?>" min="0" step="any" required>
            </label>
            <label>Faktor Emisi Karbon (kg CO&#8322;/kWh)
                <input type="number" name="faktor_emisi" value="<?= e($_POST['faktor_emisi'] ?? $user['faktor_emisi']) ?>" min="0" step="any" required>
            </label>
            <label class="check">
                <input type="checkbox" name="hitung_ulang" value="1"> Hitung ulang biaya dan emisi data pemakaian yang sudah ada
            </label>
            <p class="muted small">Gunakan nilai dari sumber resmi (tagihan PLN dan publikasi faktor emisi jaringan listrik) agar hasil akurat.</p>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </form>
    </div>

    <div class="card form-card">
        <h2>Ganti Password</h2>
        <?php if ($errPass): ?><div class="alert alert-danger"><?= e($errPass) ?></div><?php endif; ?>
        <form method="post" id="formPassword" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" value="password">
            <label>Password Lama
                <input type="password" name="password_lama" required>
            </label>
            <label>Password Baru
                <input type="password" name="password_baru" minlength="6" required>
            </label>
            <label>Konfirmasi Password Baru
                <input type="password" name="password_konf" minlength="6" required>
            </label>
            <button class="btn btn-primary" type="submit">Ganti Password</button>
        </form>
        <hr>
        <a class="btn btn-danger" href="logout.php">Keluar dari Akun</a>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
