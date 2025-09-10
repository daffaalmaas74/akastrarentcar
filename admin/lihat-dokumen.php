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
$row = $result->fetch_assoc();

// Array dokumen yang wajib diupload untuk bagian personal
$personalDocs = [
    'ktp_user', 'sim_a_user', 'kartu_keluarga_user', 'cover_rekening_tabungan_user', 
    'domisili_tempat_tinggal_user', 'surat_keterangan_kerja_user', 'kartu_kredit_user'
];

// Cek apakah semua dokumen personal telah diunggah
$allPersonalDocumentsUploaded = true;
foreach ($personalDocs as $doc) {
    if (empty($row[$doc])) {
        $allPersonalDocumentsUploaded = false;
        break; // Keluar loop jika ada dokumen yang kosong
    }
}

if ($result->num_rows > 0) {
    echo "<h5>Dokumen Personal</h5>";
    foreach ($personalDocs as $doc) {
        if (!empty($row[$doc])) {
            // Ganti 'user' menjadi 'penyewa' pada nama dokumen
            $docLabel = str_replace('user', 'penyewa', ucfirst(str_replace('_', ' ', $doc)));
            echo "<div class='w-100 mb-2'><button class='btn btn-primary btn-block' onclick='openPDF(\"" . $row[$doc] . "\")'>" . $docLabel . "</button></div>";
        }
    }

    echo "<br><h5>Dokumen Perusahaan</h5>";
    foreach (['ktp_direktur', 'domisili_perusahaan', 'akta_perusahaan', 'siup_perusahaan',
             'npwp_perusahaan', 'tdp_perusahaan'] as $doc) {
        if (!empty($row[$doc])) {
            // Ganti 'user' menjadi 'penyewa' pada nama dokumen
            $docLabel = ucfirst(str_replace('_', ' ', $doc));
            echo "<div class='w-100 mb-2'><button class='btn btn-primary btn-block' onclick='openPDF(\"" . $row[$doc] . "\")'>" . $docLabel . "</button></div>";
        }
    }
}

// Jika semua dokumen personal telah diupload, tampilkan tombol download
if ($allPersonalDocumentsUploaded) {
    echo "<script>document.getElementById('downloadAllButton').style.display = 'inline-block';</script>";
} else {
    echo "<script>document.getElementById('downloadAllButton').style.display = 'none';</script>";
}

$conn->close();
?>

<script>
    function openPDF(filePath) {
        // Membuka file PDF di tab baru
        window.open(filePath, '_blank');
    }
</script>
