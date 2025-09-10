
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
    <title>AkastraRentCar - ServiceUser</title>
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
            <a href="service-user.php" class="nav-item nav-link active  ">Services</a>
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
                <h1 class="display-3 text-white mb-3 animated slideInDown">Services</h1>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    

   <!-- Table Start -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Daftar Kendaraan</h2>
    
    <!-- Button dan Search Box -->
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="btn-group">
                <a href="?category=harian" class="btn <?php echo $category == 'harian' ? 'active' : ''; ?>">Sewa Harian</a>
                <a href="?category=bulanan" class="btn <?php echo $category == 'bulanan' ? 'active' : ''; ?>">Sewa Bulanan</a>
                <a href="?category=3tahun" class="btn <?php echo $category == '3tahun' ? 'active' : ''; ?>">Sewa 3 Tahun</a>
                <a href="?category=4tahun" class="btn <?php echo $category == '4tahun' ? 'active' : ''; ?>">Sewa 4 Tahun</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" class="form-control" placeholder="Cari Merk Mobil" id="searchInput" value="<?php echo $searchTerm; ?>">
            </div>
        </div>
    </div>
    
    <!-- Tabel Kendaraan -->
    <div class="table-responsive">
    <table class="table table-bordered" id="vehicleTable">
        <thead style="background-color: var(--primary); color: white;">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Merk Mobil</th>
                <th class="text-center">
                        <?php
                            // Menampilkan label kolom berdasarkan kategori
                            echo ($category == 'harian') ? 'Harga Sewa/Hari' : 'Harga Sewa/Bulan';
                        ?>
                    </th>
                <th class="text-center">Spesifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) : ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td class="text-center"><?php echo $row['merk_mobil']; ?></td>
                        <td class="text-center">Rp.<?php echo $row['harga_sewa']; ?></td>
                        <td class="text-center"><a href="#" class="spec-link" onclick="showSpecifications('<?php echo $row['merk_mobil']; ?>', '<?php echo $row['jenis_mobil']; ?>', '<?php echo $row['transmisi']; ?>', '<?php echo $row['bahan_bakar']; ?>', '<?php echo $row['kapasitas_mesin']; ?>', '<?php echo $row['jumlah_bangku']; ?>', '<?php echo $row['gambar_mobil']; ?>')">Lihat Spesifikasi</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">Tidak ada data kendaraan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    
    <!-- Pagination -->
    <nav aria-label="Pagination" class="mt-4">
        <ul class="pagination justify-content-center" id="pagination">
            <!-- Pagination items akan di-generate oleh JavaScript -->
        </ul>
    </nav>
</div>
<!-- Table End -->

