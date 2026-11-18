<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$tips = [
    ['Pendingin', 'Atur AC di 24-26&deg;C', 'Setiap kenaikan 1&deg;C pada suhu AC dapat menghemat sekitar 3-5% listrik. Bersihkan filter secara berkala agar kerja AC tetap efisien.'],
    ['Pencahayaan', 'Ganti ke lampu LED', 'Lampu LED memakai daya jauh lebih rendah dibanding lampu pijar atau neon dengan terang yang sama, dan umurnya lebih panjang.'],
    ['Standby', 'Cabut perangkat yang tidak dipakai', 'Charger dan alat elektronik yang tertancap tetap menarik daya siaga. Gunakan stop kontak dengan saklar agar mudah dimatikan.'],
    ['Dapur', 'Jaga kulkas tetap efisien', 'Jangan menaruh makanan panas ke kulkas, pastikan pintu menutup rapat, dan beri jarak dari dinding agar sirkulasi udara baik.'],
    ['Cuci', 'Cuci dengan beban penuh', 'Mesin cuci dan setrika lebih hemat jika dipakai dalam satu sesi. Kumpulkan pakaian dulu, setrika sekaligus.'],
    ['Alam', 'Manfaatkan cahaya dan angin alami', 'Buka tirai dan jendela pada siang hari untuk mengurangi pemakaian lampu dan AC.'],
    ['Pemanas', 'Isi dispenser dan teko seperlunya', 'Hindari memanaskan air berulang. Matikan dispenser pada malam hari jika tidak dibutuhkan.'],
    ['Perangkat', 'Pilih label hemat energi', 'Saat membeli perangkat baru, bandingkan konsumsi dayanya. Perangkat inverter umumnya lebih irit.'],
];

$edukasi = [
    ['Energi Terbarukan', 'Energi terbarukan berasal dari sumber yang terus tersedia di alam, seperti matahari, angin, air, panas bumi, dan biomassa. Indonesia memiliki potensi besar, termasuk panas bumi dan tenaga surya.'],
    ['Jejak Karbon', 'Jejak karbon adalah total emisi gas rumah kaca dari aktivitas kita. Listrik dari pembangkit berbahan bakar fosil menyumbang emisi, sehingga berhemat listrik berarti ikut mengurangi emisi.'],
    ['Panel Surya Atap', 'Panel surya atap memungkinkan rumah tangga menghasilkan listrik sendiri dari matahari, mengurangi ketergantungan pada listrik berbasis fosil dan menekan tagihan jangka panjang.'],
    ['SDG 7 dan SDG 13', 'SDG 7 menargetkan energi yang terjangkau dan bersih bagi semua, sedangkan SDG 13 menargetkan tindakan segera melawan perubahan iklim. Penghematan energi di rumah mendukung keduanya.'],
];

$judul_halaman = 'Tips & Edukasi';
$halaman_aktif = 'tips';
include __DIR__ . '/includes/header.php';
?>
<h1>Tips Hemat Energi</h1>
<p class="muted">Langkah sederhana yang bisa langsung dipraktikkan di rumah.</p>

<section class="grid grid-3">
    <?php foreach ($tips as [$kat, $judul, $isi]): ?>
        <article class="card tip">
            <span class="badge badge-ok"><?= e($kat) ?></span>
            <h3><?= $judul ?></h3>
            <p><?= $isi ?></p>
        </article>
    <?php endforeach; ?>
</section>

<h1 class="mt">Edukasi Energi Bersih</h1>
<section class="accordion">
    <?php foreach ($edukasi as $i => [$judul, $isi]): ?>
        <details class="card" <?= $i === 0 ? 'open' : '' ?>>
            <summary><?= e($judul) ?></summary>
            <p><?= e($isi) ?></p>
        </details>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
