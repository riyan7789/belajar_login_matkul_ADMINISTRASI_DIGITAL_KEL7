-- Database SMWTS (Sistem Manajemen Waktu dan Tugas Siswa)
CREATE DATABASE IF NOT EXISTS smwts CHARACTER SET utf8mb4;
USE smwts;

-- Tabel pengguna (untuk login: nama, email, kata sandi)
CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna     INT AUTO_INCREMENT PRIMARY KEY,
    nama            VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    kata_sandi      VARCHAR(255) NOT NULL,          -- disimpan dalam bentuk hash
    percobaan_gagal INT NOT NULL DEFAULT 0,         -- hitungan gagal login
    terkunci_sampai DATETIME NULL                   -- waktu akun terkunci sementara
);

-- Tabel tugas (tugas, deskripsi tugas, deadline)
CREATE TABLE IF NOT EXISTS tugas (
    id_tugas      INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna   INT NOT NULL,
    nama_tugas    VARCHAR(150) NOT NULL,
    deskripsi     TEXT NOT NULL,
    deadline      DATETIME NOT NULL,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
);
