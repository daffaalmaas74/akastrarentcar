<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_user'])) {
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "akastrarentcar", 3307);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Escape id_user untuk mencegah SQL Injection
    $id_user = $conn->real_escape_string($_POST['id_user']);

    // Query delete
    $query = "DELETE FROM user_account WHERE id_user = '$id_user'";

    if ($conn->query($query) === TRUE) {
        $_SESSION['success_message'] = "Akun user berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus akun user.";
    }

    $conn->close();

    // Redirect kembali ke halaman list user
    header("Location: list-akun-user.php");
    exit();
}
?>
