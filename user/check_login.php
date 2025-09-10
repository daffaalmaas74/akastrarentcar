<?php
session_start();

// Cek apakah session 'user_id' ada, menandakan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, alihkan ke halaman login
    header('Location: ../login.php');
    exit();
}
?>
