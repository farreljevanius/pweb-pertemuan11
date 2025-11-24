<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Hitung statistik
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$pendapatan_bulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE MONTH(tanggal_masuk) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_masuk) = YEAR(CURRENT_DATE())"))['total'] ?? 0;
$transaksi_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'Proses'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LaundryCrafty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>LaundryCrafty</h1>
        <div>
            <span>Selamat datang, <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px;">Dashboard</h2>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Pelanggan</h3>
                <p><?php echo $total_pelanggan; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Transaksi</h3>
                <p><?php echo $total_transaksi; ?></p>
            </div>
            <div class="stat-card">
                <h3>Pendapatan Bulan Ini</h3>
                <p>Rp <?php echo number_format($pendapatan_bulan, 0, ',', '.'); ?></p>
            </div>
            <div class="stat-card">
                <h3>Cucian Proses</h3>
                <p><?php echo $transaksi_proses; ?></p>
            </div>
        </div>

        <h3 style="margin-bottom: 15px;">Menu</h3>
        <div class="menu">
            <a href="pelanggan.php" class="menu-item">
                <h3>👤 Pelanggan</h3>
            </a>
            <a href="transaksi.php" class="menu-item">
                <h3>📋 Transaksi</h3>
            </a>
            <a href="laporan.php" class="menu-item">
                <h3>📊 Laporan</h3>
            </a>
            <a href="layanan.php" class="menu-item">
                <h3>⚙️ Layanan</h3>
            </a>
        </div>
    </div>
</body>
</html>