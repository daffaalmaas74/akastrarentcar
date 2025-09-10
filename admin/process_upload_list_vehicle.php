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

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data form
    $merk_mobil = htmlspecialchars($_POST['merk_mobil']);
    $jenis_mobil = htmlspecialchars($_POST['jenis_mobil']);
    $harga_sewa = $_POST['harga_sewa'];
    $bahan_bakar = $_POST['bahan_bakar'];
    $transmisi = $_POST['transmisi'];
    $kapasitas_mesin = $_POST['kapasitas_mesin'];
    $jumlah_bangku = $_POST['jumlah_bangku'];
    $durasi = $_POST['durasi'];

    // Upload gambar
    $gambar_mobil = $_FILES['gambar_mobil'];
    $gambar_name = time() . '_' . $gambar_mobil['name']; // Menggunakan timestamp untuk nama unik
    $gambar_tmp_name = $gambar_mobil['tmp_name'];
    $gambar_size = $gambar_mobil['size'];
    $gambar_error = $gambar_mobil['error'];

    // Cek apakah ada error saat upload gambar
    if ($gambar_error === 0) {
        // Cek format file gambar
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $gambar_extension = strtolower(pathinfo($gambar_name, PATHINFO_EXTENSION));

        if (!in_array($gambar_extension, $allowed_extensions)) {
            die("Hanya file gambar yang diperbolehkan!");
        }

        // Cek ukuran file (maksimal 50MB)
        if ($gambar_size > 50 * 1024 * 1024) {
            die("Ukuran gambar maksimal 50MB!");
        }

        // Tentukan path direktori file (gunakan nama file yang unik)
        $gambar_upload_path = 'uploads/' . $gambar_name;

        // Pindahkan file gambar ke folder uploads
        if (!move_uploaded_file($gambar_tmp_name, $gambar_upload_path)) {
            die("Terjadi kesalahan saat mengupload gambar!");
        }
    } else {
        die("Terjadi kesalahan saat mengupload gambar!");
    }

    // Tentukan tabel berdasarkan pilihan durasi
    switch ($durasi) {
        case 'harian':
            $table = 'list_kendaraan_harian';
            break;
        case 'bulanan':
            $table = 'list_kendaraan_bulanan';
            break;
        case '3_tahun':
            $table = 'list_kendaraan_3_tahun';
            break;
        case '4_tahun':
            $table = 'list_kendaraan_4_tahun';
            break;
        default:
            die("Durasi tidak valid!");
    }

    // Persiapkan query menggunakan prepared statement
    $query = "INSERT INTO $table (merk_mobil, jenis_mobil, gambar_mobil, harga_sewa, bahan_bakar, transmisi, kapasitas_mesin, jumlah_bangku, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    // Persiapkan statement
    $stmt = $conn->prepare($query);

    // Bind parameter dengan tipe data yang sesuai
    $stmt->bind_param('ssssssis', $merk_mobil, $jenis_mobil, $gambar_upload_path, $harga_sewa, $bahan_bakar, $transmisi, $kapasitas_mesin, $jumlah_bangku);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Setelah data berhasil disimpan, redirect dengan parameter kategori
        header("Location: list-kendaraan.php?kategori=" . $_POST['durasi']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }


    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
