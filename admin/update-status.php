<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

// Ambil data dari POST
$id_reservation = $_POST['id_reservation'];
$status = $_POST['status'];

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

// Query untuk mengambil data reservasi, nomor telepon user, dan nama user
$query = "SELECT u.nama_user, u.no_telp_user, r.merk_mobil, r.durasi, r.company_name 
          FROM reservasi r 
          JOIN user_account u ON r.id_user = u.id_user 
          WHERE r.id_reservation = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_reservation);
$stmt->execute();
$stmt->bind_result($nama_user, $no_telp_user, $merk_mobil, $durasi, $company_name);
$stmt->fetch();
$stmt->close();

// Mengganti karakter '_' dengan spasi pada durasi
$durasi = str_replace('_', ' ', $durasi);

// Query untuk update status
$query = "UPDATE reservasi SET status = ? WHERE id_reservation = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $id_reservation);

// Eksekusi query untuk update status
if ($stmt->execute()) {
    // Menyiapkan pesan untuk dikirim melalui WhatsApp
    $message = "Akastra Rent Car\n";

    $message .= "Kepada $nama_user,\n\n";
    $message .= "Data reservasi:\n";
    $message .= "Merk mobil: $merk_mobil\n";
    $message .= "Durasi: $durasi\n";
    if ($company_name) {
        $message .= "Perusahaan: $company_name\n";
    }
    if ($status == 'disetujui') {
        $message .= "Reservasi Anda disetujui.";
    } else {
        $message .= "Reservasi Anda dibatalkan.";
    }
    $message .= "\n\n";

    // Menghapus karakter selain angka dan mengganti awalan nomor jika perlu
    $no_telp_user = preg_replace('/\D/', '', $no_telp_user);  // Menghapus karakter selain angka

    // Jika nomor telepon dimulai dengan '0', ganti menjadi '62'
    if (substr($no_telp_user, 0, 1) == '0') {
        $no_telp_user = '62' . substr($no_telp_user, 1);
    }

    // Jika nomor telepon sudah dimulai dengan '62', biarkan seperti itu
    if (substr($no_telp_user, 0, 2) != '62') {
        // Jika tidak diawali dengan '62', tambahkan '62' di depan
        $no_telp_user = '62' . $no_telp_user;
    }

    // Pastikan pesan sudah ter-encode dengan benar
    $message = urlencode($message);

    // Format link WhatsApp
    $wa_link = "https://wa.me/$no_telp_user&text=$message";
} else {
    echo "Error: " . $stmt->error;
    exit();
}


$conn->close();
?>

<!-- HTML dengan template Bootstrap -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin AkastraRentCar - Kirim Pesan WA</title>
    <link href="../img/logo.jpg" rel="icon">
    <!-- Tambahkan link Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Kontainer untuk menempatkan tombol di tengah halaman -->
    <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh;">
        <!-- Tombol untuk kirim pesan WA dan kembali ke halaman reservasi-pending.php -->
        <button class="btn btn-primary btn-lg" onclick="window.open('<?php echo $wa_link; ?>', '_blank'); window.location.href = 'reservasi-pending.php';">Kirim Pesan WA dan Kembali</button>
    </div>

    <!-- Tambahkan script Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

