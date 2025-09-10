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

// Ambil data dokumen pengguna dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM dokumen_user WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Cek status_sewa di tabel reservasi
$query_status = "SELECT status_sewa FROM reservasi WHERE id_user = ?";
$stmt_status = $conn->prepare($query_status);
$stmt_status->bind_param("i", $user_id);
$stmt_status->execute();
$result_status = $stmt_status->get_result();

$can_edit = true; // Variabel untuk menentukan apakah tombol edit muncul

while ($row = $result_status->fetch_assoc()) {
    if ($row['status_sewa'] !== 'telah selesai' && $row['status_sewa'] !== null) {
        $can_edit = false; // Jika ada yang belum selesai atau status_sewa bukan null, set menjadi false
        break;
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AkastraRent - My Document</title>
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
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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
                <h1 class="display-3 text-white mb-3 animated slideInDown">My Document</h1>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    



<div class="container mt-5">
    <?php 
    if (empty($data)) { // Jika tidak ada data
        echo '
        <div class="container mt-5 text-center">
            <h2 class="mb-4">Anda belum mengupload dokumen yang dibutuhkan untuk melengkapi syarat penyewaan, silahkan upload terlebih dahulu</h2>
            <a href="upload_dokumen.php" class="btn btn-primary">Upload Dokumen</a>
        </div>';
    } else { // Jika ada data
        echo '
        <div class="container mt-5">
            <h2 class="text-center mb-4">Document Data</h2>
            <div class="row g-2">
                <!-- Dokumen Personal -->
                <h5 class="mt-3">Dokumen Personal</h5>
                <div class="col-md-4">
                    <label for="ktp_user" class="form-label">KTP Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="ktp_user" value="' . ($data['ktp_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['ktp_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['ktp_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="sim_a_user" class="form-label">SIM A Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="sim_a_user" value="' . ($data['sim_a_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['sim_a_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['sim_a_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="kartu_keluarga_user" class="form-label">Kartu Keluarga Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="kartu_keluarga_user" value="' . ($data['kartu_keluarga_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['kartu_keluarga_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['kartu_keluarga_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="cover_rekening_tabungan_user" class="form-label">Cover Rekening Tabungan Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="cover_rekening_tabungan_user" value="' . ($data['cover_rekening_tabungan_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['cover_rekening_tabungan_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['cover_rekening_tabungan_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="domisili_tempat_tinggal_user" class="form-label">Surat Domisili Tempat Tinggal Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="domisili_tempat_tinggal_user" value="' . ($data['domisili_tempat_tinggal_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['domisili_tempat_tinggal_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['domisili_tempat_tinggal_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="surat_keterangan_kerja_user" class="form-label">Surat Keterangan Kerja Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="surat_keterangan_kerja_user" value="' . ($data['surat_keterangan_kerja_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['surat_keterangan_kerja_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['surat_keterangan_kerja_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="kartu_kredit_user" class="form-label">Kartu Kredit Penyewa</label>
                    <input type="text" class="form-control form-control-sm" id="kartu_kredit_user" value="' . ($data['kartu_kredit_user'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['kartu_kredit_user'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['kartu_kredit_user']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
            </div>
            
            <!-- Dokumen Perusahaan -->
            <h5 class="mt-4">Dokumen Perusahaan</h5>
            <div class="row g-2">
                <div class="col-md-4">
                    <label for="domisili_perusahaan" class="form-label">Surat Domisili Perusahaan</label>
                    <input type="text" class="form-control form-control-sm" id="domisili_perusahaan" value="' . ($data['domisili_perusahaan'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['domisili_perusahaan'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['domisili_perusahaan']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="akta_perusahaan" class="form-label">Akta Perusahaan</label>
                    <input type="text" class="form-control form-control-sm" id="akta_perusahaan" value="' . ($data['akta_perusahaan'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['akta_perusahaan'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['akta_perusahaan']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="ktp_direktur" class="form-label">ktp Direktur</label>
                    <input type="text" class="form-control form-control-sm" id="ktp_direktur" value="' . ($data['ktp_direktur'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['ktp_direktur'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['ktp_direktur']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                 <div class="col-md-4">
                    <label for="siup_perusahaan" class="form-label">Surat Izin Usaha Perdagangan (SIUP)</label>
                    <input type="text" class="form-control form-control-sm" id="siup_perusahaan" value="' . ($data['siup_perusahaan'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['siup_perusahaan'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['siup_perusahaan']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="npwp_perusahaan" class="form-label">NPWP Perusahaan</label>
                    <input type="text" class="form-control form-control-sm" id="npwp_perusahaan" value="' . ($data['npwp_perusahaan'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['npwp_perusahaan'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['npwp_perusahaan']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <div class="col-md-4">
                    <label for="tdp_perusahaan" class="form-label">Tanda Daftar Perusahaan</label>
                    <input type="text" class="form-control form-control-sm" id="tdp_perusahaan" value="' . ($data['tdp_perusahaan'] ? 'Sudah diupload' : '-') . '" readonly>
                    ' . ($data['tdp_perusahaan'] ? '<a href="lihat_dokumen.php?file=' . urlencode($data['tdp_perusahaan']) . '" class="btn btn-primary btn-sm mt-2" target="_blank">Lihat Dokumen</a>' : '') . '
                </div>
                <!-- Tambahkan elemen lainnya sesuai kebutuhan -->
            </div>
        </div>';
   
     // Menampilkan tombol "Edit Document" jika semua status_sewa telah selesai
        if ($can_edit) {
            echo '<div class="d-flex justify-content-center mt-4">
                    <a href="edit_dokumen.php" class="btn btn-primary btn-sm">Edit Document</a>
                   </div>';
        } else {
            echo '<p class="mt-5 text-center">Dokumen Tidak dapat diedit selama penyewaan kendaraan masih berlangsung.</p>';
        }

     }
    ?>
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
