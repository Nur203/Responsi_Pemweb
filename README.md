# GreenTrack

**Sistem Monitoring Energi Terbarukan dan Jejak Karbon**

Proyek Akhir (Responsi 2) Praktikum Pemrograman Web 1
Jurusan Informatika, Fakultas Teknik, Universitas Jenderal Soedirman

**Tema SDGs:** SDG 7 (Affordable and Clean Energy) dan SDG 13 (Climate Action)

## 1. Deskripsi Proyek

GreenTrack adalah aplikasi web untuk mencatat pemakaian energi listrik rumah tangga, menghitung estimasi biaya dan emisi karbon yang dihasilkan, serta membantu pengguna menetapkan dan memantau target penghematan energi. Aplikasi juga menyediakan tips penggunaan energi yang efisien dan informasi seputar energi bersih.

## 2. Latar Belakang dan Tujuan

### Latar Belakang

Penggunaan listrik yang boros berkontribusi pada meningkatnya emisi gas rumah kaca, terutama karena sebagian besar listrik masih dihasilkan dari bahan bakar fosil. Banyak orang tidak mengetahui berapa besar konsumsi energi dan jejak karbon dari kebiasaan sehari-hari, sehingga sulit untuk mulai berhemat. Dibutuhkan alat sederhana yang membuat data tersebut mudah dicatat, dipahami, dan dipantau.

### Tujuan

- Membantu pengguna mencatat dan memantau pemakaian energi listrik.
- Memberikan estimasi biaya dan emisi karbon dari pemakaian tersebut.
- Mendorong penghematan energi melalui target dan tips yang praktis.
- Meningkatkan kesadaran tentang energi bersih dan perubahan iklim.

### Manfaat

- Pengguna dapat menekan tagihan listrik.
- Pengguna mengetahui dampak lingkungan dari pemakaian energinya.
- Mendukung upaya pengurangan emisi pada skala rumah tangga.

## 3. Keterkaitan dengan SDGs

| SDG | Keterkaitan |
| --- | --- |
| SDG 7: Affordable and Clean Energy | Mendorong efisiensi dan penghematan energi serta menyebarkan informasi energi bersih. |
| SDG 13: Climate Action | Menghitung jejak karbon dan mendorong tindakan nyata untuk mengurangi emisi. |

## 4. Daftar Modul dan Fitur

| No | Modul | Deskripsi | Berkas utama |
| --- | --- | --- | --- |
| 1 | Registrasi dan Login | Pendaftaran akun dan autentikasi pengguna menggunakan session PHP. Password disimpan dalam bentuk hash. | `register.php`, `login.php`, `logout.php` |
| 2 | Dashboard | Ringkasan total pemakaian, biaya, dan emisi karbon, grafik tren 6 bulan, perangkat terboros, dan progres target. | `index.php` |
| 3 | Data Pemakaian Energi (CRUD) | Tambah, lihat, ubah, hapus, dan cari data pemakaian per perangkat. | `pemakaian.php`, `pemakaian_tambah.php`, `pemakaian_ubah.php`, `pemakaian_hapus.php` |
| 4 | Kalkulator Konsumsi dan Biaya | Menghitung kWh dan estimasi biaya berdasarkan daya, durasi, dan tarif. | `kalkulator.php` |
| 5 | Kalkulator Emisi Karbon | Menghitung estimasi emisi dari konsumsi kWh beserta kesetaraannya. | `emisi.php` |
| 6 | Riwayat Pemakaian | Tabel dan grafik riwayat pemakaian per bulan atau per hari. | `riwayat.php` |
| 7 | Target Hemat Energi | Menetapkan batas kWh bulanan dan memantau progres penghematan. | `target.php` |
| 8 | Tips dan Edukasi Energi Bersih | Halaman informasi dan tips hemat energi. | `tips.php` |
| 9 | Profil dan Pengaturan | Pengelolaan profil, tarif listrik, faktor emisi, ganti password, dan logout. | `profil.php` |

Fitur pendukung: validasi form di sisi klien (JavaScript) dan sisi server (PHP), token CSRF pada setiap form, prepared statement untuk seluruh query, serta pembatasan data per pengguna.

