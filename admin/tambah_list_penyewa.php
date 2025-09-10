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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_reservation = $_POST['id_reservation'];
    $id_user = $_POST['id_user'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
    $total_harga = $_POST['total_harga'];

    // Upload file bukti pembayaran dan serah terima
    $bukti_pembayaran = $_FILES['bukti_pembayaran']['name'];
    $bukti_serah_terima = $_FILES['bukti_serah_terima']['name'];
    move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], "uploads/" . $bukti_pembayaran);
    move_uploaded_file($_FILES['bukti_serah_terima']['tmp_name'], "uploads/" . $bukti_serah_terima);

    // Koneksi ke database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data ke tabel list_penyewa
    $sql = "INSERT INTO list_penyewa (id_user, id_reservation, merk_mobil, tanggal_mulai, tanggal_akhir, total_harga, bukti_pembayaran, bukti_serah_terima) 
            VALUES ('$id_user', '$id_reservation', (SELECT merk_mobil FROM reservasi WHERE id_reservation = '$id_reservation'), '$tanggal_mulai', '$tanggal_akhir', '$total_harga', '$bukti_pembayaran', '$bukti_serah_terima')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil ditambahkan";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
