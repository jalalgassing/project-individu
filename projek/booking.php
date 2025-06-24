<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

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
    $user_id = $_SESSION["user_id"];

    // Validasi input
    if (empty($nama) || empty($nomor_hp) || empty($hari) || empty($jam_booking) || 
        empty($metode_pembayaran) || empty($jenis_lapangan) || empty($lama_sewa)) {
        $error = "Semua field harus diisi.";
    } elseif ($lama_sewa < 1 || $lama_sewa > 4) {
        $error = "Lama sewa harus antara 1-4 jam.";
    } else {
        // Hitung jam akhir
        $jam_akhir = date('H:i', strtotime($jam_booking . ' + ' . $lama_sewa . ' hours'));
        
        // Periksa apakah melebihi jam operasional
        if ($jam_akhir > '22:00') {
            $error = "Lama sewa melebihi jam operasional (22:00)";
        } else {
            // Periksa konflik booking
            $sql_check = "SELECT * FROM bookings WHERE hari = '$hari' AND jenis_lapangan = '$jenis_lapangan' AND (
                (jam_booking <= '$jam_booking' AND ADDTIME(jam_booking, CONCAT(lama_sewa, ':00:00')) > '$jam_booking') OR
                (jam_booking < '$jam_akhir' AND ADDTIME(jam_booking, CONCAT(lama_sewa, ':00:00')) >= '$jam_akhir') OR
                (jam_booking >= '$jam_booking' AND ADDTIME(jam_booking, CONCAT(lama_sewa, ':00:00')) <= '$jam_akhir')
            )";
            
            $result_check = mysqli_query($conn, $sql_check);

            if (mysqli_num_rows($result_check) > 0) {
                $error = "Maaf, lapangan sudah dibooking pada jam tersebut.";
            } else {
                // Insert data ke database
                $sql = "INSERT INTO bookings (user_id, nama, nomor_hp, hari, jam_booking, metode_pembayaran, jenis_lapangan, lama_sewa) 
                        VALUES ('$user_id', '$nama', '$nomor_hp', '$hari', '$jam_booking', '$metode_pembayaran', '$jenis_lapangan', '$lama_sewa')";

                if (mysqli_query($conn, $sql)) {
                    $success = true;
                    $message = "Booking berhasil untuk $lama_sewa jam!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking - Latom Futsal</title>
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Booking Lapangan</h1>

            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" required>

                <label for="nomor_hp">Nomor HP:</label>
                <input type="text" id="nomor_hp" name="nomor_hp" required>

                <label for="hari">Tanggal Booking:</label>
                <input type="date" id="hari" name="hari" required>

                <label for="jam_booking">Jam Mulai:</label>
                <input type="time" id="jam_booking" name="jam_booking" min="08:00" max="21:00" required>

                <label for="jenis_lapangan">Jenis Lapangan:</label>
                <select id="jenis_lapangan" name="jenis_lapangan" required>
                    <option value="">Pilih Lapangan</option>
                    <option value="Mini Soccer">Mini Soccer</option>
                    <option value="Lapangan Rumput Sintesis">Lapangan Rumput Sintesis</option>
                    <option value="Lapangan Puzzle">Lapangan Puzzle</option>
                </select>

                <label for="lama_sewa">Lama Sewa (jam):</label>
                <select id="lama_sewa" name="lama_sewa" required>
                    <option value="1">1 Jam</option>
                    <option value="2">2 Jam</option>
                    <option value="3">3 Jam</option>
                    <option value="4">4 Jam</option>
                </select>

                <label for="metode_pembayaran">Metode Pembayaran:</label>
                <select id="metode_pembayaran" name="metode_pembayaran" required>
                    <option value="">Pilih Pembayaran</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="OVO">OVO</option>
                    <option value="Gopay">Gopay</option>
                    <option value="Dana">Dana</option>
                    <option value="Sea Bank">Sea Bank</option>
                </select>

                <input type="submit" value="Booking">
            </form>
        </div>
    </div>

    <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Booking Berhasil!',
            text: '<?php echo $message; ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'home.php';
        });
    </script>
    <?php endif; ?>

    <div class="footer-section">
        <h3>Kontak</h3>
        <p><span class="icon">📍</span> Jl. Futsal No. 123, Tokyo</p>
        <p><span class="icon">📞</span> (021) 1234-5678</p>
        <p><span class="icon">✉️</span> info@latomfutsal.com</p>
    </div>
</body>
</html>