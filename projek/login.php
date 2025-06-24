<?php
session_start(); // Mulai session

require_once 'koneksi.php';

$error = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {
                // Login berhasil
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["username"];
                $success = true;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Latom Futsal</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo Latom Futsal" style="height: 40px; vertical-align: middle; margin-right: 10px;">
        <h1>LATOM FUTSAL</h1>
    </div>
    
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br><br>

            <input type="submit" value="Login">
        </form>

        <p>Belum punya akun? <a href="register.php">Register di sini</a>.</p>
    </div>

    <script>
    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?php echo $error; ?>',
            confirmButtonText: 'OK'
        });
    <?php elseif ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil',
            text: 'Anda akan diarahkan ke halaman utama',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'home.php';
        });
    <?php endif; ?>
    </script>
</body>
</html>