<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "akastrarentcar";
$port = 3307;

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname, $port);
// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menentukan tabel yang digunakan berdasarkan tombol yang dipilih
$table = "list_kendaraan_harian"; // Default untuk sewa harian
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    switch ($category) {
        case 'bulanan':
            $table = "list_kendaraan_bulanan";
            break;
        case '3tahun':
            $table = "list_kendaraan_3_tahun";
            break;
        case '4tahun':
            $table = "list_kendaraan_4_tahun";
            break;
    }
}

// Mendapatkan data pencarian dari input
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data kendaraan berdasarkan kategori dan pencarian
$sql = "SELECT * FROM $table WHERE merk_mobil LIKE '%$searchTerm%' ORDER BY merk_mobil ASC";
$result = $conn->query($sql);

// Menampilkan hasil pencarian dalam bentuk tabel
if ($result->num_rows > 0) :
    $no = 1;
    while ($row = $result->fetch_assoc()) :
?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td class="text-center"><?php echo $row['merk_mobil']; ?></td>
            <td class="text-center"><?php echo $row['harga_sewa']; ?></td>
            <td class="text-center"><a href="#" class="spec-link" onclick="showSpecifications('<?php echo $row['merk_mobil']; ?>', '<?php echo $row['jenis_mobil']; ?>', '<?php echo $row['transmisi']; ?>', '<?php echo $row['bahan_bakar']; ?>', '<?php echo $row['kapasitas_mesin']; ?>', '<?php echo $row['jumlah_bangku']; ?>', '<?php echo $row['gambar_mobil']; ?>')">Lihat Spesifikasi</a></td>
        </tr>
<?php
    endwhile;
else :
?>
    <tr>
        <td colspan="4">Tidak ada data kendaraan</td>
    </tr>
<?php
endif;

// Menutup koneksi
$conn->close();
?>
