<?php
// Koneksi database. Database & tabel dibuat OTOMATIS jika belum ada.
mysqli_report(MYSQLI_REPORT_OFF);
session_start();

$conn = @new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die('MySQL belum menyala. Buka XAMPP, klik Start pada MySQL, lalu refresh halaman ini.');
}
$conn->set_charset('utf8mb4');
$conn->query("CREATE DATABASE IF NOT EXISTS smwts CHARACTER SET utf8mb4");
$conn->select_db('smwts');

$conn->query("CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna     INT AUTO_INCREMENT PRIMARY KEY,
    nama            VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    kata_sandi      VARCHAR(255) NOT NULL,
    percobaan_gagal INT NOT NULL DEFAULT 0,
    terkunci_sampai DATETIME NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS tugas (
    id_tugas      INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna   INT NOT NULL,
    nama_tugas    VARCHAR(150) NOT NULL,
    deskripsi     TEXT NOT NULL,
    deadline      DATETIME NOT NULL,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
)");

const MAX_GAGAL   = 5;   // batas gagal login berturut-turut
const MENIT_KUNCI = 15;  // lama akun terkunci (menit)

function e($teks) { return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8'); }
