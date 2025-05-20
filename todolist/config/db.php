<?php
$host = 'localhost';
$user = 'root';  // Username MySQL, biasanya 'root' di XAMPP
$pass = '';      // Password, biasanya kosong di XAMPP
$db   = 'todolist';  // Nama database yang sudah kamu buat

// Koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>