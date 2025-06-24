<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Periksa apakah user adalah admin
$sql_check_admin = "SELECT is_admin FROM users WHERE id = " . $_SESSION["user_id"];
$result = mysqli_query($conn, $sql_check_admin);
$row = mysqli_fetch_assoc($result);

if (!$row || !$row['is_admin']) {
    header("Location: home.php");
    exit();
}

// Handle delete booking
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM bookings WHERE id = $id";
    mysqli_query($conn, $sql_delete);
    header("Location: admin.php");
    exit();
}

// Handle edit booking
$error = '';
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
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
                        WHERE id = $id";
        
        if (mysqli_query($conn, $sql_update)) {
            $success = true;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Ambil semua data booking
$sql_bookings = "SELECT bookings.*, users.username 
                FROM bookings 
                JOIN users ON bookings.user_id = users.id 
                ORDER BY hari, jam_booking";
$result_bookings = mysqli_query($conn, $sql_bookings);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL - ADMIN PANEL</h1>
    </div>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Admin Panel</h1>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data booking berhasil diperbarui',
                        confirmButtonText: 'OK'
                    });
                </script>
            <?php endif; ?>

            <h2>Daftar Booking</h2>
            
            <?php if (mysqli_num_rows($result_bookings) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Nomor HP</th>
                            <th>Tanggal</th>
                            <th>Jam Booking</th>
                            <th>Lama Sewa</th>
                            <th>Jenis Lapangan</th>
                            <th>Metode Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = mysqli_fetch_assoc($result_bookings)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking["nama"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["username"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["nomor_hp"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["hari"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["jam_booking"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["lama_sewa"]); ?> Jam</td>
                                <td><?php echo htmlspecialchars($booking["jenis_lapangan"]); ?></td>
                                <td><?php echo htmlspecialchars($booking["metode_pembayaran"]); ?></td>
                                <td>
                                    <button onclick="openEditModal(<?php echo $booking['id']; ?>, 
                                        '<?php echo addslashes($booking["nama"]); ?>',
                                        '<?php echo addslashes($booking["nomor_hp"]); ?>',
                                        '<?php echo $booking["hari"]; ?>',
                                        '<?php echo $booking["jam_booking"]; ?>',
                                        '<?php echo $booking["metode_pembayaran"]; ?>',
                                        '<?php echo $booking["jenis_lapangan"]; ?>',
                                        '<?php echo $booking["lama_sewa"]; ?>')">Edit</button>
                                    
                                    <button onclick="confirmDelete(<?php echo $booking['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="info">Belum ada booking lapangan.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Booking</h2>
            <form id="editForm" method="post">
                <input type="hidden" name="edit_id" id="edit_id">
                
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="modal_nama" name="nama" required>

                <label for="nomor_hp">Nomor HP:</label>
                <input type="text" id="modal_nomor_hp" name="nomor_hp" required>

                <label for="hari">Tanggal Booking:</label>
                <input type="date" id="modal_hari" name="hari" required>

                <label for="jam_booking">Jam Mulai:</label>
                <input type="time" id="modal_jam_booking" name="jam_booking" min="08:00" max="21:00" required>

                <label for="jenis_lapangan">Jenis Lapangan:</label>
                <select id="modal_jenis_lapangan" name="jenis_lapangan" required>
                    <option value="">Pilih Lapangan</option>
                    <option value="Mini Soccer">Mini Soccer</option>
                    <option value="Lapangan Rumput Sintesis">Lapangan Rumput Sintesis</option>
                    <option value="Lapangan Puzzle">Lapangan Puzzle</option>
                </select>

                <label for="lama_sewa">Lama Sewa (jam):</label>
                <select id="modal_lama_sewa" name="lama_sewa" required>
                    <option value="1">1 Jam</option>
                    <option value="2">2 Jam</option>
                    <option value="3">3 Jam</option>
                    <option value="4">4 Jam</option>
                </select>

                <label for="metode_pembayaran">Metode Pembayaran:</label>
                <select id="modal_metode_pembayaran" name="metode_pembayaran" required>
                    <option value="">Pilih Pembayaran</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="OVO">OVO</option>
                    <option value="Gopay">Gopay</option>
                    <option value="Dana">Dana</option>
                    <option value="Sea Bank">Sea Bank</option>
                </select>

                <input type="submit" value="Simpan Perubahan">
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka modal edit
        function openEditModal(id, nama, nomor_hp, hari, jam_booking, metode_pembayaran, jenis_lapangan, lama_sewa) {
            document.getElementById('edit_id').value = id;
            document.getElementById('modal_nama').value = nama;
            document.getElementById('modal_nomor_hp').value = nomor_hp;
            document.getElementById('modal_hari').value = hari;
            document.getElementById('modal_jam_booking').value = jam_booking;
            document.getElementById('modal_metode_pembayaran').value = metode_pembayaran;
            document.getElementById('modal_jenis_lapangan').value = jenis_lapangan;
            document.getElementById('modal_lama_sewa').value = lama_sewa;
            
            document.getElementById('editModal').style.display = 'block';
        }

        // Fungsi untuk menutup modal edit
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Fungsi konfirmasi hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak dapat mengembalikan data yang telah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin.php?delete=' + id;
                }
            });
        }

        // Tutup modal ketika klik di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>

    <div class="footer-section">
        <h3>Kontak</h3>
        <p><span class="icon">📍</span> Jl. Futsal No. 123, Tokyo</p>
        <p><span class="icon">📞</span> (021) 1234-5678</p>
        <p><span class="icon">✉️</span> info@latomfutsal.com</p>
    </div>
</body>
</html>