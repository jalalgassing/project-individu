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

// Proses penghapusan booking
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $sql_delete = "DELETE FROM bookings WHERE id = $booking_id";
    mysqli_query($conn, $sql_delete);
}

header("Location: home.php");
exit();
?>