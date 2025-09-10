<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
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

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT nama_user, email_user, no_telp_user FROM user_account WHERE id_user = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
    $nama_user = $user_data['nama_user'];
    $email_user = $user_data['email_user'];
    $no_telp_user = $user_data['no_telp_user'];
} else {
    die("User data not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $durasi = $_POST['durasi'];
    $id_kendaraan = $_POST['id_kendaraan'];
    $company_name = empty($_POST['company_name']) ? NULL : $_POST['company_name']; // Menyimpan NULL jika kosong

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
        // Ambil merk_mobil berdasarkan id_kendaraan dan durasi yang dipilih
        $sql_kendaraan = "SELECT merk_mobil FROM $tabel_kendaraan WHERE id_kendaraan = ?";
        $stmt_kendaraan = $conn->prepare($sql_kendaraan);
        $stmt_kendaraan->bind_param("i", $id_kendaraan);
        $stmt_kendaraan->execute();
        $result_kendaraan = $stmt_kendaraan->get_result();

        if ($result_kendaraan->num_rows > 0) {
            $kendaraan_data = $result_kendaraan->fetch_assoc();
            $merk_mobil = $kendaraan_data['merk_mobil'];

            // Masukkan data ke tabel reservasi (tanpa nama_user, email_user, dan no_telp_user)
            $sql_booking = "INSERT INTO reservasi (id_user, durasi, id_kendaraan, merk_mobil, company_name, waktu_reservasi)
                            VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt_booking = $conn->prepare($sql_booking);
            $stmt_booking->bind_param("issss", $user_id, $durasi, $id_kendaraan, $merk_mobil, $company_name);

            if ($stmt_booking->execute()) {
                // Kirim pesan WA ke admin
                $userkey = '3cf89ed83802';
                $passkey = 'dd95620e24ad1cebe0354837';
                $telepon = '085694737918'; // Nomor admin

                // Cek jika company_name kosong, gunakan '-' dalam pesan WA
                $company_name_wa = $company_name ? $company_name : '-';

                $message = "Halo AkastraRentCar,\n\nTerdapat reservasi baru yaitu :\n\n"
                        . "Nama: $nama_user\n"
                        . "No. Telp: $no_telp_user\n"
                        . "Perusahaan: $company_name_wa\n"
                        . "Merk Mobil: $merk_mobil\n"
                        . "Durasi: $durasi";

                $url = 'https://console.zenziva.net/wareguler/api/sendWA/';
                $curlHandle = curl_init();
                curl_setopt($curlHandle, CURLOPT_URL, $url);
                curl_setopt($curlHandle, CURLOPT_HEADER, 0);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
                    'userkey' => $userkey,
                    'passkey' => $passkey,
                    'to' => $telepon,
                    'message' => $message
                ));
                $results = json_decode(curl_exec($curlHandle), true);
                curl_close($curlHandle);

                // Setelah berhasil, tampilkan modal pop-up
                $booking_success = true;
            } else {
                echo "Gagal menyimpan booking: " . $stmt_booking->error;
            }
        } else {
            echo "Data kendaraan tidak ditemukan.";
        }

        $stmt_kendaraan->close();
    }

    $stmt_booking->close();
}

$stmt_user->close();
$conn->close();
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - Reservation User</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="../img/logo.jpg" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700&family=Ubuntu:wght@400;500&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/booking-user.css" rel="stylesheet">

    <!-- Link CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<!-- Link CSS jQuery UI (untuk autocomplete) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- Link jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>



</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


   


   <!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="home-user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="../img/logo2.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="home-user.php" class="nav-item nav-link">Home</a>
            <a href="about-user.php" class="nav-item nav-link">About</a>
            <a href="service-user.php" class="nav-item nav-link ">Services</a>
            <a href="contact-user.php" class="nav-item nav-link">Contact</a>
            <a href="reservation-user.php" class="nav-item nav-link active">Reservation</a>
            <!-- Profile Dropdown -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../img/profile.png" alt="User Logo" class="user-logo"> 
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="my-order.php">My Order</a></li>
                    <li><a class="dropdown-item" href="my-reservation-user.php">My Reservation</a></li>
                    <li><a class="dropdown-item" href="my-document.php">Upload Documents</a></li>
                    <li><a class="dropdown-item" href="account-info.php">Account</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- Booking Start -->
