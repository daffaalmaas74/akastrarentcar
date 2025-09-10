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

if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];

    // Query untuk cek apakah nomor telepon sudah ada
    $stmt = $conn->prepare("SELECT no_telp_user FROM user_account WHERE no_telp_user = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "exists"; // Nomor telepon sudah terdaftar
    } else {
        echo "not_exists"; // Nomor telepon belum terdaftar
    }

    $stmt->close();
}
$conn->close();
?>
