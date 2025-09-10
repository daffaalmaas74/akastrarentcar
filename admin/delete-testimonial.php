<?php
session_start();

// Cek jika admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "akastrarentcar";
$port = 3307;

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil id_testimoni dari POST
$id_testimoni = $_POST['id_testimoni'];

// Query untuk menghapus testimonial
$query = "DELETE FROM testimoni WHERE id_testimoni = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_testimoni);

if ($stmt->execute()) {
    $_SESSION['message'] = "Testimonial berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus testimonial.";
}

$stmt->close();
$conn->close();

// Redirect kembali ke list-testimonial.php
header("Location: list-testimonial.php");
exit();
?>
