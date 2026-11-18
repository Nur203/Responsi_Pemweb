<?php
// Form bersama untuk tambah/ubah pemakaian.
// Dipanggil dari pemakaian_tambah.php dan pemakaian_ubah.php.
// Variabel yang diharapkan: $mode ('tambah'|'ubah'), $data (array), $error (string)
?>
<div class="page-head">
    <h1><?= $mode === 'ubah' ? 'Ubah' : 'Tambah' ?> Data Pemakaian</h1>
    <a class="btn btn-ghost" href="pemakaian.php">&larr; Kembali</a>
</div>

<div class="card form-card">
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post" id="formPemakaian" novalidate
          data-tarif="<?= e($user['tarif_listrik']) ?>" data-faktor="<?= e($user['faktor_emisi']) ?>">
        <?= csrf_field() ?>
        <label>Nama Perangkat
            <input type="text" name="nama_perangkat" value="<?= e($data['nama_perangkat']) ?>" maxlength="100" required placeholder="mis. AC Kamar, Kulkas">
        </label>
        <div class="grid grid-2">
            <label>Daya (Watt)
                <input type="number" name="daya_watt" value="<?= e($data['daya_watt']) ?>" min="1" step="any" required>
            </label>
            <label>Durasi per Hari (jam)
                <input type="number" name="durasi_jam" value="<?= e($data['durasi_jam']) ?>" min="0.1" max="24" step="any" required>
            </label>
            <label>Jumlah Hari
                <input type="number" name="jumlah_hari" value="<?= e($data['jumlah_hari']) ?>" min="1" max="366" required>
            </label>
            <label>Tanggal
                <input type="date" name="tanggal" value="<?= e($data['tanggal']) ?>" required>
            </label>
        </div>
        <label>Catatan (opsional)
            <input type="text" name="catatan" value="<?= e($data['catatan']) ?>" maxlength="255">
        </label>

        <div class="preview" id="preview">
            <div><span>Estimasi kWh</span><strong id="pvKwh">0</strong></div>
            <div><span>Estimasi Biaya</span><strong id="pvBiaya">Rp 0</strong></div>
            <div><span>Estimasi Emisi</span><strong id="pvEmisi">0 kg CO&#8322;</strong></div>
        </div>
        <p class="muted small">Dihitung dengan tarif <?= rupiah($user['tarif_listrik']) ?>/kWh dan faktor emisi <?= angka($user['faktor_emisi'], 4) ?> kg CO&#8322;/kWh (ubah di menu Pengaturan).</p>

        <button class="btn btn-primary" type="submit">Simpan</button>
    </form>
</div>
