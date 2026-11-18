<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sandi = $_POST['kata_sandi'] ?? '';
    $ulang = $_POST['ulang_sandi'] ?? '';

    if ($nama === '' || $email === '' || $sandi === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($sandi) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif ($sandi !== $ulang) {
        $error = 'Konfirmasi kata sandi tidak sama.';
    } else {
        $cek = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
        $cek->bind_param('s', $email);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($sandi, PASSWORD_DEFAULT); // kata sandi terenkripsi
            $ins = $conn->prepare("INSERT INTO pengguna (nama, email, kata_sandi) VALUES (?,?,?)");
            $ins->bind_param('sss', $nama, $email, $hash);
            $ins->execute();
            header('Location: login.php?daftar=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar - SMWTS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="center">
  <div class="card">
    <h1>Daftar Akun</h1>
    <p class="sub">Buat akun untuk masuk ke SMWTS</p>

    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" autocomplete="off">
      <label for="nama">Nama</label>
      <input type="text" id="nama" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>">

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">

      <label for="kata_sandi">Kata Sandi</label>
      <input type="password" id="kata_sandi" name="kata_sandi" required>

      <label for="ulang_sandi">Ulangi Kata Sandi</label>
      <input type="password" id="ulang_sandi" name="ulang_sandi" required>

      <button type="submit">Daftar</button>
    </form>
    <div class="link">Sudah punya akun? <a href="login.php">Login</a></div>
  </div>
</div>
</body>
</html>
