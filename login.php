<?php
require 'config.php';

if (isset($_SESSION['id_pengguna'])) { header('Location: dashboard.php'); exit; }

$error = '';
$info  = isset($_GET['daftar']) ? 'Akun berhasil dibuat. Silakan login.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sandi = $_POST['kata_sandi'] ?? '';

    if ($nama === '' || $email === '' || $sandi === '') {
        $error = 'Nama, email, dan kata sandi wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || mb_strtolower(trim($user['nama'])) !== mb_strtolower($nama)) {
            $error = 'Login gagal. Nama, email, atau kata sandi salah.';
        } elseif ($user['terkunci_sampai'] && strtotime($user['terkunci_sampai']) > time()) {
            $sisa = ceil((strtotime($user['terkunci_sampai']) - time()) / 60);
            $error = "Akun terkunci sementara. Coba lagi dalam $sisa menit.";
        } elseif (password_verify($sandi, $user['kata_sandi'])) {
            $r = $conn->prepare("UPDATE pengguna SET percobaan_gagal = 0, terkunci_sampai = NULL WHERE id_pengguna = ?");
            $r->bind_param('i', $user['id_pengguna']);
            $r->execute();

            session_regenerate_id(true);
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['nama']        = $user['nama'];
            header('Location: dashboard.php');
            exit;
        } else {
            $gagal = $user['percobaan_gagal'] + 1;
            if ($gagal >= MAX_GAGAL) {
                $u = $conn->prepare("UPDATE pengguna SET percobaan_gagal = 0,
                     terkunci_sampai = DATE_ADD(NOW(), INTERVAL " . MENIT_KUNCI . " MINUTE) WHERE id_pengguna = ?");
                $u->bind_param('i', $user['id_pengguna']);
                $error = 'Terlalu banyak percobaan gagal. Akun dikunci ' . MENIT_KUNCI . ' menit.';
            } else {
                $u = $conn->prepare("UPDATE pengguna SET percobaan_gagal = ? WHERE id_pengguna = ?");
                $u->bind_param('ii', $gagal, $user['id_pengguna']);
                $error = 'Login gagal. Nama, email, atau kata sandi salah. Sisa percobaan: ' . (MAX_GAGAL - $gagal) . '.';
            }
            $u->execute();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - SMWTS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="center">
  <div class="card">
    <h1>Login SMWTS</h1>
    <p class="sub">Sistem Manajemen Waktu dan Tugas Siswa</p>

    <?php if ($info):  ?><div class="ok"><?= e($info) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" autocomplete="off">
      <label for="nama">Nama</label>
      <input type="text" id="nama" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>">

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">

      <label for="kata_sandi">Kata Sandi</label>
      <input type="password" id="kata_sandi" name="kata_sandi" required>

      <button type="submit">Masuk</button>
    </form>
    <div class="link">Belum punya akun? <a href="daftar.php">Daftar di sini</a></div>
  </div>
</div>
</body>
</html>
