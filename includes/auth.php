<?php
// Helper umum: session, autentikasi, escape, format, flash message, CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../koneksi.php';

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function rupiah($n) {
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

function angka($n, $d = 2) {
    return number_format((float)$n, $d, ',', '.');
}

function is_login() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_login()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function flash($type = null, $msg = null) {
    if ($type !== null) {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
        return;
    }
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check() {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(400);
        die('Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
    }
}

// Ambil data user yang sedang login (termasuk tarif dan faktor emisi)
function current_user() {
    global $koneksi;
    static $u = null;
    if ($u === null && is_login()) {
        $st = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
        mysqli_stmt_bind_param($st, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($st);
        $u = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        if (!$u) {
            session_destroy();
            redirect('login.php');
        }
    }
    return $u;
}

// Hitung kWh, biaya, emisi
function hitung($daya_watt, $durasi_jam, $hari, $tarif, $faktor) {
    $kwh = ($daya_watt * $durasi_jam * $hari) / 1000;
    return [
        'kwh'   => $kwh,
        'biaya' => $kwh * $tarif,
        'emisi' => $kwh * $faktor,
    ];
}

// Tampilan angka ala meteran listrik (kWh): digit dalam kotak, digit desimal berwarna beda
function meter($value, $int = 5, $dec = 1) {
    $s = number_format((float)$value, $dec, '.', '');
    $parts = explode('.', $s);
    $i = str_pad(substr($parts[0], -$int), $int, '0', STR_PAD_LEFT);
    $d = $parts[1] ?? '';
    $out = '<span class="meter" role="img" aria-label="' . e(angka($value, $dec)) . ' kWh">';
    $lead = true;
    foreach (str_split($i) as $k => $ch) {
        if ($ch !== '0' || $k === $int - 1) $lead = false;
        $out .= '<span class="dg' . ($lead ? ' dg-zero' : '') . '" aria-hidden="true">' . $ch . '</span>';
    }
    foreach (str_split($d) as $ch) {
        $out .= '<span class="dg dg-dec" aria-hidden="true">' . $ch . '</span>';
    }
    return $out . '</span>';
}

function nama_bulan($ym) {
    $n = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $n[(int)substr($ym, 5, 2) - 1] . ' ' . substr($ym, 0, 4);
}
