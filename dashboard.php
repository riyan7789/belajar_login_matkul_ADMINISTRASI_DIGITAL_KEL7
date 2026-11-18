<?php
require 'config.php';

if (!isset($_SESSION['id_pengguna'])) { header('Location: login.php'); exit; }
$id = (int) $_SESSION['id_pengguna'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hapus tugas
    if (isset($_POST['hapus'])) {
        $idt = (int) $_POST['hapus'];
        $d = $conn->prepare("DELETE FROM tugas WHERE id_tugas = ? AND id_pengguna = ?");
        $d->bind_param('ii', $idt, $id);
        $d->execute();
        header('Location: dashboard.php');
        exit;
    }

    // Tambah tugas
    $nama_tugas = trim($_POST['nama_tugas'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $deadline   = str_replace('T', ' ', $_POST['deadline'] ?? '');

    if ($nama_tugas === '' || $deskripsi === '' || $deadline === '') {
        $error = 'Tugas, deskripsi tugas, dan deadline wajib diisi.';
    } else {
        $i = $conn->prepare("INSERT INTO tugas (id_pengguna, nama_tugas, deskripsi, deadline) VALUES (?,?,?,?)");
        $i->bind_param('isss', $id, $nama_tugas, $deskripsi, $deadline);
        $i->execute();
        header('Location: dashboard.php');
        exit;
    }
}

$q = $conn->prepare("SELECT * FROM tugas WHERE id_pengguna = ? ORDER BY deadline ASC");
$q->bind_param('i', $id);
$q->execute();
$daftar = $q->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - SMWTS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <strong>SMWTS</strong>
  <span>Halo, <?= e($_SESSION['nama']) ?> &nbsp; <a href="logout.php">Logout</a></span>
</header>

<main>
  <div class="box">
    <h2>Buat Tugas</h2>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label for="nama_tugas">Tugas</label>
      <input type="text" id="nama_tugas" name="nama_tugas" required>

      <label for="deskripsi">Deskripsi Tugas</label>
      <textarea id="deskripsi" name="deskripsi" required></textarea>

      <label for="deadline">Deadline</label>
      <input type="datetime-local" id="deadline" name="deadline" required>

      <button type="submit">Simpan Tugas</button>
    </form>
  </div>

  <h2 style="color:#1f3a5f">Daftar Tugas</h2>
  <?php if ($daftar->num_rows === 0): ?>
    <div class="box">Belum ada tugas.</div>
  <?php endif; ?>

  <?php while ($t = $daftar->fetch_assoc()):
        $lewat = strtotime($t['deadline']) < time(); ?>
    <div class="box">
      <h3><?= e($t['nama_tugas']) ?></h3>
      <div><?= nl2br(e($t['deskripsi'])) ?></div>
      <div class="deadline">
        Deadline: <?= date('d-m-Y H:i', strtotime($t['deadline'])) ?>
        <?php if ($lewat): ?><span class="lewat"> (Sudah lewat)</span><?php endif; ?>
      </div>
      <form method="post" onsubmit="return confirm('Hapus tugas ini?')">
        <button class="hapus" name="hapus" value="<?= (int) $t['id_tugas'] ?>">Hapus</button>
      </form>
    </div>
  <?php endwhile; ?>
</main>
</body>
</html>
