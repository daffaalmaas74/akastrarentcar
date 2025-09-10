<?php
session_start();

// Cek jika admin sudah login, jika tidak arahkan ke login.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

// Jika admin sudah login, lanjutkan ke halaman ini
// Ambil informasi session admin
$admin_id = $_SESSION['admin_id'];
$admin_nama = $_SESSION['nama_admin'];  // Mengambil nama admin dari session

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

// Menutup koneksi
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin AkastraRentCar - Input Kendaraan</title>
    <link href="../img/logo.jpg" rel="icon">

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-text mx-3">AkastraRent Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            

            <!-- Nav Item - Charts -->
            <li class="nav-item active">
                <a class="nav-link" href="list-kendaraan.php">
                     <i class="fas fa-fw fa-car"></i> <!-- Ganti dengan ikon mobil -->
                    <span>List Kendaraan</span></a>
            </li>
                     <li class="nav-item">
                <a class="nav-link" href="reservasi-pending.php">
                     <i class="fas fa-fw fa-clock"></i> <!-- Ganti dengan ikon mobil -->
                    <span>Reservasi Pending</span></a>
            </li>
                        <li class="nav-item">
                <a class="nav-link" href="list-reservasi.php">
                     <i class="fas fa-fw fa-list"></i> <!-- Ganti dengan ikon mobil -->
                    <span>List reservasi</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="list-akun-user.php">
                     <i class="fas fa-fw fa-user"></i> <!-- Ganti dengan ikon mobil -->
                    <span>List Akun User</span></a>
            </li>
                        <li class="nav-item">
                <a class="nav-link" href="list-akun-admin.php">
                     <i class="fas fa-fw fa-user"></i> <!-- Ganti dengan ikon mobil -->
                    <span>List Akun Admin</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="list-testimonial.php">
                    <i class="fas fa-fw fa-comments"></i>
                    <span>Testimoni</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

<!-- Nav Item - User Information -->
<li class="nav-item dropdown no-arrow">
    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-lg-inline text-gray-600 small"><?php echo $admin_nama; ?></span> <!-- Menampilkan nama admin -->
        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
    </a>
    <!-- Dropdown - User Information -->
    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <a class="dropdown-item" href="info-akun.php">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
        <div class="dropdown-divider"></div>
<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
    Logout
</a>
    </div>
</li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">


    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header py-4">
                    <h6 class="m-0 font-weight-bold text-primary">Form Input Kendaraan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form action="process_upload_list_vehicle.php?kategori=<?php echo isset($_GET['kategori']) ? $_GET['kategori'] : 'harian'; ?>" method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <label>Merk Mobil</label>
                                <input type="text" name="merk_mobil" class="form-control" maxlength="150" required>
                            </div>
                            <div class="form-group">
                                <label>Jenis Mobil</label>
                                <input type="text" name="jenis_mobil" class="form-control" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <label for="picture">Gambar Mobil</label>
                                <div class="custom-file">
                                    <input type="file" name="gambar_mobil" class="custom-file-input" id="picture" required>
                                    <label class="custom-file-label" for="picture">Choose file</label>
                                </div>
                            </div>

                            <!-- Preview gambar -->
                            <div id="preview-container" class="form-group" style="display:none;">
                                <label>Preview Gambar</label>
                                <div id="preview">
                                    <img id="preview-img" src="" alt="Preview" style="max-width: 100%; max-height: 100px; display: inline-block;">
                                    <span id="file-name"></span>
                                    <button type="button" id="close-preview" class="btn btn-danger btn-sm" style="display: inline-block; margin-left: 10px;">&times; Close</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Harga Sewa</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="harga_sewa" class="form-control" id="harga_sewa" required>
                                </div>
                            </div>
                            
                            <!-- Input Bahan Bakar -->
                            <div class="form-group">
                                <label>Jenis Bahan Bakar</label>
                                <select name="bahan_bakar" class="form-control" required>
                                    <option value="bensin">Bensin</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="listrik">Listrik</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                            </div>

                            <!-- Input Transmisi -->
                            <div class="form-group">
                                <label>Transmisi</label>
                                <select name="transmisi" class="form-control" required>
                                    <option value="otomatis">Otomatis</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <!-- Input Kapasitas Mesin -->
                            <div class="form-group">
                                <label>Kapasitas Mesin</label>
                                <div class="input-group">
                                    <input type="text" name="kapasitas_mesin" class="form-control" id="kapasitas_mesin" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">cc</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Jumlah Bangku -->
                            <div class="form-group">
                                <label>Jumlah Bangku</label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_bangku" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Bangku</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Durasi Select Box -->
                            <div class="form-group mt-3">
                                <label>Durasi</label>
                                <select name="durasi" class="form-control" required>
                                    <option value="harian">Harian</option>
                                    <option value="bulanan">Bulanan</option>
                                    <option value="3_tahun">3 Tahun</option>
                                    <option value="4_tahun">4 Tahun</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->


<!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; AkastraRentCar 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->






            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

  <!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pemberitahuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar?
            </div>
            <div class="modal-footer">
                <!-- Tombol Logout -->
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-danger">Logout</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Modal -->
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pemberitahuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data berhasil disimpan!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="window.location.reload();">OK</button>
            </div>
        </div>
    </div>
</div>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script>
    // Ambil elemen input file, preview container, dan elemen lainnya
    const fileInput = document.getElementById('picture');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-img');
    const fileNameDisplay = document.getElementById('file-name');
    const closePreviewButton = document.getElementById('close-preview');
    const fileLabel = document.querySelector('label[for="picture"]');

    // Event listener untuk mengganti nama file di label saat file dipilih
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        
        // Tampilkan nama file di label
        fileLabel.textContent = file ? file.name : "Choose file";

        // Jika ada file yang dipilih, tampilkan preview gambar
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';  // Menampilkan preview
                fileNameDisplay.textContent = file.name; // Menampilkan nama file
            };
            
            reader.readAsDataURL(file);  // Membaca gambar dan menampilkan preview
        } else {
            previewContainer.style.display = 'none';  // Menyembunyikan preview jika tidak ada gambar
        }
    });

    // Event listener untuk menghapus gambar dan mengosongkan pilihan
    closePreviewButton.addEventListener('click', function() {
        fileInput.value = '';  // Mengosongkan input file
        previewContainer.style.display = 'none';  // Menyembunyikan preview
        fileLabel.textContent = "Choose file";  // Mengubah label kembali ke teks default
    });

     // Menambahkan pemisah ribuan dengan titik
    document.getElementById('harga_sewa').addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^\d]/g, '');  // Menghapus karakter selain angka
        if (value.length > 3) {
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Menambahkan titik setiap 3 digit
        }
        e.target.value = value;
    });
</script>

<script>
    // Trigger modal after successful submission
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $stmt->execute()): ?>
        $(document).ready(function() {
            $('#modalSuccess').modal('show');
        });
    <?php endif; ?>
</script>






</body>

</html>