<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "latom_futsal_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
