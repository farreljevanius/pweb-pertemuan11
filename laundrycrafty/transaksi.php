<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['tambah'])) {
    $id_pelanggan = intval($_POST['id_pelanggan']);
    $id_layanan = intval($_POST['id_layanan']);
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $berat = floatval($_POST['berat']);
    
    $layanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_per_kg FROM layanan WHERE id_layanan = $id_layanan"));
    $total_harga = $berat * $layanan['harga_per_kg'];
    
    $query = "INSERT INTO transaksi (id_pelanggan, id_layanan, tanggal_masuk, tanggal_selesai, berat, total_harga, status) 
              VALUES ($id_pelanggan, $id_layanan, '$tanggal_masuk', '$tanggal_selesai', $berat, $total_harga, 'Proses')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Transaksi berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan transaksi!";
    }
}

if (isset($_GET['update_status'])) {
    $id = intval($_GET['update_status']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    
    $query = "UPDATE transaksi SET status = '$status' WHERE id_transaksi = $id";
    if (mysqli_query($conn, $query)) {
        $success = "Status berhasil diupdate!";
    }
}

$pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY nama");
$layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY nama_layanan");

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT t.*, p.nama, l.nama_layanan 
          FROM transaksi t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          JOIN layanan l ON t.id_layanan = l.id_layanan
          WHERE p.nama LIKE '%$search%' OR DATE_FORMAT(t.tanggal_masuk, '%Y-%m-%d') LIKE '%$search%'
          ORDER BY t.id_transaksi DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - LaundryCrafty</title>
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
        <h2 style="margin-bottom: 20px;">Transaksi Laundry</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Tambah Transaksi</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Pelanggan</label>
                    <select name="id_pelanggan" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php 
                        mysqli_data_seek($pelanggan, 0);
                        while ($p = mysqli_fetch_assoc($pelanggan)): 
                        ?>
                        <option value="<?php echo $p['id_pelanggan']; ?>"><?php echo $p['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Layanan</label>
                    <select name="id_layanan" required id="layanan">
                        <option value="">-- Pilih Layanan --</option>
                        <?php 
                        mysqli_data_seek($layanan, 0);
                        while ($l = mysqli_fetch_assoc($layanan)): 
                        ?>
                        <option value="<?php echo $l['id_layanan']; ?>" data-harga="<?php echo $l['harga_per_kg']; ?>">
                            <?php echo $l['nama_layanan']; ?> - Rp <?php echo number_format($l['harga_per_kg'], 0, ',', '.'); ?>/kg
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Berat (kg)</label>
                    <input type="number" step="0.1" name="berat" required>
                </div>
                <button type="submit" name="tambah">Tambah Transaksi</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Daftar Transaksi</h3>
            
            <div class="search-box">
                <form method="GET">
                    <input type="text" name="search" placeholder="Cari nama/tanggal..." value="<?php echo $search; ?>">
                    <button type="submit">Cari</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Tgl Masuk</th>
                        <th>Tgl Selesai</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id_transaksi']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['nama_layanan']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                        <td><?php echo $row['berat']; ?> kg</td>
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php 
                            $badge_class = '';
                            if ($row['status'] == 'Proses') $badge_class = 'badge-proses';
                            elseif ($row['status'] == 'Selesai') $badge_class = 'badge-selesai';
                            else $badge_class = 'badge-diambil';
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $row['status']; ?></span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Proses'): ?>
                                <a href="?update_status=<?php echo $row['id_transaksi']; ?>&status=Selesai">
                                    <button class="btn-update">Selesai</button>
                                </a>
                            <?php elseif ($row['status'] == 'Selesai'): ?>
                                <a href="?update_status=<?php echo $row['id_transaksi']; ?>&status=Sudah Diambil">
                                    <button class="btn-update">Diambil</button>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>