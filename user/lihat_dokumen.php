<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Dapatkan parameter file dari URL
if (isset($_GET['file'])) {
    $file = $_GET['file'];
   // Path ke file sebenarnya
$file_path = __DIR__ . '/../admin/uploads/dokumen-user/' . basename($file);

// Periksa apakah file ada
if (file_exists($file_path)) {
    // Header untuk menampilkan PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit();
} else {
    echo "File tidak ditemukan.";
}

} else {
    echo "Parameter file tidak valid.";
}
?>
