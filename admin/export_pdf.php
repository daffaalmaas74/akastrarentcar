<?php
require('libs/fpdf.php');
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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data penyewa dari database
$sql = "
    SELECT r.id_reservation, u.nama_user, u.no_telp_user, r.merk_mobil, r.durasi, r.tanggal_rental_mulai, r.tanggal_rental_berakhir, r.total_harga, r.status_sewa
    FROM reservasi r
    JOIN user_account u ON r.id_user = u.id_user
    WHERE r.status_sewa IN ('berjalan', 'telah selesai')
";
$result = $conn->query($sql);

// Inisialisasi PDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape, milimeter, A4 size
$pdf->AddPage();

// Judul PDF
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 8, 'Laporan List Penyewa', 0, 1, 'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(15, 6, 'No', 1, 0, 'C', true);
$pdf->Cell(35, 6, 'Nama User', 1, 0, 'C', true);
$pdf->Cell(30, 6, 'Phone Number', 1, 0, 'C', true);
$pdf->Cell(90, 6, 'Merk Mobil', 1, 0, 'C', true);
$pdf->Cell(20, 6, 'Durasi', 1, 0, 'C', true);
$pdf->Cell(25, 6, 'Mulai Sewa', 1, 0, 'C', true);
$pdf->Cell(25, 6, 'Berakhir Sewa', 1, 0, 'C', true);
$pdf->Cell(45, 6, 'Total Harga', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);
$no = 1;
$total_harga = 0;
while ($row = $result->fetch_assoc()) {
    $harga = preg_replace("/[^0-9]/", "", $row['total_harga']); // Hanya angka
    $total_harga += intval($harga);

    $pdf->Cell(15, 6, $no++, 1, 0, 'C');
    $pdf->Cell(35, 6, $row['nama_user'], 1, 0, 'L');
    $pdf->Cell(30, 6, $row['no_telp_user'], 1, 0, 'C');
    $pdf->Cell(90, 6, $row['merk_mobil'], 1, 0, 'L');
    $pdf->Cell(20, 6, str_replace('_', ' ', $row['durasi']), 1, 0, 'C');
    $pdf->Cell(25, 6, $row['tanggal_rental_mulai'], 1, 0, 'C');
    $pdf->Cell(25, 6, $row['tanggal_rental_berakhir'], 1, 0, 'C');
    $pdf->Cell(45, 6, 'Rp. ' . number_format(intval($harga), 0, ',', '.'), 1, 1, 'R');
}

// Menambahkan total harga
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(240, 6, 'Total Pendapatan', 1, 0, 'C', true);
$pdf->Cell(45, 6, 'Rp. ' . number_format($total_harga, 0, ',', '.'), 1, 1, 'R', true);

$conn->close();

// Output PDF
$pdf->Output('D', 'list_penyewa.pdf');
?>
