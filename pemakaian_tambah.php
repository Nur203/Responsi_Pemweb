<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/pemakaian_helper.php';
require_login();
$user = current_user();

$mode = 'tambah';
$error = '';
$data = ['nama_perangkat' => '', 'daya_watt' => '', 'durasi_jam' => '', 'jumlah_hari' => 1, 'tanggal' => date('Y-m-d'), 'catatan' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $error] = validasi_pemakaian($_POST);
    if (!$error) {
        $h = hitung($data['daya_watt'], $data['durasi_jam'], $data['jumlah_hari'], $user['tarif_listrik'], $user['faktor_emisi']);
        $st = mysqli_prepare($koneksi, "INSERT INTO pemakaian (user_id, nama_perangkat, daya_watt, durasi_jam, jumlah_hari, tanggal, kwh, biaya, emisi, catatan) VALUES (?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($st, 'isddisddds', $user['id'], $data['nama_perangkat'], $data['daya_watt'], $data['durasi_jam'], $data['jumlah_hari'], $data['tanggal'], $h['kwh'], $h['biaya'], $h['emisi'], $data['catatan']);
        if (mysqli_stmt_execute($st)) {
            flash('success', 'Data pemakaian berhasil ditambahkan.');
            redirect('pemakaian.php');
        }
        $error = 'Gagal menyimpan data.';
    }
}

$judul_halaman = 'Tambah Pemakaian';
$halaman_aktif = 'pemakaian';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/pemakaian_form.php';
include __DIR__ . '/includes/footer.php';
