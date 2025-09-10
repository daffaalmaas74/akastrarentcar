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
    die("Data user tidak ditemukan.");
}

// Query untuk menampilkan rental berjalan
$sql_rental_berjalan = "SELECT * FROM reservasi WHERE status_sewa = 'berjalan' ORDER BY id_reservation ASC";
$result_rental_berjalan = $conn->query($sql_rental_berjalan);

// Query untuk menampilkan riwayat rental
$sql_riwayat_rental = "SELECT * FROM reservasi WHERE status_sewa = 'telah selesai' ORDER BY id_reservation ASC";
$result_riwayat_rental = $conn->query($sql_riwayat_rental);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AkastraRent - My Order</title>
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
    <link href="../css/my-reservation-user.css" rel="stylesheet">
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading...</span></div>
    </div>
    <!-- Spinner End -->
    <!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="home-user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="../img/logo.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="home-user.php" class="nav-item nav-link">Home</a>
            <a href="about-user.php" class="nav-item nav-link">About</a>
            <a href="service-user.php" class="nav-item nav-link">Services</a>
            <a href="contact-user.php" class="nav-item nav-link">Contact</a>
            <a href="reservation-user.php" class="nav-item nav-link">Reservation</a>
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
    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(../img/carousel-bg-2.jpg);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center">
                <h1 class="display-3 text-white mb-3 animated slideInDown">My Order</h1>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    



 <div class="container mt-5">
    <!-- Header Section -->
    <div class="text-center mb-4">
        <h2 class="mt-2">Rental Berjalan</h2>
    </div>
    
    <!-- Pesanan Berjalan -->
    <div class="order-section mb-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Merk Mobil</th>
                            <th class="text-center">Durasi</th>
                            <th class="text-center">Company Name</th>
                            <th class="text-center">Tanggal Mulai Rental</th>
                            <th class="text-center">Tanggal Berakhir Rental</th>
                            <th class="text-center">Sisa Waktu Penyewaan</th>
                            <th class="text-center">Bukti Pembayaran</th>
                            <th class="text-center">Bukti Serah Terima</th>
                            <th class="text-center">Status Sewa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_rental_berjalan->num_rows > 0) {
                            $no = 1;
                            while ($row = $result_rental_berjalan->fetch_assoc()) {
                                $tanggal_mulai = new DateTime($row['tanggal_rental_mulai']);
                                $tanggal_berakhir = new DateTime($row['tanggal_rental_berakhir']);
                                $today = new DateTime();
                                $interval = $tanggal_berakhir->diff($today);

                                if ($tanggal_berakhir < $today) {
                                    // Jika sudah melewati batas
                                    $keterlambatan_hari = $interval->format('%a'); // Mengambil jumlah hari keterlambatan
                                    $sisa_waktu = "Terlambat " . $keterlambatan_hari . " Hari"; // Menampilkan jumlah keterlambatan
                                    $status_sewa = "<span class='badge bg-danger'>Belum Mengembalikan Kendaraan</span>";
                                } else {
                                    // Jika masih dalam waktu sewa
                                    $sisa_waktu = $interval->format('%a') . " Hari";
                                    $status_sewa = "<span class='badge bg-warning fs-7'>Berjalan</span>";
                                }
                        ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center"><?= $row['merk_mobil']; ?></td>
                                    <td class="text-center"><?= $row['durasi']; ?></td>
                                    <td class="text-center"><?= $row['company_name']; ?></td>
                                    <td class="text-center"><?= $row['tanggal_rental_mulai']; ?></td>
                                    <td class="text-center"><?= $row['tanggal_rental_berakhir']; ?></td>
                                    <td class="text-center"><?= $sisa_waktu; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-danger text-white btn-sm" onclick="window.open('../admin/<?= $row['bukti_pembayaran']; ?>')">Click Here</button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger text-white btn-sm" onclick="window.open('../admin/<?= $row['bukti_serah_terima']; ?>')">Click Here</button>
                                    </td>
                                    <td class="text-center"><?= $status_sewa; ?></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Rental -->
    <div class="text-center mb-4">
        <h3 class="mt-2">Riwayat Rental</h3>
    </div>
    <div class="order-section">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Merk Mobil</th>
                            <th class="text-center">Durasi</th>
                            <th class="text-center">Company Name</th>
                            <th class="text-center">Tanggal Mulai Rental</th>
                            <th class="text-center">Tanggal Berakhir Rental</th>
                            <th class="text-center">Bukti Pembayaran</th>
                            <th class="text-center">Bukti Serah Terima</th>
                            <th class="text-center">Status Sewa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_riwayat_rental->num_rows > 0) {
                            $no = 1;
                            while ($row = $result_riwayat_rental->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center"><?= $row['merk_mobil']; ?></td>
                                    <td class="text-center"><?= $row['durasi']; ?></td>
                                    <td class="text-center"><?= $row['company_name']; ?></td>
                                    <td class="text-center"><?= $row['tanggal_rental_mulai']; ?></td>
                                    <td class="text-center"><?= $row['tanggal_rental_berakhir']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-danger text-white btn-sm" onclick="window.open('../admin/<?= $row['bukti_pembayaran']; ?>')">Click Here</button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger text-white btn-sm" onclick="window.open('../admin/<?= $row['bukti_serah_terima']; ?>')">Click Here</button>
                                    </td>
                                    <td class="text-center"><span class="badge bg-success">Telah Selesai</span></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$stmt_user->close();
$conn->close();
?>




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
     <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
  
</body>
</html>
