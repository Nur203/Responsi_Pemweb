<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('pemakaian.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$st = mysqli_prepare($koneksi, "DELETE FROM pemakaian WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($st, 'ii', $id, $_SESSION['user_id']);
mysqli_stmt_execute($st);

flash(mysqli_stmt_affected_rows($st) > 0 ? 'success' : 'danger',
      mysqli_stmt_affected_rows($st) > 0 ? 'Data berhasil dihapus.' : 'Data tidak ditemukan.');
redirect('pemakaian.php');
