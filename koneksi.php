<?php
// Konfigurasi koneksi database GreenTrack
$koneksi = mysqli_connect("localhost", "root", "", "greentrack") or die("Gagal koneksi");
mysqli_set_charset($koneksi, "utf8mb4");
