<?php
// Variabel opsional: $judul_halaman, $halaman_aktif
$judul_halaman = $judul_halaman ?? 'GreenTrack';
$halaman_aktif = $halaman_aktif ?? '';
$menu = [
    'index'      => ['index.php', 'Dashboard'],
    'pemakaian'  => ['pemakaian.php', 'Pemakaian'],
    'kalkulator' => ['kalkulator.php', 'Kalkulator'],
    'emisi'      => ['emisi.php', 'Emisi karbon'],
    'riwayat'    => ['riwayat.php', 'Riwayat'],
    'target'     => ['target.php', 'Target'],
    'tips'       => ['tips.php', 'Tips'],
    'profil'     => ['profil.php', 'Pengaturan'],
];
$flash = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> | GreenTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Hanken+Grotesk:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <svg class="mark" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="4" fill="#1d4a37"/><path d="M4.5 16.5l5-5 3.5 3 6.5-8" fill="none" stroke="#e9a820" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        GreenTrack
    </a>
    <button class="nav-toggle" id="navToggle" aria-label="Buka menu" aria-controls="nav">Menu</button>
    <nav class="nav" id="nav">
        <?php foreach ($menu as $key => [$url, $label]): ?>
            <a href="<?= $url ?>" class="<?= $halaman_aktif === $key ? 'active' : '' ?>"<?= $halaman_aktif === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="logout.php" class="nav-logout">Keluar</a>
    </nav>
</header>
<main class="container">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['msg']) ?></div>
<?php endif; ?>
