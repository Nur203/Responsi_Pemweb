<?php
require_once __DIR__ . '/includes/auth.php';
if (is_login()) redirect('index.php');

$error = '';
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($nama === '' || $email === '' || $pass === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid. Contoh: nama@email.com.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($pass !== $pass2) {
        $error = 'Konfirmasi password tidak cocok dengan password.';
    } else {
        $st = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($st, 's', $email);
        mysqli_stmt_execute($st);
        mysqli_stmt_store_result($st);
        if (mysqli_stmt_num_rows($st) > 0) {
            $error = 'Email ini sudah terdaftar. Masuk dengan email tersebut atau pakai email lain.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $st = mysqli_prepare($koneksi, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($st, 'sss', $nama, $email, $hash);
            if (mysqli_stmt_execute($st)) {
                flash('success', 'Akun berhasil dibuat. Silakan masuk.');
                redirect('login.php');
            } else {
                $error = 'Akun belum tersimpan karena ada gangguan pada database. Coba lagi sebentar lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | GreenTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Hanken+Grotesk:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-wrap">
    <aside class="auth-aside">
        <a class="brand" href="login.php">
            <svg class="mark" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="4" fill="#e9a820"/><path d="M4.5 16.5l5-5 3.5 3 6.5-8" fill="none" stroke="#10281e" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            GreenTrack
        </a>
        <div>
            <h2>Mulai dari satu perangkat di rumahmu.</h2>
            <?= meter(0) ?>
            <p class="caption">Setiap catatan menambah angka di meteranmu.</p>
        </div>
        <p class="caption">Sistem monitoring energi dan jejak karbon rumah tangga.</p>
    </aside>
    <main class="auth-main">
        <div class="auth-form">
            <h1>Buat akun</h1>
            <p class="muted">Gratis, cukup nama, email, dan password.</p>
            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post" id="formRegister" novalidate>
                <?= csrf_field() ?>
                <label>Nama lengkap
                    <input type="text" name="nama" value="<?= e($nama) ?>" required>
                </label>
                <label>Email
                    <input type="email" name="email" value="<?= e($email) ?>" required>
                </label>
                <label>Password
                    <input type="password" name="password" minlength="6" required>
                </label>
                <label>Ulangi password
                    <input type="password" name="password2" minlength="6" required>
                </label>
                <button class="btn btn-primary btn-block" type="submit">Daftar</button>
            </form>
            <p class="auth-foot">Sudah punya akun? <a href="login.php">Masuk</a></p>
        </div>
    </main>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
