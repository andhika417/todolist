<?php
session_start();
include '../config/db.php';  // Pastikan path ke db.php benar

if (isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $query  = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $result = mysqli_fetch_assoc($query);

    if ($result) {
        echo "Email sudah terdaftar!";
    } else {
        // Daftarkan user baru
        $insert_query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $insert_query)) {
            echo "Registrasi berhasil!";
            header("Location: login.php");
        } else {
            echo "Terjadi kesalahan saat mendaftar!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Document</title>
</head>
<body>
    <form class="login-container" method="post">
        <h2>Register</h2>
        <label for="name">Username:</label>
        <input type="text" name="name" placeholder="Nama" required>

        <label for="email">email:</label>
        <input type="email" name="email" placeholder="Email" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="register">Register</button>
        <a href="login.php">login</a>
    </form>
</body>
</html>