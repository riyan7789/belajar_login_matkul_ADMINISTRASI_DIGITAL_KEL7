<?php
// inisialisasi session
session_start();

// mengecek apakah ada session user yang aktif, jika tidak arahkan ke login.php
if(!isset($_SESSION['user'])) {
    header('location:login.php'); // arahkan ke login.php
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Belajar Membuat Halaman Login</title>
</head>
<body style="text-align:center">
    <h1>Halaman Administrator</h1>
    <a href="index.php">Home</a>
    <a href="logout.php">Logout</a>
    <hr>
    <h3>Selamat datang, nama user</h3>
    halaman ini akan tampil setelah user login.

</body>
</html>
