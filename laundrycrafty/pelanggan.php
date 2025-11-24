<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    
    $query = "INSERT INTO pelanggan (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
    if (mysqli_query($conn, $query)) {
        $success = "Pelanggan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan pelanggan!";
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM pelanggan WHERE id_pelanggan = $id";
    if (mysqli_query($conn, $query)) {
        $success = "Pelanggan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus pelanggan!";
    }
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM pelanggan WHERE nama LIKE '%$search%' ORDER BY id_pelanggan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - LaundryCrafty</title>
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
        <h2 style="margin-bottom: 20px;">Data Pelanggan</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Tambah Pelanggan</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Pelanggan</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" required></textarea>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" required>
                </div>
                <button type="submit" name="tambah">Tambah Pelanggan</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Daftar Pelanggan</h3>
            
            <div class="search-box">
                <form method="GET">
                    <input type="text" name="search" placeholder="Cari nama pelanggan..." value="<?php echo $search; ?>">
                    <button type="submit">Cari</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['alamat']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td>
                            <a href="?hapus=<?php echo $row['id_pelanggan']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
