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

// Query untuk menghitung jumlah kendaraan tersedia
$query_kendaraan = "SELECT COUNT(*) AS jumlah_kendaraan FROM (
                        SELECT id_kendaraan FROM list_kendaraan_harian
                        UNION ALL
                        SELECT id_kendaraan FROM list_kendaraan_bulanan
                    ) AS kendaraan";
$result_kendaraan = $conn->query($query_kendaraan);
$row_kendaraan = $result_kendaraan->fetch_assoc();
$jumlah_kendaraan = $row_kendaraan['jumlah_kendaraan'];

// Query untuk menghitung jumlah akun user terdaftar
$query_user = "SELECT COUNT(*) AS jumlah_user FROM user_account";
$result_user = $conn->query($query_user);
$row_user = $result_user->fetch_assoc();
$jumlah_user = $row_user['jumlah_user'];

// Query untuk menghitung jumlah akun admin terdaftar
$query_admin = "SELECT COUNT(*) AS jumlah_admin FROM admin_account";
$result_admin = $conn->query($query_admin);
$row_admin = $result_admin->fetch_assoc();
$jumlah_admin = $row_admin['jumlah_admin'];

$query_reservasi = "SELECT COUNT(*) AS jumlah_pending FROM reservasi WHERE status = 'NULL'";


$result_reservasi = $conn->query($query_reservasi);
$row_reservasi = $result_reservasi->fetch_assoc();
$jumlah_pending = $row_reservasi['jumlah_pending'];


// Query untuk mengambil 5 data terbaru dari tabel user_account
$query_user_terbaru = "SELECT id_user, nama_user, username_user, email_user, no_telp_user 
                       FROM user_account 
                       ORDER BY id_user DESC 
                       LIMIT 5";
$result_user_terbaru = $conn->query($query_user_terbaru);


$query_reservasi_terbaru = "SELECT r.id_reservation, 
                                   u.nama_user, 
                                   r.merk_mobil, 
                                   REPLACE(r.durasi, '_', ' ') AS durasi, 
                                   IFNULL(r.company_name, '-') AS company_name, 
                                   IF(r.status = 'NULL', 'Menunggu', r.status) AS status 
                            FROM reservasi r 
                            JOIN user_account u ON r.id_user = u.id_user 
                            ORDER BY r.id_reservation DESC 
                            LIMIT 5";
$result_reservasi_terbaru = $conn->query($query_reservasi_terbaru);


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

    <title>Admin AkastraRentCar - Dashboard</title>
    <!-- Favicon -->
    <link href="../img/logo.jpg" rel="icon">
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

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
            <li class="nav-item active">
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
            <li class="nav-item">
                <a class="nav-link" href="list-penyewa.php">
                     <i class="fas fa-fw fa-list-alt"></i> <!-- Ganti dengan ikon mobil -->
                    <span>List Penyewa</span></a>
            </li>
            <li class="nav-item">
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

            <li class="nav-item">
                <a class="nav-link" href="dokumen-penyewa.php">
                     <i class="fas fa-fw fa-file-alt"></i> <!-- Ganti dengan ikon mobil -->
                    <span>Dokumen Penyewa</span></a>
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                   <!-- Content Row -->
<div class="row">

    <!-- kendaraan tersedia Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Kendaraan Tersedia
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_kendaraan; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-car fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- akun user Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            akun user terdaftar
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_user; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- akun admin Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            akun admin terdaftar
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_admin; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reservasi Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Reservasi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_pending; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Content Row -->


<!-- akun user terbaru Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800 text-center">Akun User Terbaru</h1>
                    

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        
                        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <a href="list-akun-user.php" class="btn btn-primary btn-sm mb-2 mb-md-0">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Lengkap</th>
                                            <th class="text-center">Username</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Nomor Telepon</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php
    if ($result_user_terbaru->num_rows > 0) {
        $no = 1; // Inisialisasi nomor urut
        while ($row = $result_user_terbaru->fetch_assoc()) {
            echo "<tr>";
            echo "<td class = text-center>" . $no++ . "</td>";
            echo "<td class = text-center>" . htmlspecialchars($row['nama_user']) . "</td>";
            echo "<td class = text-center>" . htmlspecialchars($row['username_user']) . "</td>";
            echo "<td class = text-center>" . htmlspecialchars($row['email_user']) . "</td>";
            echo "<td class = text-center>" . htmlspecialchars($row['no_telp_user']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'>Tidak ada data user terbaru</td></tr>";
    }
    ?>
</tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- reservasi terbaru Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800 text-center">Reservasi Terbaru</h1>
                    

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        
                        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <a href="list-reservasi.php" class="btn btn-primary btn-sm mb-2 mb-md-0">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Lengkap</th>
                                            <th class="text-center">Merk Mobil</th>
                                            <th class="text-center">Durasi</th>
                                            <th class="text-center">Nama perusahaan</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php
    if ($result_reservasi_terbaru->num_rows > 0) {
        $no = 1; // Inisialisasi nomor urut
        while ($row = $result_reservasi_terbaru->fetch_assoc()) {
            echo "<tr>";
            echo "<td class= text-center>" . $no++ . "</td>";
            echo "<td class= text-center>" . htmlspecialchars($row['nama_user']) . "</td>";
            echo "<td class= text-center>" . htmlspecialchars($row['merk_mobil']) . "</td>";
            echo "<td class= text-center>" . htmlspecialchars($row['durasi']) . "</td>";
            echo "<td class= text-center>" . htmlspecialchars($row['company_name']) . "</td>";
            echo "<td class= text-center>" . htmlspecialchars($row['status']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Tidak ada reservasi terbaru</td></tr>";
    }
    ?>
</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
                   

                            


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

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>