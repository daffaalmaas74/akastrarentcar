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

// Cek apakah email sudah terdaftar
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT email_user FROM user_account WHERE email_user = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "exists"; // Email sudah terdaftar
    } else {
        echo "available"; // Email belum terdaftar
    }

    $stmt->close();
    $conn->close();
}
?>