<div class="container-fluid booking my-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="form-container h-100 d-flex flex-column justify-content-center text-center p-5 wow zoomIn" data-wow-delay="0.6s">
                    <h1 class="text-white mb-4">Reservation Form</h1>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" id="fullname" class="form-control" value="<?php echo $user_data['nama_user']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" value="<?php echo $user_data['email_user']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Whatsapp Phone Number</label>
                            <input type="text" id="phone" class="form-control" value="<?php echo $user_data['no_telp_user']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="companyName" class="form-label">Company Name(Optional)</label>
                            <input type="text" name="company_name" id="companyName" class="form-control" placeholder="Enter Company Name">
                        </div>
                        <div class="mb-3">
                            <label for="durasi" class="form-label">Kategori Sewa</label>
                            <select name="durasi" id="durasi" class="form-select" required>
                                <option value="" disabled selected>Pilih kategori sewa</option>
                                <option value="harian">Sewa Harian</option>
                                <option value="bulanan">Sewa Bulanan</option>
                                <option value="3_tahun">Sewa 3 Tahun</option>
                                <option value="4_tahun">Sewa 4 Tahun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pilihkendaraan" class="form-label">Pilih Kendaraan</label>
                            <select name="id_kendaraan" id="pilihkendaraan" class="form-select select2" required>
                                <option value="" disabled selected>Pilih kendaraan</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-secondary w-100 py-3">Pesan Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Booking End -->


<!-- Modal Pop-Up -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalLabel">Reservasi Berhasil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Kami akan menghubungi anda dalam 1x24 jam untuk konfirmasi reservasi.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeModalBtn" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pemberitahuan</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar?
            </div>
            <div class="modal-footer">
                <!-- Tombol Logout -->
                <form method="POST">
                    <button type="submit" class="btn btn-danger" formaction="logout.php">Logout</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>


     <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Address</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i> Jl. Raya 
Kebayoran Lama No. 26 Paal VII Jakarta Barat 11540</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>021-5360767</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>akastrarentcar@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="https://wa.me/081282622205" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://mail.google.com/mail/?view=cm&to=akastrarentcar@gmail.com" target="_blank"><i class="fas fa-envelope"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://instagram.com/akastratoyota" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Opening Hours</h4>
                    <h6 class="text-light">Monday - Friday:</h6>
                    <p class="mb-4">09.00 AM - 09.00 PM</p>
                    <h6 class="text-light">Saturday - Sunday:</h6>
                    <p class="mb-0">09.00 AM - 12.00 PM</p>
                </div>
                
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">AkastraRent</a>, All Right Reserved.
                        
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="home-user.php">Home</a>
                            <a href="about-user.php#FAQ-section">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->



    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Link jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Link JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <!-- Template Javascript -->
    <script src="../js/main.js"></script>

    <script>
$('#durasi').change(function() {
    const durasi = $(this).val(); // Ambil nilai durasi

    if (durasi) {
        $.ajax({
            url: 'get_kendaraan.php', // Pastikan path sudah benar
            type: 'GET',
            data: { durasi: durasi },
            success: function(response) {
                // Parsing response JSON
                const kendaraanData = JSON.parse(response);

                // Masukkan data ke dropdown kendaraan
                const $kendaraanSelect = $('#pilihkendaraan');
                $kendaraanSelect.empty(); // Kosongkan dropdown terlebih dahulu

                // Jika ada kendaraan, masukkan ke dropdown
                if (kendaraanData.length > 0) {
                    kendaraanData.forEach(function(item) {
                        $kendaraanSelect.append(new Option(item.text, item.id));
                    });
                } else {
                    $kendaraanSelect.append('<option value="" disabled>Tidak ada kendaraan tersedia</option>');
                }

                // Terapkan Select2
                $kendaraanSelect.select2();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data kendaraan.');
            }
        });
    }
});


</script>

<script>
// Menampilkan modal pop-up jika booking berhasil
<?php if (isset($booking_success) && $booking_success): ?>
  var myModal = new bootstrap.Modal(document.getElementById('bookingModal'), {
    keyboard: false
  });
  myModal.show();
<?php endif; ?>

// Arahkan ke halaman home-user.php setelah modal ditutup
document.getElementById('closeModalBtn').addEventListener('click', function() {
    window.location.href = 'home-user.php';
});
</script>

<script>
    document.getElementById('companyName').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

</script>


</body>

</html>