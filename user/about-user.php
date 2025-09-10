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


// Menutup koneksi
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - About</title>
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
    <link href="../css/about-user.css" rel="stylesheet">
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
    <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="../img/logo2.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="home-user.php" class="nav-item nav-link">Home</a>
            <a href="about-user.php" class="nav-item nav-link active">About</a>
            <a href="service-user.php" class="nav-item nav-link">Services</a>
            <a href="contact-user.php" class="nav-item nav-link">Contact</a>
            <a href="reservation-user.php"class="nav-item nav-link">Reservation</a>
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
<!-- Navbar End -->


    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(../img/carousel-bg-1.jpg);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center">
                <h1 class="display-3 text-white mb-3 animated slideInDown">About Us</h1>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

<!-- About Us Section Start -->
<div class="container my-5">
    <div class="text-center">
        <img src="../img/mobil-rent.png" alt="Akastra Rent Car" class="img-fluid mb-4">
    </div>
    <div class="text-left">
        <h2 class="text-dark"><strong>PT. <span style="color: red;">AKASTRA</span> RENT CAR</strong></h2>
        <div style="width: 31%; height: 2px; background-color: red; margin: 5px 0;"></div>
        <p class="mt-4 text-justify">
           PT. Alkausar Putra Indonesia (Akastra Rent Car) adalah perusahaan yang bergerak dibidang usaha : penyewaan kendaraan dan bengkel perawatan. Manajemen perusahaan ditopang oleh suatu team yang solid, ahli dibidangnya yang merupakan asset perusahaan sehingga perusahaan semakin berkembang pesat. Akastra Rent Car memiliki komitmen untuk berusaha memberikan yang terbaik dalam pelayanan, harga dan fasilitas kepada semua pelanggan, dan hal tersebutlah yang membuat kami dapat bersaing dengan perusahaan jasa lainnya dan kami juga akan terus menjaga pelayanan terhadap setiap pelanggan tanpa terkecuali.
        </p>
    </div>
</div>
<!-- About Us Section End -->


    <style>

        /* Meningkatkan ukuran font untuk keseluruhan teks */
        h2.text-dark {
            font-size: 2.5rem; /* Membesarkan ukuran font judul */
        }

        p.text-justify {
            font-size: 1.125rem; /* Membesarkan ukuran font untuk paragraf */
        }
        .text-justify {
            text-align: justify;
        }
        .text-danger {
            color: #dc3545;
        }
    </style>




    <!-- About Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <h1 class="mb-4 px-3"><span class="text-primary">Mengapa Akastra Rent?</span> Tempat Terbaik Untuk Penyewaan Mobil</h1>
                
                                <!-- VISI Section -->
                <div class="mb-4 px-3">
                    <h3 class="text-dark">VISI</h3>
                    <p class="lead">Menjadi Mitra bisnis terpercaya, dapat diandalkan dan berkomitmen dalam memberikan pelayanan yang maksimal kepada pelanggan.</p>
                </div>

                <!-- MISI Section -->
                <div class="px-3">
                    <h3 class="text-dark">MISI</h3>
                    <ul class="list-unstyled">
                        <li class="misi-point"><i class="fa fa-check-circle text-primary me-2"></i><span class="lead">Menyediakan kendaraan siap pakai dengan kondisi yang prima sesuai kebutuhan pelanggan.</span></li>
                        <li class="misi-point"><i class="fa fa-check-circle text-primary me-2"></i><span class="lead">Memberikan solusi terbaik sesuai keinginan pelanggan.</span></li>
                        <li class="misi-point"><i class="fa fa-check-circle text-primary me-2"></i><span class="lead">Meminimalkan keluhan dari pelanggan.</span></li>
                        <li class="misi-point"><i class="fa fa-check-circle text-primary me-2"></i><span class="lead">Santun dalam memberikan layanan kepada para pelanggan.</span></li>
                    </ul>
                </div>

                    
            </div>
            
            <!-- Image Section -->
            <div class="col-lg-6 pt-4" style="min-height: 400px;">
                <div class="position-relative h-100 wow fadeIn" data-wow-delay="0.1s">
                    <img class="position-absolute img-fluid w-100 h-100" src="../img/about.jpg" style="object-fit: cover;" alt="">
                    <div class="position-absolute top-0 end-0 mt-n4 me-n4 py-4 px-5" style="background: rgba(0, 0, 0, .08);">
                        <h1 class="display-4 text-white mb-0">15 <span class="fs-4">Tahun</span></h1>
                        <h4 class="text-white">Pengalaman</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<style>
    /* Meningkatkan ukuran font untuk bagian VISI dan MISI */
    .lead {
        font-size: 1.125rem; /* Menambah sedikit ukuran font */
    }

    h3.text-dark {
        font-size: 1.5rem; /* Meningkatkan ukuran font untuk judul bagian */
    }

    /* Memberikan jarak antar poin di bagian MISI */
    .misi-point {
        margin-bottom: 15px; /* Menambahkan jarak antar poin */
    }

    /* Memastikan gambar responsif dengan menyembunyikan bagian yang melebihi ukuran */
    .position-relative img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767px) {
        /* Atur gambar untuk memenuhi layar penuh di perangkat mobile */
        .position-relative img {
            height: 50%; /* Sesuaikan tinggi gambar pada layar kecil */
        }
        /* Sesuaikan padding untuk tampilan di perangkat mobile */
        .py-4, .px-5 {
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
    }
</style>




<br>
<br>

   <!-- FAQ and Image Section Start -->
<div class="container my-5">
    <div class="row align-items-center">
        <!-- FAQ Section -->
        <div class="col-lg-6">
            <div class="text-center">
                <h2 class="text-dark" style="position: relative; margin-bottom: 10px;">
                    <strong>FAQ</strong>
                    <div style="display: block; width: 50px; height: 3px; background-color: red; margin: 5px auto;"></div>

                </h2>
            </div>
            <div class="accordion" id="accordionPanelsStayOpenExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                            Apakah saya perlu membuat akun untuk menggunakan aplikasi ini?
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">
                            Ya, Anda wajib membuat akun terlebih dahulu untuk dapat mengisi form booking. Pembuatan akun ini bertujuan untuk memudahkan pelacakan pemesanan dan memastikan pengalaman yang lebih personal serta aman.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                            Bagaimana cara membuat akun?
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                            Anda dapat membuat akun dengan mengisi informasi dasar seperti username, nama lengkap, email, dan password, dan nomor telepon.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                            Bagaimana cara menghubungi perusahaan jika ada pertanyaan lebih lanjut?
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                        <div class="accordion-body">
                            Informasi kontak, seperti nomor telepon, alamat email, biasanya tersedia di bagian akhir company profile atau di situs web resmi perusahaan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Section -->
        <div class="col-lg-6 text-center">
            <img src="../img/faq-1.png" alt="FAQ Illustration" class="img-fluid" style="border-radius: 10px;">
        </div>
    </div>
</div>
<!-- FAQ and Image Section End -->

        <style>
            .accordion {
                margin-top: 20px;
            }

            .accordion-item {
                border: 1px solid #ddd; /* Add border for a clean look */
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .accordion-button {
                font-size: 16px;
            }

            .img-fluid{
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Add shadow for a polished look */
            }

            /* Adjusting FAQ margin */
            .col-lg-6:first-child {
                padding-left: 0;
            }
    
        </style>
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