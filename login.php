<?php
require_once __DIR__ . '/includes/auth.php';
if (is_login()) redirect('index.php');

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = 'Isi email dan password.';
    } else {
        $st = mysqli_prepare($koneksi, "SELECT id, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($st, 's', $email);
        mysqli_stmt_execute($st);
        $u = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        if ($u && password_verify($pass, $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$u['id'];
            redirect('index.php');
        } else {
            $error = 'Email atau password salah. Periksa lagi lalu coba masuk kembali.';
        }
    }
}
$flash = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | GreenTrack</title>
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
            <h2>Catat listrik rumahmu, lihat jejak karbonnya.</h2>
            <?= meter(128.4) ?>
            <p class="caption">Contoh pembacaan: 128,4 kWh dalam satu bulan.</p>
        </div>
        <p class="caption">Sistem monitoring energi dan jejak karbon rumah tangga.</p>
    </aside>
    <main class="auth-main">
        <div class="auth-form">
            <h1>Masuk</h1>
            <p class="muted">Gunakan akun GreenTrack-mu.</p>
            <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['msg']) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post" id="formLogin" novalidate>
                <?= csrf_field() ?>
                <label>Email
                    <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
                </label>
                <label>Password
                    <input type="password" name="password" required>
                </label>
                <button class="btn btn-primary btn-block" type="submit">Masuk</button>
            </form>
            <p class="auth-foot">Belum punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </main>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
