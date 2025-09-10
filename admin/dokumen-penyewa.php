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

// Ambil data user dan dokumen dari database
$sql = "
    SELECT u.id_user, u.nama_user, u.no_telp_user, d.ktp_user, d.sim_a_user, d.kartu_keluarga_user, 
           d.cover_rekening_tabungan_user, d.domisili_tempat_tinggal_user, d.surat_keterangan_kerja_user, 
           d.kartu_kredit_user, d.ktp_direktur, d.domisili_perusahaan, d.akta_perusahaan, 
           d.siup_perusahaan, d.npwp_perusahaan, d.tdp_perusahaan
    FROM user_account u
    LEFT JOIN dokumen_user d ON u.id_user = d.id_user
";

$result = $conn->query($sql);
?>





<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin AkastraRentCar - Dokumen Penyewa</title>
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

    <style>
        .car-image {
    width: 100px;  /* Ukuran lebar thumbnail */
    height: 60px;  /* Ukuran tinggi thumbnail */
    object-fit: cover;  /* Menjaga rasio aspek dan memotong gambar jika perlu */
}
    </style>

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

            <li class="nav-item active">
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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary text-center">Dokumen Penyewa</h4>
            <br>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama User</th>
                            <th class="text-center">No.Telp</th>
                            <th class="text-center">Data Dokumen Personal</th>
                            <th class="text-center">Data Dokumen Perusahaan</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                // Cek data dokumen personal
                                $personal_docs = [];
                                $personal_columns = [
                                    'ktp_user', 'sim_a_user', 'kartu_keluarga_user', 'cover_rekening_tabungan_user',
                                    'domisili_tempat_tinggal_user', 'surat_keterangan_kerja_user', 'kartu_kredit_user'
                                ];

                                foreach ($personal_columns as $column) {
                                    if (!empty($row[$column])) {
                                        $personal_docs[$column] = $row[$column];
                                    }
                                }

                                // Cek data dokumen perusahaan
                                $company_docs = [];
                                $company_columns = [
                                    'ktp_direktur', 'domisili_perusahaan', 'akta_perusahaan', 'siup_perusahaan',
                                    'npwp_perusahaan', 'tdp_perusahaan'
                                ];

                                foreach ($company_columns as $column) {
                                    if (!empty($row[$column])) {
                                        $company_docs[$column] = $row[$column];
                                    }
                                }

                                echo "<tr>";
                                echo "<td class='text-center'>" . $no++ . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['nama_user']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['no_telp_user']) . "</td>";
                                echo "<td class='text-center'>";
                                echo !empty($personal_docs) ? "<i class='fas fa-check text-success'> Lengkap</i>" : "<i class='fas fa-times text-danger'> Tidak Lengkap</i>";
                                echo "</td>";
                                echo "<td class='text-center'>";
                                echo !empty($company_docs) ? "<i class='fas fa-check text-success'> Lengkap</i>" : "<i class='fas fa-times text-danger'> Tidak Lengkap</i>";
                                echo "</td>";
                                echo "<td class='text-center'>
                                        <div class='d-flex justify-content-center gap-1'>
                                            <a href='#' class='btn btn-success btn-sm' data-toggle='modal' data-target='#dokumenModal' data-id='" . $row['id_user'] . "'> 
                                                <i class='fas fa-eye'></i> Lihat Dokumen 
                                            </a>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php
// Menutup koneksi
$conn->close();
?>

<!-- Modal Lihat Dokumen -->
<div class="modal fade" id="dokumenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">xx</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="dokumenDetail">
                <!-- Dokumen Detail akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <!-- Tombol Unduh Semua -->
                <button type="button" class="btn btn-success" id="downloadAllButton" style="display:none;">Download Semua</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>








            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; AkastraRent 2024</span>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <!-- Tambahkan Script AJAX -->
<script>
$('#dokumenModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Tombol yang memicu modal
    var userId = button.data('id'); // Ambil ID user dari data-id pada tombol

    // Lakukan permintaan AJAX untuk mengambil dokumen user
    $.ajax({
        url: 'lihat-dokumen.php',
        method: 'GET',
        data: { id_user: userId },
        success: function(response) {
            $('#dokumenDetail').html(response);
        },
        error: function() {
            alert('Terjadi kesalahan saat mengambil data dokumen.');
        }
    });

    // Menangani tombol Download Semua
    $('#downloadAllButton').click(function() {
        window.location.href = 'download-dokumen.php?id_user=' + userId;
    });
});
</script>



</body>

</html>