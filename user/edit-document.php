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

$user_id = $_SESSION['user_id'];

// Mengambil data dokumen yang sudah ada dari database
$query = "SELECT * FROM dokumen_user WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$existing_docs = $result->fetch_assoc();
$stmt->close();

// Proses upload dokumen jika ada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tentukan lokasi penyimpanan file
    $target_dir = "../admin/uploads/dokumen-user/";

    // Pastikan folder uploads/dokumen-user ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Membuat folder jika belum ada
    }

    $uploaded_files = [];
    $dokumen_fields = [
        'ktp_user', 'sim_a_user', 'kartu_keluarga_user', 'cover_rekening_tabungan_user', 
        'domisili_tempat_tinggal_user', 'surat_keterangan_kerja_user', 'kartu_kredit_user',
        'ktp_direktur', 'domisili_perusahaan', 'akta_perusahaan', 'siup_perusahaan', 
        'npwp_perusahaan', 'tdp_perusahaan'
    ];

    // Menyimpan nilai sementara untuk setiap dokumen
    foreach ($dokumen_fields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $file_name = $_FILES[$field]['name'];
            $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

            // Validasi format file (hanya PDF)
            if ($file_type != 'pdf') {
                echo "Hanya file PDF yang diizinkan untuk $field.";
                exit();
            }

            // Membuat nama file sesuai format yang diminta
            $base_name = pathinfo($file_name, PATHINFO_FILENAME);
            $new_file_name = $base_name . '_' . $field . '_' . $user_id . '.' . $file_type;

            $target_file = $target_dir . $new_file_name;

            // Upload file
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $target_file)) {
                $uploaded_files[$field] = $target_file;
            } else {
                echo "Terjadi kesalahan saat mengupload $field.";
                exit();
            }
        } else {
            // Jika tidak ada file baru, tetap gunakan file lama
            if (isset($existing_docs[$field])) {
                $uploaded_files[$field] = $existing_docs[$field];
            }
        }
    }

    // Persiapkan data untuk disimpan di database
    $ktp_user = $uploaded_files['ktp_user'];
    $sim_a_user = $uploaded_files['sim_a_user'];
    $kartu_keluarga_user = $uploaded_files['kartu_keluarga_user'];
    $cover_rekening_tabungan_user = $uploaded_files['cover_rekening_tabungan_user'];
    $domisili_tempat_tinggal_user = $uploaded_files['domisili_tempat_tinggal_user'];
    $surat_keterangan_kerja_user = $uploaded_files['surat_keterangan_kerja_user'];
    $kartu_kredit_user = isset($uploaded_files['kartu_kredit_user']) ? $uploaded_files['kartu_kredit_user'] : $existing_docs['kartu_kredit_user'];
    $ktp_direktur = isset($uploaded_files['ktp_direktur']) ? $uploaded_files['ktp_direktur'] : $existing_docs['ktp_direktur'];
    $domisili_perusahaan = isset($uploaded_files['domisili_perusahaan']) ? $uploaded_files['domisili_perusahaan'] : $existing_docs['domisili_perusahaan'];
    $akta_perusahaan = isset($uploaded_files['akta_perusahaan']) ? $uploaded_files['akta_perusahaan'] : $existing_docs['akta_perusahaan'];
    $siup_perusahaan = isset($uploaded_files['siup_perusahaan']) ? $uploaded_files['siup_perusahaan'] : $existing_docs['siup_perusahaan'];
    $npwp_perusahaan = isset($uploaded_files['npwp_perusahaan']) ? $uploaded_files['npwp_perusahaan'] : $existing_docs['npwp_perusahaan'];
    $tdp_perusahaan = isset($uploaded_files['tdp_perusahaan']) ? $uploaded_files['tdp_perusahaan'] : $existing_docs['tdp_perusahaan'];

    // Menyimpan data ke database
    $query = "UPDATE dokumen_user SET 
        ktp_user = ?, sim_a_user = ?, kartu_keluarga_user = ?, cover_rekening_tabungan_user = ?, 
        domisili_tempat_tinggal_user = ?, surat_keterangan_kerja_user = ?, kartu_kredit_user = ?, 
        ktp_direktur = ?, domisili_perusahaan = ?, akta_perusahaan = ?, siup_perusahaan = ?, 
        npwp_perusahaan = ?, tdp_perusahaan = ? 
        WHERE id_user = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssssssssssi", 
        $ktp_user, 
        $sim_a_user, 
        $kartu_keluarga_user, 
        $cover_rekening_tabungan_user, 
        $domisili_tempat_tinggal_user, 
        $surat_keterangan_kerja_user, 
        $kartu_kredit_user,
        $ktp_direktur,
        $domisili_perusahaan,
        $akta_perusahaan,
        $siup_perusahaan,
        $npwp_perusahaan,
        $tdp_perusahaan,
        $user_id
    );

    if ($stmt->execute()) {
        // Redirect ke halaman my-document.php setelah upload berhasil
        header("Location: my-document.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat menyimpan data ke database.";
    }

    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AkastraRent - My Reservation</title>
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
    <h2 class="text-center mb-4">Form Edit Dokumen</h2>
    <form action="edit-document.php" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Dokumen Personal -->
            <h5 class="mt-3">Dokumen Personal</h5>
            <div class="col-md-6">
                <label for="ktp_user" class="form-label">KTP Penyewa</label>
                <input type="file" class="form-control" id="ktp_user" name="ktp_user" accept="application/pdf">
                <?php if (isset($existing_docs['ktp_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['ktp_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="sim_a_user" class="form-label">SIM A Penyewa</label>
                <input type="file" class="form-control" id="sim_a_user" name="sim_a_user" accept="application/pdf">
                <?php if (isset($existing_docs['sim_a_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['sim_a_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="kartu_keluarga_user" class="form-label">Kartu Keluarga Penyewa</label>
                <input type="file" class="form-control" id="kartu_keluarga_user" name="kartu_keluarga_user" accept="application/pdf">
                <?php if (isset($existing_docs['kartu_keluarga_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['kartu_keluarga_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="cover_rekening_tabungan_user" class="form-label">Cover Rekening Tabungan Penyewa</label>
                <input type="file" class="form-control" id="cover_rekening_tabungan_user" name="cover_rekening_tabungan_user" accept="application/pdf">
                <?php if (isset($existing_docs['cover_rekening_tabungan_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['cover_rekening_tabungan_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="domisili_tempat_tinggal_user" class="form-label">Surat Domisili Tempat Tinggal Penyewa</label>
                <input type="file" class="form-control" id="domisili_tempat_tinggal_user" name="domisili_tempat_tinggal_user" accept="application/pdf">
                <?php if (isset($existing_docs['domisili_tempat_tinggal_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['domisili_tempat_tinggal_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="surat_keterangan_kerja_user" class="form-label">Surat Keterangan Kerja Penyewa</label>
                <input type="file" class="form-control" id="surat_keterangan_kerja_user" name="surat_keterangan_kerja_user" accept="application/pdf">
                <?php if (isset($existing_docs['surat_keterangan_kerja_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['surat_keterangan_kerja_user']); ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="kartu_kredit_user" class="form-label">Kartu Kredit Penyewa</label>
                <input type="file" class="form-control" id="kartu_kredit_user" name="kartu_kredit_user" accept="application/pdf">
                <?php if (isset($existing_docs['kartu_kredit_user'])): ?>
                    <small>File saat ini: <?php echo basename($existing_docs['kartu_kredit_user']); ?></small>
                <?php endif; ?>
            </div>

            <!-- Dokumen Perusahaan -->
            <h3 class="mt-4">Dokumen Perusahaan</h3>
            <label class="col-md-6 mb-3">Lengkapi dokumen dibawah jika penyewaan kendaraan untuk sebuah perusahaan.</label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="domisili_perusahaan" class="form-label">Domisili Perusahaan</label>
                    <input type="file" class="form-control" id="domisili_perusahaan" name="domisili_perusahaan" accept="application/pdf">
                    <?php if (isset($existing_docs['domisili_perusahaan'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['domisili_perusahaan']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="akta_perusahaan" class="form-label">Akta Perusahaan</label>
                    <input type="file" class="form-control" id="akta_perusahaan" name="akta_perusahaan" accept="application/pdf">
                    <?php if (isset($existing_docs['akta_perusahaan'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['akta_perusahaan']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="siup_perusahaan" class="form-label">SIUP Perusahaan</label>
                    <input type="file" class="form-control" id="siup_perusahaan" name="siup_perusahaan" accept="application/pdf">
                    <?php if (isset($existing_docs['siup_perusahaan'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['siup_perusahaan']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="npwp_perusahaan" class="form-label">NPWP Perusahaan</label>
                    <input type="file" class="form-control" id="npwp_perusahaan" name="npwp_perusahaan" accept="application/pdf">
                    <?php if (isset($existing_docs['npwp_perusahaan'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['npwp_perusahaan']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="tdp_perusahaan" class="form-label">TDP Perusahaan</label>
                    <input type="file" class="form-control" id="tdp_perusahaan" name="tdp_perusahaan" accept="application/pdf">
                    <?php if (isset($existing_docs['tdp_perusahaan'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['tdp_perusahaan']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="ktp_direktur" class="form-label">KTP Direktur (Opsional)</label>
                    <input type="file" class="form-control" id="ktp_direktur" name="ktp_direktur" accept="application/pdf">
                    <?php if (isset($existing_docs['ktp_direktur'])): ?>
                        <small>File saat ini: <?php echo basename($existing_docs['ktp_direktur']); ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-4">Update Dokumen</button>
        </div>
    </form>
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