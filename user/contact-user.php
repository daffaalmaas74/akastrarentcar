
<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
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

// Menentukan kategori default jika belum ada yang dipilih
$category = isset($_GET['category']) ? $_GET['category'] : 'harian'; // Default 'harian'


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

// Menutup koneksi
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AkastraRent - Contact</title>
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
    <link href="../css/service-user.css" rel="stylesheet">
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
        <img src="../img/logo2.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="home-user.php" class="nav-item nav-link">Home</a>
            <a href="about-user.php" class="nav-item nav-link">About</a>
            <a href="service-user.php" class="nav-item nav-link">Services</a>
            <a href="contact-user.php" class="nav-item nav-link active">Contact</a>
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
                <h1 class="display-3 text-white mb-3 animated slideInDown">Contact</h1>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    

<!-- Contact Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                <iframe class="position-relative rounded w-100 h-100"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.562142202016!2d106.7843619!3d-6.2103535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f6cfd03048a1%3A0x4466de73e8f6a0e5!2sAkastra%20Toyota!5e0!3m2!1sid!2sid!4v1603794290143!5m2!1sid!2sid"
        frameborder="0" style="min-height: 350px; border:0;" allowfullscreen="" aria-hidden="false"
        tabindex="0"></iframe>

            </div>
            <div class="col-md-6">
                    <p class="mb-4" style="font-size: 1.25rem;" >
    Kamu masih memiliki <strong>Pertanyaan</strong>? Ayo tanyakan ke kami melalui Social Media Kami !
</p>


                <!-- Social Media Contact Section -->
<div class="container my-5">
    <!-- WhatsApp -->
    <div class="d-flex align-items-center mb-3">
        <a href="https://wa.me/081282622205" target="_blank" class="d-flex align-items-center text-decoration-none">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fab fa-whatsapp text-white" style="font-size: 25px;"></i>
            </div>
            <div class="ms-3 hover-box w-100 py-2 px-3" style="transition: background-color 0.3s ease, color 0.3s ease;">
                <h5 class="text-primary mb-1">WhatsApp</h5>
                <p class="mb-0 text-dark">081282622205</p>
            </div>
        </a>
    </div>

    <!-- Instagram -->
    <div class="d-flex align-items-center mb-3">
        <a href="https://instagram.com/akastratoyota" target="_blank" class="d-flex align-items-center text-decoration-none">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fab fa-instagram text-white" style="font-size: 25px;"></i>
            </div>
            <div class="ms-3 hover-box w-100 py-2 px-3" style="transition: background-color 0.3s ease, color 0.3s ease;">
                <h5 class="text-primary mb-1">Instagram</h5>
                <p class="mb-0 text-dark">@akastratoyota</p>
            </div>
        </a>
    </div>

    <!-- Email -->
    <div class="d-flex align-items-center">
        <a href="https://mail.google.com/mail/?view=cm&to=akastrarentcar@gmail.com" target="_blank" class="d-flex align-items-center text-decoration-none">
            <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary" style="width: 50px; height: 50px;">
                <i class="fa fa-envelope-open text-white" style="font-size: 25px;"></i>
            </div>
            <div class="ms-3 hover-box w-100 py-2 px-3" style="transition: background-color 0.3s ease, color 0.3s ease;">
                <h5 class="text-primary mb-1">Email</h5>
                <p class="mb-0 text-dark">akastrarentcar@gmail.com</p>
            </div>
        </a>
    </div>
</div>
<!-- Social Media Contact Section End -->

<style>
    /* Efek hover pada teks */
    .d-flex a .hover-box:hover {
        background-color: #d81324;
        color: white !important;
        border-radius: 0px;
    }

    /* Memastikan perubahan warna teks */
    .d-flex a .hover-box:hover h5,
    .d-flex a .hover-box:hover p {
        color: white !important;
    }
</style>




                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

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
