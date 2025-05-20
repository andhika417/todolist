<?php
session_start();
include '../config/db.php';  // Pastikan path ke db.php benar

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $result = mysqli_fetch_assoc($query);

    if ($result && password_verify($password, $result['password'])) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['user_name'] = $result['name'];
        header("Location: ../index.php");  // Pastikan redirect ke index.php setelah login
    } else {
        echo "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Login</title>
</head>

<body>
    <form class="login-container" method="post">
        <h2>Login</h2>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required />

        <button type="submit" name="login">Login</button>
        
        <a href="register.php">Register</a>

    </form>
</body>

</html>