<!-- Modal Spesifikasi -->
<div class="modal fade" id="specModal" tabindex="-1" aria-labelledby="specModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="specModalLabel">Spesifikasi Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Merk:</strong> <span id="specMerk"></span></p>
                <p><strong>Jenis Mobil :</strong> <span id="specJenis"></span></p>
                <p><strong>Transmisi:</strong> <span id="specTransmisi"></span></p>
                <p><strong>Bahan Bakar:</strong> <span id="specBahanBakar"></span></p>
                <p><strong>Kapasitas Mesin:</strong> <span id="specMesin"></span> cc</p>
                <p><strong>Jumlah Bangku:</strong> <span id="specBangku"></span> Bangku</p>
                <div class="text-center">
                     <img id="specImage" class="img-fluid" style="max-width: 100%; height: auto;" src="" alt="Gambar Mobil">
                </div>
                <p class="  text-center ">Gambar diatas adalah contoh</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>




    <div class="container-xxl service py-5">
        <div class="container">
            <div class="row g-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-lg-4">
                    <div class="nav w-100 nav-pills me-4">
                        <button class="nav-link w-100 d-flex align-items-center text-start p-4 mb-4 active" data-bs-toggle="pill" data-bs-target="#tab-pane-1" type="button"><h4 class="m-0">Ketentuan</h4></button>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="tab-content w-100">
                        <!-- Tab Ketentuan -->
                        <div class="tab-pane fade show active" id="tab-pane-1">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h5>Syarat & Ketentuan Penyewaan:</h5>
                                    <ul>
                                        <li>Minimal usia penyewa 21 tahun.</li>
                                        <li>Menyertakan KTP dan SIM A yang berlaku.</li>
                                        <li>Untuk sewa harian minimal penyewaan 3 hari.</li>
                                        <li>Biaya tambahan untuk layanan antar-jemput.</li>
                                        <li>Kendaraan harus dikembalikan dalam kondisi yang sama.</li>
                                        <li>Harga belum termasuk PPN.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
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
   <script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    const table = document.getElementById('vehicleTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr')); // Mengubah NodeList menjadi Array
    const pagination = document.getElementById('pagination');

    function showPage(page, rows) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? '' : 'none';
        });
    }

    function createPagination(totalRows) {
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        pagination.innerHTML = ''; // Menghapus pagination lama

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item';
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;

            li.addEventListener('click', function(e) {
                e.preventDefault();
                showPage(i, rows);

                pagination.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                li.classList.add('active');
            });

            pagination.appendChild(li);
        }

        // Set halaman awal aktif
        if (pagination.querySelector('li')) {
            pagination.querySelector('li').classList.add('active');
        }
    }

    // Tampilkan halaman pertama dan buat pagination untuk data awal
    showPage(1, rows);
    createPagination(rows.length);

    // Fungsi pencarian
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const filteredRows = rows.filter(row => {
            const merk = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            return merk.includes(searchQuery); // Sesuaikan dengan kolom yang relevan
        });

        // Menyembunyikan semua baris terlebih dahulu
        rows.forEach(row => row.style.display = 'none');
        filteredRows.forEach(row => row.style.display = ''); // Menampilkan hanya baris yang sesuai dengan pencarian

        // Membuat pagination baru sesuai hasil pencarian
        createPagination(filteredRows.length);
        showPage(1, filteredRows); // Menampilkan halaman pertama setelah pencarian
    });
});

</script>

<script>
    function openFullscreen(src) {
        const fullscreenDiv = document.createElement('div');
        fullscreenDiv.style.position = 'fixed';
        fullscreenDiv.style.top = '0';
        fullscreenDiv.style.left = '0';
        fullscreenDiv.style.width = '100%';
        fullscreenDiv.style.height = '100%';
        fullscreenDiv.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
        fullscreenDiv.style.display = 'flex';
        fullscreenDiv.style.justifyContent = 'center';
        fullscreenDiv.style.alignItems = 'center';
        fullscreenDiv.style.zIndex = '1000';

        const img = document.createElement('img');
        img.src = src;
        img.style.maxWidth = '90%';
        img.style.maxHeight = '90%';

        fullscreenDiv.appendChild(img);

        fullscreenDiv.addEventListener('click', function() {
            document.body.removeChild(fullscreenDiv);
        });

        document.body.appendChild(fullscreenDiv);
    }
</script>

<script>
    function showSpecifications(merk, jenis, transmisi, bahanBakar, mesin, bangku, image) {
        document.getElementById('specMerk').textContent = merk;
        document.getElementById('specJenis').textContent = jenis;
        document.getElementById('specTransmisi').textContent = transmisi;
        document.getElementById('specBahanBakar').textContent = bahanBakar;
        document.getElementById('specMesin').textContent = mesin;
        document.getElementById('specBangku').textContent = bangku;
        document.getElementById('specImage').src = '../admin/' + image;

        const scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%'; // Mencegah halaman mengecil karena hilangnya scrollbar

        const modal = new bootstrap.Modal(document.getElementById('specModal'));
        modal.show();
    }

    document.getElementById('specModal').addEventListener('hidden.bs.modal', function () {
        // Mengambil posisi scroll yang sebelumnya
        const scrollY = Math.abs(parseInt(document.body.style.top || '0', 10));
        
        // Mengembalikan kembali posisi scroll
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = ''; // Mengembalikan lebar asli halaman
        window.scrollTo(0, scrollY); // Mengembalikan posisi scroll sebelumnya
    });

    // Menangani klik di luar modal untuk mencegah refresh halaman
    document.getElementById('specModal').addEventListener('click', function(event) {
        if (event.target === this) {
            const modal = new bootstrap.Modal(this);
            modal.hide();
        }
    });
</script>
</body>
</html>
