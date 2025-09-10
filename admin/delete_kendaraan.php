<?php
session_start();

// Cek jika admin sudah login, jika tidak arahkan ke login.php
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

// Ambil ID kendaraan yang akan dihapus dan kategori
if (isset($_POST['id_kendaraan']) && isset($_POST['kategori'])) {
    $id_kendaraan = $_POST['id_kendaraan'];
    $kategori = $_POST['kategori'];

    // Tentukan tabel berdasarkan kategori
    if ($kategori == 'bulanan') {
        $table = 'list_kendaraan_bulanan';
    } elseif ($kategori == '3_tahun') {
        $table = 'list_kendaraan_3_tahun';
    } elseif ($kategori == '4_tahun') {
        $table = 'list_kendaraan_4_tahun';
    } else {
        // Default kategori adalah 'harian'
        $table = 'list_kendaraan_harian';
    }

    // Query untuk menghapus kendaraan berdasarkan ID dan kategori
    $query = "DELETE FROM $table WHERE id_kendaraan = ?";

    // Persiapkan statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameter dan eksekusi
        $stmt->bind_param("i", $id_kendaraan);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect kembali ke halaman kategori yang sesuai setelah berhasil menghapus
    header("Location: list-kendaraan.php?kategori=" . $kategori);
    exit();
}

// Tutup koneksi
$conn->close();
?>
