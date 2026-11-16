<?php
session_start();

if (!isset($_SESSION['pengguna'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Manajemen Waktu dan Tugas Siswa</title>
</head>
<body style="text-align:center">
    <h1>Halaman Administrator</h1>
    <a href="index.php">Home</a> |
    <a href="logout.php">Logout</a>
    <hr>
    <h3>Selamat datang, <?= htmlspecialchars($_SESSION['pengguna']['nama']) ?></h3>
    <p>Halaman ini hanya tampil setelah user login.</p>
</body>
</html>
