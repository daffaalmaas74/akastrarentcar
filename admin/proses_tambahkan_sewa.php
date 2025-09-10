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
    die(json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]));
}

// Ambil data dari form
$reservation_id = $_POST['reservation_id'];
$total_harga = $_POST['total_harga'];
$tanggal_rental_mulai = $_POST['tanggal_rental_mulai'];
$tanggal_rental_berakhir = $_POST['tanggal_rental_berakhir'];

// Mendapatkan data user untuk penamaan file
$query_user = "SELECT nama_user, id_user FROM user_account WHERE id_user = (SELECT id_user FROM reservasi WHERE id_reservation = ?)";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$nama_user = $user['nama_user'];
$id_user = $user['id_user'];

// Validasi format file dan ukuran
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
$max_size = 5 * 1024 * 1024; // 5MB

// Fungsi untuk validasi dan penamaan file
function validate_upload($file, $nama_user, $id_user, $reservation_id, $prefix) {
    global $allowed_types, $max_size;
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(["status" => "error", "message" => "Hanya format gambar (JPEG, PNG, GIF) dan PDF yang diperbolehkan."]);
        exit();
    }
    if ($file['size'] > $max_size) {
        echo json_encode(["status" => "error", "message" => "Ukuran file tidak boleh lebih dari 5MB."]);
        exit();
    }
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $file_name = $nama_user . '-' . $id_user . '-' . $prefix . '-' . $reservation_id . '.' . $file_ext;
    return "uploads/" . $file_name;
}

// Proses upload bukti pembayaran
$bukti_pembayaran = $_FILES["bukti_pembayaran"];
$target_file_bukti_pembayaran = validate_upload($bukti_pembayaran, $nama_user, $id_user, $reservation_id, 'bukti_pembayaran');
move_uploaded_file($bukti_pembayaran["tmp_name"], $target_file_bukti_pembayaran);

// Proses upload bukti serah terima
$bukti_serah_terima = $_FILES["bukti_serah_terima"];
$target_file_bukti_serah_terima = validate_upload($bukti_serah_terima, $nama_user, $id_user, $reservation_id, 'bukti_serah_terima');
move_uploaded_file($bukti_serah_terima["tmp_name"], $target_file_bukti_serah_terima);


// Update status sewa dan tambahkan data sewa
$sql = "UPDATE reservasi SET 
    total_harga = ?, 
    tanggal_rental_mulai = ?, 
    tanggal_rental_berakhir = ?, 
    bukti_pembayaran = ?, 
    bukti_serah_terima = ?, 
    status_sewa = 'berjalan'
    WHERE id_reservation = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $total_harga, $tanggal_rental_mulai, $tanggal_rental_berakhir, $target_file_bukti_pembayaran, $target_file_bukti_serah_terima, $reservation_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$conn->close();
?>
