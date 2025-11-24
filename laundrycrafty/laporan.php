<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'bulan';
$tanggal_dari = isset($_GET['tanggal_dari']) ? $_GET['tanggal_dari'] : date('Y-m-01');
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');

if ($filter == 'hari') {
    $where = "DATE(tanggal_masuk) = CURDATE()";
    $judul = "Hari Ini";
} elseif ($filter == 'minggu') {
    $where = "YEARWEEK(tanggal_masuk, 1) = YEARWEEK(CURDATE(), 1)";
    $judul = "Minggu Ini";
} elseif ($filter == 'bulan') {
    $where = "MONTH(tanggal_masuk) = MONTH(CURDATE()) AND YEAR(tanggal_masuk) = YEAR(CURDATE())";
    $judul = "Bulan Ini";
} else {
    $where = "tanggal_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
    $judul = "Custom";
}

$query = "SELECT t.*, p.nama, l.nama_layanan 
          FROM transaksi t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          JOIN layanan l ON t.id_layanan = l.id_layanan
          WHERE $where
          ORDER BY t.tanggal_masuk DESC";
$result = mysqli_query($conn, $query);

$total_query = "SELECT SUM(total_harga) as total FROM transaksi WHERE $where";
$total_result = mysqli_fetch_assoc(mysqli_query($conn, $total_query));
$total_pendapatan = $total_result['total'] ?? 0;

$jumlah_transaksi = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - LaundryCrafty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>LaundryCrafty</h1>
        <div>
            <a href="index.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px;">Laporan Pendapatan</h2>

        <div class="card">
            <form method="GET" class="filter-box">
                <button type="submit" name="filter" value="hari" class="<?php echo $filter=='hari'?'active':''; ?>">Hari Ini</button>
                <button type="submit" name="filter" value="minggu" class="<?php echo $filter=='minggu'?'active':''; ?>">Minggu Ini</button>
                <button type="submit" name="filter" value="bulan" class="<?php echo $filter=='bulan'?'active':''; ?>">Bulan Ini</button>
                
                <input type="date" name="tanggal_dari" value="<?php echo $tanggal_dari; ?>">
                <input type="date" name="tanggal_sampai" value="<?php echo $tanggal_sampai; ?>">
                <button type="submit" name="filter" value="custom">Tampilkan</button>
            </form>

            <h3 style="margin-bottom: 15px;">Laporan: <?php echo $judul; ?></h3>

            <div class="stats-row">
                <div class="stat-box">
                    <h3>Total Transaksi</h3>
                    <p><?php echo $jumlah_transaksi; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Pendapatan</h3>
                    <p>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
                </div>
            </div>

            <button onclick="window.print()" class="btn-print">Cetak Laporan</button>

            <table style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Berat (kg)</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['nama_layanan']; ?></td>
                        <td><?php echo $row['berat']; ?></td>
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($jumlah_transaksi == 0): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data transaksi</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
