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
$is_admin = $user && $user['is_admin'];

$sql = "SELECT bookings.*, users.username 
        FROM bookings 
        JOIN users ON bookings.user_id = users.id 
        ORDER BY hari, jam_booking";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
            
            <?php if ($is_admin): ?>
                <div class="admin-notice">
                    <p>Anda login sebagai <strong>Admin</strong>. Anda dapat mengelola semua booking.</p>
                </div>
            <?php endif; ?>
            
            <div class="welcome-message">
                <h3>Selamat datang di Latom Futsal!</h3>
                <p>Kami menyediakan lapangan futsal berkualitas tinggi dengan berbagai fasilitas lengkap.</p>
                <p><strong>Jam Operasional:</strong> Setiap hari, 08:00 - 22:00</p>
                <p><strong>Lokasi:</strong> Jl. Futsal No. 123, Tokyo</p>
            </div>

            <h2>Jadwal Booking Lapangan</h2>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Tanggal</th>
                            <th>Jam Booking</th>
                            <th>Lama Sewa</th>
                            <th>Jenis Lapangan</th>
                            <th>Metode Bayar</th>
                            <?php if ($is_admin): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nama"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nomor_hp"]); ?></td>
                                <td><?php echo htmlspecialchars($row["hari"]); ?></td>
                                <td><?php echo htmlspecialchars($row["jam_booking"]); ?></td>
                                <td><?php echo htmlspecialchars($row["lama_sewa"]); ?> Jam</td>
                                <td><?php echo htmlspecialchars($row["jenis_lapangan"]); ?></td>
                                <td><?php echo htmlspecialchars($row["metode_pembayaran"]); ?></td>
                                <?php if ($is_admin): ?>
                                    <td>
                                        <a href="edit_booking.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                        <a href="delete_booking.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirmDelete()">Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="info">Belum ada booking lapangan.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-section">
        <h3>Kontak</h3>
        <p><span class="icon">📍</span> Jl. Futsal No. 123, Tokyo</p>
        <p><span class="icon">📞</span> (021) 1234-5678</p>
        <p><span class="icon">✉️</span> info@latomfutsal.com</p>
    </div>

    <script>
        function confirmDelete() {
            return confirm('Apakah Anda yakin ingin menghapus booking ini?');
        }
    </script>
</body>
</html>