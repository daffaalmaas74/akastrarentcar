<?php
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

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Cek apakah username sudah terdaftar
    $stmt = $conn->prepare("SELECT username_admin FROM admin_account WHERE username_admin = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "exists"; // Username sudah ada
    } else {
        echo "available"; // Username tersedia
    }

    $stmt->close();
}

$conn->close();
?>
