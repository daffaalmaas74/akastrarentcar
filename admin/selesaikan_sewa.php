<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "Anda harus login terlebih dahulu.";
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

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah parameter id_reservation ada
if (isset($_POST['id_reservation'])) {
    $id_reservation = $_POST['id_reservation'];

    // Ambil data reservasi dan user
    $sql = "SELECT r.merk_mobil, r.company_name, r.durasi, r.tanggal_rental_mulai, r.tanggal_rental_berakhir, u.nama_user, u.no_telp_user
            FROM reservasi r
            JOIN user_account u ON r.id_user = u.id_user
            WHERE r.id_reservation = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_reservation);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_user = $row['nama_user'];
        $no_telp_user = preg_replace('/[^0-9]/', '', $row['no_telp_user']);
        $merk_mobil = $row['merk_mobil'];
        $company_name = $row['company_name'] ? $row['company_name'] : '-';
        $durasi = $row['durasi'];
        $tanggal_mulai = $row['tanggal_rental_mulai'];
        $tanggal_berakhir = $row['tanggal_rental_berakhir'];

        // Update status sewa menjadi telah selesai
        $update_sql = "UPDATE reservasi SET status_sewa = 'telah selesai' WHERE id_reservation = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $id_reservation);
        
        if ($update_stmt->execute()) {
            // Format pesan WhatsApp
            $pesan = "From: AkastraRentCar\n"
                   . "To: $nama_user\n"
                   . "Informasi mengenai penyewaan kendaraan:\n"
                   . "Merk Mobil: $merk_mobil\n"
                   . "Company Name: $company_name\n"
                   . "Durasi: $durasi\n"
                   . "Tanggal Mulai Rental: $tanggal_mulai\n"
                   . "Tanggal Berakhir Rental: $tanggal_berakhir\n\n"
                   . "Telah selesai masa sewa dan kendaraan telah dikembalikan.\n\n"
                   . "Terima kasih telah menggunakan layanan kami. Kami sangat menghargai pendapat Anda dan ingin mendengar pengalaman Anda.\n"
                   . "Kami ingin mengundang Anda untuk mengisi Form Testimonial di link berikut:\n"
                   . "http://localhost/akastra/testimonial.php\n\n"
                   . "Terima kasih atas waktu dan feedback yang Anda berikan!\n"
                   . "AkastraRentCar";
            
            // Kirim pesan WhatsApp menggunakan Zenziva
            $userkey = "3cf89ed83802";
            $passkey = "dd95620e24ad1cebe0354837";
            $url = "https://console.zenziva.net/wareguler/api/sendWA/";
            $data = [
                'userkey' => $userkey,
                'passkey' => $passkey,
                'to' => $no_telp_user,
                'message' => $pesan
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_RETURNTRANSFER => true
            ]);
            
            $response = curl_exec($curl);
            curl_close($curl);

            echo "Masa sewa telah selesai dan pesan WA telah dikirim.";
        } else {
            echo "Gagal menyelesaikan masa sewa.";
        }
        
        $update_stmt->close();
    } else {
        echo "Data reservasi tidak ditemukan.";
    }

    $stmt->close();
} else {
    echo "ID reservasi tidak ditemukan.";
}

// Menutup koneksi
$conn->close();
?>