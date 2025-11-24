<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Tambah layanan
if (isset($_POST['tambah'])) {
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $harga_per_kg = floatval($_POST['harga_per_kg']);
    
    $query = "INSERT INTO layanan (nama_layanan, harga_per_kg) VALUES ('$nama_layanan', $harga_per_kg)";
    if (mysqli_query($conn, $query)) {
        $success = "Layanan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan layanan!";
    }
}

// Edit layanan
if (isset($_POST['edit'])) {
    $id = intval($_POST['id_layanan']);
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $harga_per_kg = floatval($_POST['harga_per_kg']);
    
    $query = "UPDATE layanan SET nama_layanan = '$nama_layanan', harga_per_kg = $harga_per_kg WHERE id_layanan = $id";
    if (mysqli_query($conn, $query)) {
        $success = "Layanan berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate layanan!";
    }
}

// Hapus layanan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM layanan WHERE id_layanan = $id";
    if (mysqli_query($conn, $query)) {
        $success = "Layanan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus layanan!";
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM layanan WHERE id_layanan = $id"));
}

// Ambil semua layanan
$result = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - LaundryCrafty</title>
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
        <h2 style="margin-bottom: 20px;">Manajemen Layanan</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 15px;"><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Layanan</h3>
            <form method="POST">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_layanan" value="<?php echo $edit_data['id_layanan']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input type="text" name="nama_layanan" value="<?php echo $edit_data['nama_layanan'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga per Kg (Rp)</label>
                    <input type="number" name="harga_per_kg" value="<?php echo $edit_data['harga_per_kg'] ?? ''; ?>" required>
                </div>
                
                <?php if ($edit_data): ?>
                    <button type="submit" name="edit">Update Layanan</button>
                    <a href="layanan.php" style="margin-left: 10px; text-decoration: none; color: #666;">Batal</a>
                <?php else: ?>
                    <button type="submit" name="tambah">Tambah Layanan</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Daftar Layanan</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th>Harga per Kg</th>
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
                        <td><?php echo $row['nama_layanan']; ?></td>
                        <td>Rp <?php echo number_format($row['harga_per_kg'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['id_layanan']; ?>" class="btn-edit">Edit</a>
                            <a href="?hapus=<?php echo $row['id_layanan']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>