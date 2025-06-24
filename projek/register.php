<?php
require_once 'koneksi.php';

$error = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Validasi input (tambahkan validasi yang lebih kuat)
    if (empty($username) || empty($password) || empty($email)) {
        $error = "Semua field harus diisi.";
    } else {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
            $message = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL</h1>
    </div>
   
    <div class="container">
        <h2>Register</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email"><br><br>

            <input type="submit" value="Register">
        </form>

        <p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
    </div>

    <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $message; ?>',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php'; // Redirect ke halaman login
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
