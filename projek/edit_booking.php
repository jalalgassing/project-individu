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

// Ambil data booking yang akan diedit
$booking_id = $_GET['id'] ?? 0;
$sql_booking = "SELECT * FROM bookings WHERE id = $booking_id";
$result_booking = mysqli_query($conn, $sql_booking);
$booking = mysqli_fetch_assoc($result_booking);

if (!$booking) {
    header("Location: home.php");
    exit();
}

// Proses update data
$error = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama"];
    $nomor_hp = $_POST["nomor_hp"];
    $hari = $_POST["hari"];
    $jam_booking = $_POST["jam_booking"];
    $metode_pembayaran = $_POST["metode_pembayaran"];
    $jenis_lapangan = $_POST["jenis_lapangan"];
    $lama_sewa = $_POST["lama_sewa"];

    // Validasi input
    if (empty($nama) || empty($nomor_hp) || empty($hari) || empty($jam_booking) || 
        empty($metode_pembayaran) || empty($jenis_lapangan) || empty($lama_sewa)) {
        $error = "Semua field harus diisi.";
    } elseif ($lama_sewa < 1 || $lama_sewa > 4) {
        $error = "Lama sewa harus antara 1-4 jam.";
    } else {
        $sql_update = "UPDATE bookings SET 
                      nama = '$nama',
                      nomor_hp = '$nomor_hp',
                      hari = '$hari',
                      jam_booking = '$jam_booking',
                      metode_pembayaran = '$metode_pembayaran',
                      jenis_lapangan = '$jenis_lapangan',
                      lama_sewa = '$lama_sewa'
                      WHERE id = $booking_id";
        
        if (mysqli_query($conn, $sql_update)) {
            $success = true;
            header("Location: home.php?success=1");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL - EDIT BOOKING</h1>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="fasilitas.php">Fasilitas</a></li>
                <li><a href="booking.php">Booking</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Edit Booking</h1>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($booking['nama']); ?>" required>

                <label for="nomor_hp">Nomor HP:</label>
                <input type="text" id="nomor_hp" name="nomor_hp" value="<?php echo htmlspecialchars($booking['nomor_hp']); ?>" required>

                <label for="hari">Tanggal Booking:</label>
                <input type="date" id="hari" name="hari" value="<?php echo htmlspecialchars($booking['hari']); ?>" required>

                <label for="jam_booking">Jam Mulai:</label>
                <input type="time" id="jam_booking" name="jam_booking" value="<?php echo htmlspecialchars($booking['jam_booking']); ?>" min="08:00" max="21:00" required>

                <label for="jenis_lapangan">Jenis Lapangan:</label>
                <select id="jenis_lapangan" name="jenis_lapangan" required>
                    <option value="">Pilih Lapangan</option>
                    <option value="Mini Soccer" <?php echo $booking['jenis_lapangan'] == 'Mini Soccer' ? 'selected' : ''; ?>>Mini Soccer</option>
                    <option value="Lapangan Rumput Sintesis" <?php echo $booking['jenis_lapangan'] == 'Lapangan Rumput Sintesis' ? 'selected' : ''; ?>>Lapangan Rumput Sintesis</option>
                    <option value="Lapangan Puzzle" <?php echo $booking['jenis_lapangan'] == 'Lapangan Puzzle' ? 'selected' : ''; ?>>Lapangan Puzzle</option>
                </select>

                <label for="lama_sewa">Lama Sewa (jam):</label>
                <select id="lama_sewa" name="lama_sewa" required>
                    <option value="1" <?php echo $booking['lama_sewa'] == 1 ? 'selected' : ''; ?>>1 Jam</option>
                    <option value="2" <?php echo $booking['lama_sewa'] == 2 ? 'selected' : ''; ?>>2 Jam</option>
                    <option value="3" <?php echo $booking['lama_sewa'] == 3 ? 'selected' : ''; ?>>3 Jam</option>
                    <option value="4" <?php echo $booking['lama_sewa'] == 4 ? 'selected' : ''; ?>>4 Jam</option>
                </select>

                <label for="metode_pembayaran">Metode Pembayaran:</label>
                <select id="metode_pembayaran" name="metode_pembayaran" required>
                    <option value="">Pilih Pembayaran</option>
                    <option value="Transfer Bank" <?php echo $booking['metode_pembayaran'] == 'Transfer Bank' ? 'selected' : ''; ?>>Transfer Bank</option>
                    <option value="OVO" <?php echo $booking['metode_pembayaran'] == 'OVO' ? 'selected' : ''; ?>>OVO</option>
                    <option value="Gopay" <?php echo $booking['metode_pembayaran'] == 'Gopay' ? 'selected' : ''; ?>>Gopay</option>
                    <option value="Dana" <?php echo $booking['metode_pembayaran'] == 'Dana' ? 'selected' : ''; ?>>Dana</option>
                    <option value="Sea Bank" <?php echo $booking['metode_pembayaran'] == 'Sea Bank' ? 'selected' : ''; ?>>Sea Bank</option>
                </select>

                <input type="submit" value="Simpan Perubahan">
                <a href="home.php" class="btn-cancel">Batal</a>
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