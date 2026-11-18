<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/pemakaian_helper.php';
require_login();
$user = current_user();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = mysqli_prepare($koneksi, "SELECT * FROM pemakaian WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($st, 'ii', $id, $user['id']);
mysqli_stmt_execute($st);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if (!$data) {
    flash('danger', 'Data tidak ditemukan.');
    redirect('pemakaian.php');
}

$mode = 'ubah';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $error] = validasi_pemakaian($_POST);
    if (!$error) {
        $h = hitung($data['daya_watt'], $data['durasi_jam'], $data['jumlah_hari'], $user['tarif_listrik'], $user['faktor_emisi']);
        $st = mysqli_prepare($koneksi, "UPDATE pemakaian SET nama_perangkat=?, daya_watt=?, durasi_jam=?, jumlah_hari=?, tanggal=?, kwh=?, biaya=?, emisi=?, catatan=? WHERE id=? AND user_id=?");
        mysqli_stmt_bind_param($st, 'sddisdddsii', $data['nama_perangkat'], $data['daya_watt'], $data['durasi_jam'], $data['jumlah_hari'], $data['tanggal'], $h['kwh'], $h['biaya'], $h['emisi'], $data['catatan'], $id, $user['id']);
        if (mysqli_stmt_execute($st)) {
            flash('success', 'Data pemakaian berhasil diperbarui.');
            redirect('pemakaian.php');
        }
        $error = 'Gagal memperbarui data.';
    }
}

$judul_halaman = 'Ubah Pemakaian';
$halaman_aktif = 'pemakaian';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/pemakaian_form.php';
include __DIR__ . '/includes/footer.php';
