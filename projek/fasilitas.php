<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Periksa apakah user adalah admin
$sql_check_admin = "SELECT is_admin FROM users WHERE id = " . $_SESSION["user_id"];
$result_admin = mysqli_query($conn, $sql_check_admin);
$user = mysqli_fetch_assoc($result_admin);
$is_admin = $user && $user['is_admin'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fasilitas - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL</h1>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="fasilitas.php">Fasilitas</a></li>
                <li><a href="booking.php">Booking</a></li>
                <?php if ($is_admin): ?>
                    <li><a href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Fasilitas Latom Futsal</h1>
            
            <?php if ($is_admin): ?>
                <div class="admin-notice">
                    <p>Anda login sebagai <strong>Admin</strong>. Anda dapat mengelola semua booking.</p>
                </div>
            <?php endif; ?>

            <h2>Pilihan Lapangan</h2>
            <ul>
                <li>Mini Soccer Rp. 500.000 / 2 jam</li>
                <li>Lapangan Rumput Sintesis Rp. 150.000/ jam</li>
                <li>Lapangan Puzzle Rp. 100.000 / jam</li>
            </ul>
            
            <?php if ($is_admin): ?>
                <div class="admin-features">
                    <h2>Fitur Admin</h2>
                    <div class="admin-actions">
                        <a href="admin.php" class="btn-admin">Kelola Booking</a>
                        <a href="edit_prices.php" class="btn-admin">Edit Harga</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-section">
        <h3>Kontak</h3>
        <p><span class="icon">📍</span> Jl. Futsal No. 123, Tokyo</p>
        <p><span class="icon">📞</span> (021) 1234-5678</p>
        <p><span class="icon">✉️</span> info@latomfutsal.com</p>
    </div>

    <style>
.admin-notice {
  background-color: #e3f2fd;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
  border-left: 5px solid #2196F3;
}
        
.admin-features {
  margin-top: 30px;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #dee2e6;
}
        
.admin-actions {
  display: flex;
  gap: 15px;
  margin-top: 15px;
}
        
.btn-admin {
  padding: 10px 20px;
  background-color: #2c3e50;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  transition: all 0.3s ease;
}
        
.btn-admin:hover {
  background-color: #34495e;
  transform: translateY(-2px);
}
    </style>
</body>
</html>