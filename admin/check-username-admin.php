<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "akastrarentcar";
$port = 3307;

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if username is taken
if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Query to check if the username exists
    $query = "SELECT * FROM admin_account WHERE username_admin = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'exists'; // Username is taken
    } else {
        echo 'available'; // Username is available
    }
}
?>
