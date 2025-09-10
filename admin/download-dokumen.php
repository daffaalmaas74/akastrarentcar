<?php
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

// Ambil ID user dari parameter
$id_user = $_GET['id_user'];

// Ambil data nama_user untuk penamaan folder dalam file RAR
$userData = $conn->query("SELECT nama_user FROM user_account WHERE id_user = $id_user")->fetch_assoc();
$userName = $userData['nama_user'];

// Ambil dokumen dari database
$sql = "
    SELECT d.ktp_user, d.sim_a_user, d.kartu_keluarga_user, d.cover_rekening_tabungan_user, 
           d.domisili_tempat_tinggal_user, d.surat_keterangan_kerja_user, d.kartu_kredit_user, 
           d.ktp_direktur, d.domisili_perusahaan, d.akta_perusahaan, d.siup_perusahaan,
           d.npwp_perusahaan, d.tdp_perusahaan
    FROM dokumen_user d
    WHERE d.id_user = $id_user
";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Path ke WinRAR executable (pastikan pathnya benar)
    $rarExePath = 'C:\\Program Files\\WinRAR\\rar.exe'; // Sesuaikan path ini dengan lokasi WinRAR di sistem Anda

    // Nama file RAR yang akan dibuat
    $rarFile = "dokumen-penyewa-$userName.rar";
    $rarFilePath = sys_get_temp_dir() . "\\" . $rarFile;

    // Siapkan perintah untuk membuat arsip RAR dan membuat folder sesuai nama user
    $command = "\"$rarExePath\" a \"$rarFilePath\" \"$userName\\\" "; // Membuat folder user

    // Loop untuk setiap dokumen dan masukkan ke dalam perintah RAR
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $doc => $filePath) {
            if (!empty($filePath)) {
                // Tentukan path relatif di dalam arsip RAR sesuai dengan nama user
                $relativePath = $userName . '\\' . basename($filePath); // Menyimpan file dalam folder nama_user/
                // Menambahkan file PDF ke dalam folder user di arsip
                $command .= "\"$filePath\" \"$relativePath\" ";
            }
        }
    }

    // Jalankan perintah untuk membuat RAR
    exec($command);

    // Kirim file RAR ke pengguna
    header('Content-Type: application/x-rar-compressed');
    header("Content-Disposition: attachment; filename=$rarFile");
    header('Content-Length: ' . filesize($rarFilePath));
    readfile($rarFilePath);

    // Hapus file RAR setelah dikirim
    unlink($rarFilePath);
}

$conn->close();
?>