## 5. Teknologi yang Digunakan

- HTML dan CSS untuk struktur dan tampilan halaman
- JavaScript untuk validasi form, pratinjau perhitungan, dan grafik (Canvas) di sisi klien
- PHP (server-side) dan MySQL / MariaDB untuk logika bisnis dan basis data
- XAMPP (Apache, MySQL, PHP) sebagai server lokal
- Visual Studio Code sebagai editor, Git dan GitHub untuk version control

## 6. Panduan Instalasi dan Konfigurasi

### Prasyarat

- XAMPP (Apache, MySQL, dan PHP)
- Browser modern (misalnya Google Chrome)
- Git (opsional)
- Koneksi internet (opsional, untuk memuat font dari Google Fonts; tanpa internet tampilan memakai font bawaan sistem)

### Langkah Instalasi

1. Clone atau unduh repository ini:

   ```bash
   git clone https://github.com/<username>/greentrack.git
   ```

2. Pindahkan folder proyek ke direktori `htdocs` XAMPP (contoh: `C:\xampp\htdocs\greentrack`).
3. Buka XAMPP Control Panel, lalu klik **Start** pada Apache dan MySQL.
4. Buka phpMyAdmin di `http://localhost/phpmyadmin`.
5. Buat database baru bernama `greentrack`, lalu import file `database/greentrack.sql`.
6. Sesuaikan konfigurasi koneksi database pada file `koneksi.php` bila perlu:

   ```php
   $koneksi = mysqli_connect("localhost", "root", "", "greentrack") or die("Gagal koneksi");
   ```

## 7. Panduan Menjalankan Aplikasi

1. Pastikan Apache dan MySQL sudah berjalan di XAMPP.
2. Buka browser dan akses `http://localhost/greentrack`.
3. Daftar akun baru melalui halaman Registrasi, lalu Login.
4. Mulai menambahkan data pemakaian energi dan jelajahi fitur lainnya dari dashboard.

## 8. Struktur Folder

```
greentrack/
├── assets/
│   ├── css/style.css        # Tampilan
│   └── js/app.js            # Validasi form, pratinjau, dan grafik
├── database/
│   └── greentrack.sql       # Skema database
├── includes/
│   ├── auth.php             # Session, autentikasi, CSRF, helper
│   ├── header.php           # Kerangka halaman bagian atas
│   ├── footer.php           # Kerangka halaman bagian bawah
│   └── pemakaian_helper.php # Validasi input pemakaian
├── koneksi.php              # Konfigurasi koneksi database
├── register.php
├── login.php
├── logout.php
├── index.php                # Dashboard
├── pemakaian.php            # Daftar pemakaian
├── pemakaian_form.php       # Form bersama tambah/ubah
├── pemakaian_tambah.php
├── pemakaian_ubah.php
├── pemakaian_hapus.php
├── kalkulator.php
├── emisi.php
├── riwayat.php
├── target.php
├── tips.php
└── profil.php
```

## 9. Anggota Tim

| Nama | NIM | Peran | Tanggung Jawab |
| --- | --- | --- | --- |
| Afif Muflikhuddin | H1D025061 | Pengembang web, database, hosting, dan repository | Membuat website dan database, memasang di hosting, serta membuat dan mengelola repository GitHub (upload kode, commit) |
| Raditya Ghani | H1D025151 | Dokumentasi dan pengujian | Melengkapi isi README.md (anggota tim, tangkapan layar), menguji semua fitur dan mencatat bug, menyusun naskah dan skenario video |
| Muhammad Akbar Nur Fauzi | H1D025166 | Desain dan publikasi | Membuat Project Showcase (slide), mengambil screenshot, mengedit dan mengunggah video ke YouTube, mengumpulkan tautan untuk dikumpulkan |

## 10. Catatan

Nilai awal tarif listrik (Rp 1.444,70/kWh) dan faktor emisi (0,87 kg CO₂/kWh) hanya perkiraan. Keduanya dapat diatur melalui menu Pengaturan. Gunakan nilai dari sumber resmi agar hasil perhitungan akurat.

*Dikembangkan untuk Responsi 2 Praktikum Pemrograman Web 1, Informatika Unsoed, 2026.*
