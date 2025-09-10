<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_admin'])) {
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "akastrarentcar");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Escape id_user untuk mencegah SQL Injection
    $id_admin = $conn->real_escape_string($_POST['id_admin']);

    // Query delete
    $query = "DELETE FROM admin_account WHERE id_admin = '$id_admin'";

    if ($conn->query($query) === TRUE) {
        $_SESSION['success_message'] = "Akun admin berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus akun admin.";
    }

    $conn->close();

    // Redirect kembali ke halaman list user
    header("Location: list-akun-admin.php");
    exit();
}
?>
