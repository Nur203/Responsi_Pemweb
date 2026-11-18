<?php
// Validasi input form pemakaian. Mengembalikan [data_bersih, pesan_error]
function validasi_pemakaian($in) {
    $data = [
        'nama_perangkat' => trim($in['nama_perangkat'] ?? ''),
        'daya_watt'      => $in['daya_watt'] ?? '',
        'durasi_jam'     => $in['durasi_jam'] ?? '',
        'jumlah_hari'    => $in['jumlah_hari'] ?? '',
        'tanggal'        => $in['tanggal'] ?? '',
        'catatan'        => trim($in['catatan'] ?? ''),
    ];
    $error = '';
    if ($data['nama_perangkat'] === '') {
        $error = 'Nama perangkat wajib diisi.';
    } elseif (!is_numeric($data['daya_watt']) || $data['daya_watt'] <= 0) {
        $error = 'Daya harus berupa angka lebih dari 0.';
    } elseif (!is_numeric($data['durasi_jam']) || $data['durasi_jam'] <= 0 || $data['durasi_jam'] > 24) {
        $error = 'Durasi harus antara 0 dan 24 jam per hari.';
    } elseif (!ctype_digit((string)$data['jumlah_hari']) || $data['jumlah_hari'] < 1 || $data['jumlah_hari'] > 366) {
        $error = 'Jumlah hari harus bilangan bulat 1 - 366.';
    } elseif (!DateTime::createFromFormat('Y-m-d', $data['tanggal'])) {
        $error = 'Tanggal tidak valid.';
    }
    if (!$error) {
        $data['daya_watt'] = (float)$data['daya_watt'];
        $data['durasi_jam'] = (float)$data['durasi_jam'];
        $data['jumlah_hari'] = (int)$data['jumlah_hari'];
    }
    return [$data, $error];
}
