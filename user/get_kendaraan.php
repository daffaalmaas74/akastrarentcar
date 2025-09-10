<?php
if (isset($_GET['durasi'])) {
    $durasi = $_GET['durasi'];

    // Tentukan tabel kendaraan berdasarkan durasi
    $tabel_kendaraan = '';
    if ($durasi === 'harian') {
        $tabel_kendaraan = 'list_kendaraan_harian';
    } elseif ($durasi === 'bulanan') {
        $tabel_kendaraan = 'list_kendaraan_bulanan';
    } elseif ($durasi === '3_tahun') {
        $tabel_kendaraan = 'list_kendaraan_3_tahun';
    } elseif ($durasi === '4_tahun') {
        $tabel_kendaraan = 'list_kendaraan_4_tahun';
    }

    if ($tabel_kendaraan) {
        // Koneksi ke database
        $conn = new mysqli('localhost', 'root', '', 'akastrarentcar', '3307');

        // Periksa koneksi
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Ambil data kendaraan dari tabel yang sesuai
        $sql = "SELECT id_kendaraan, merk_mobil FROM $tabel_kendaraan";
        $result = $conn->query($sql);

        $kendaraan_data = array(); // Menyimpan data kendaraan

        if ($result->num_rows > 0) {
            // Simpan hasil kendaraan dalam array
            while ($row = $result->fetch_assoc()) {
                $kendaraan_data[] = array(
                    'id' => $row['id_kendaraan'],
                    'text' => $row['merk_mobil']
                );
            }

            // Mengirim data dalam format JSON agar dapat digunakan oleh Select2
            echo json_encode($kendaraan_data);
        } else {
            echo json_encode(array());
        }

        $conn->close();
    } else {
        echo json_encode(array());
    }
} else {
    echo json_encode(array());
}
?>
