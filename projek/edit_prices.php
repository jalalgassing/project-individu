<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Periksa apakah user adalah admin
$sql_check_admin = "SELECT is_admin FROM users WHERE id = " . $_SESSION["user_id"];
$result_admin = mysqli_query($conn, $sql_check_admin);
$user = mysqli_fetch_assoc($result_admin);

if (!$user || !$user['is_admin']) {
    header("Location: home.php");
    exit();
}

// Proses update harga jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Anda perlu menambahkan tabel untuk menyimpan harga atau menggunakan cara lain
    // Ini hanya contoh konsep
    $harga_mini = $_POST['harga_mini'];
    $harga_rumput = $_POST['harga_rumput'];
    $harga_puzzle = $_POST['harga_puzzle'];
    
    // Lakukan update ke database atau file konfigurasi
    // ...
    
    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Harga - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL - EDIT HARGA</h1>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="fasilitas.php">Fasilitas</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Edit Harga Lapangan</h1>
            
            <?php if (isset($success) && $success): ?>
                <div class="success">Harga berhasil diperbarui!</div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="price-edit-group">
                    <label for="harga_mini">Harga Mini Soccer (per 2 jam):</label>
                    <input type="number" id="harga_mini" name="harga_mini" value="500000" required>
                </div>
                
                <div class="price-edit-group">
                    <label for="harga_rumput">Harga Rumput Sintesis (per jam):</label>
                    <input type="number" id="harga_rumput" name="harga_rumput" value="150000" required>
                </div>
                
                <div class="price-edit-group">
                    <label for="harga_puzzle">Harga Puzzle (per jam):</label>
                    <input type="number" id="harga_puzzle" name="harga_puzzle" value="100000" required>
                </div>
                
                <input type="submit" value="Simpan Perubahan">
                <a href="fasilitas.php" class="btn-cancel">Batal</a>
            </form>
        </div>
    </div>

    <div class="footer-section">
        <h3>Kontak</h3>
        <p><span class="icon">📍</span> Jl. Futsal No. 123, Tokyo</p>
        <p><span class="icon">📞</span> (021) 1234-5678</p>
        <p><span class="icon">✉️</span> info@latomfutsal.com</p>
    </div>
</body>
</html>