<?php
session_start();

// Cek jika admin sudah login, jika tidak arahkan ke login.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

// Cek apakah ada id_reservation yang diterima
if (isset($_POST['id_reservation'])) {
    $id_reservation = $_POST['id_reservation'];

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

    // Query untuk menghapus reservasi berdasarkan id_reservation
    $query = "DELETE FROM reservasi WHERE id_reservation = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_reservation);

    // Eksekusi query
    if ($stmt->execute()) {
        // Redirect ke halaman setelah penghapusan sukses
        header("Location: list-reservasi.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Menutup koneksi
    $stmt->close();
    $conn->close();
}
?>
