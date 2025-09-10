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

// Tentukan kategori default 'Harian' jika tidak ada yang dipilih
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'harian';

// Query untuk kategori berdasarkan pilihan
if ($kategori == 'bulanan') {
    $query = "SELECT * FROM list_kendaraan_bulanan";
    $judul = "Sewa Bulanan";
} elseif ($kategori == '3_tahun') {
    $query = "SELECT * FROM list_kendaraan_3_tahun";
    $judul = "Sewa 3 Tahun";
} elseif ($kategori == '4_tahun') {
    $query = "SELECT * FROM list_kendaraan_4_tahun";
    $judul = "Sewa 4 Tahun";
} else {
    // Default kategori adalah 'harian'
    $query = "SELECT * FROM list_kendaraan_harian";
    $judul = "Sewa Harian";
}

// Eksekusi query
$result = $conn->query($query);



// FPDF untuk export PDF
if (isset($_GET['export_pdf']) && $_GET['export_pdf'] == 'true') {
    require('libs/fpdf.php');

    // Menyiapkan halaman PDF dengan orientasi landscape ('L')
    $pdf = new FPDF('L'); // Menambahkan parameter 'L' untuk orientasi landscape
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(200, 10, "List Kendaraan", 0, 1, 'C');
    $pdf->Ln(10);

    // Menambahkan header untuk setiap kategori kendaraan
    $pdf->SetFont('Arial', 'B', 9);

    // Kategori: Kendaraan Harian
    $pdf->Cell(200, 10, "Sewa Harian", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 10, 'No', 1);
    $pdf->Cell(140, 10, 'Merk Mobil', 1);
    $pdf->Cell(30, 10, 'Harga Sewa/Hari', 1);
    $pdf->Cell(30, 10, 'Bahan Bakar', 1);
    $pdf->Cell(25, 10, 'Transmisi', 1);
    $pdf->Cell(40, 10, 'Kapasitas Mesin', 1);
    $pdf->Ln();

    // Query untuk data kendaraan harian
    $sql_harian = "SELECT merk_mobil, harga_sewa, bahan_bakar, transmisi, kapasitas_mesin FROM list_kendaraan_harian";
    $result_harian = $conn->query($sql_harian);

    if ($result_harian->num_rows > 0) {
        $no = 1;
        while ($row = $result_harian->fetch_assoc()) {
            // Mengatur nilai kapasitas mesin dengan tambahan " CC" atau "-" jika 0
            $kapasitasMesin = ($row['kapasitas_mesin'] == 0) ? '-' : $row['kapasitas_mesin'] . ' CC';

            // Menghapus titik dari harga sewa dan mengonversinya ke integer
            $hargaSewa = 'Rp. ' . number_format((int) str_replace('.', '', $row['harga_sewa']), 0, ',', '.');

            // Menambahkan data ke PDF
            $pdf->Cell(10, 10, $no++, 1);
            $pdf->Cell(140, 10, $row['merk_mobil'], 1);
            $pdf->Cell(30, 10, $hargaSewa, 1);
            $pdf->Cell(30, 10, $row['bahan_bakar'], 1);
            $pdf->Cell(25, 10, $row['transmisi'], 1);
            $pdf->Cell(40, 10, $kapasitasMesin, 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(200, 10, "Tidak ada data kendaraan harian.", 1, 1, 'C');
    }

    // Menambahkan spasi sebelum kategori berikutnya
    $pdf->Ln(10);

    // Kategori: Kendaraan Bulanan
    $pdf->Cell(200, 10, "Sewa Bulanan", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 10, 'No', 1);
    $pdf->Cell(140, 10, 'Merk Mobil', 1);
    $pdf->Cell(30, 10, 'Harga Sewa/bulan', 1);
    $pdf->Cell(30, 10, 'Bahan Bakar', 1);
    $pdf->Cell(25, 10, 'Transmisi', 1);
    $pdf->Cell(40, 10, 'Kapasitas Mesin', 1);
    $pdf->Ln();

    // Query untuk data kendaraan bulanan
    $sql_bulanan = "SELECT merk_mobil, harga_sewa, bahan_bakar, transmisi, kapasitas_mesin FROM list_kendaraan_bulanan";
    $result_bulanan = $conn->query($sql_bulanan);

    if ($result_bulanan->num_rows > 0) {
        $no = 1;
        while ($row = $result_bulanan->fetch_assoc()) {
            // Mengatur nilai kapasitas mesin dengan tambahan " CC" atau "-" jika 0
            $kapasitasMesin = ($row['kapasitas_mesin'] == 0) ? '-' : $row['kapasitas_mesin'] . ' CC';

            // Menghapus titik dari harga sewa dan mengonversinya ke integer
            $hargaSewa = 'Rp. ' . number_format((int) str_replace('.', '', $row['harga_sewa']), 0, ',', '.');

            // Menambahkan data ke PDF
            $pdf->Cell(10, 10, $no++, 1);
            $pdf->Cell(140, 10, $row['merk_mobil'], 1);
            $pdf->Cell(30, 10, $hargaSewa, 1);
            $pdf->Cell(30, 10, $row['bahan_bakar'], 1);
            $pdf->Cell(25, 10, $row['transmisi'], 1);
            $pdf->Cell(40, 10, $kapasitasMesin, 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(200, 10, "Tidak ada data kendaraan bulanan.", 1, 1, 'C');
    }

    // Menambahkan spasi sebelum kategori berikutnya
    $pdf->Ln(10);

    // Kategori: Kendaraan 3 Tahun
    $pdf->Cell(200, 10, "Sewa 3 Tahun", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 10, 'No', 1);
    $pdf->Cell(140, 10, 'Merk Mobil', 1);
    $pdf->Cell(30, 10, 'Harga Sewa/bulan', 1);
    $pdf->Cell(30, 10, 'Bahan Bakar', 1);
    $pdf->Cell(25, 10, 'Transmisi', 1);
    $pdf->Cell(40, 10, 'Kapasitas Mesin', 1);
    $pdf->Ln();

    // Query untuk data kendaraan 3 tahun
    $sql_3_tahun = "SELECT merk_mobil, harga_sewa, bahan_bakar, transmisi, kapasitas_mesin FROM list_kendaraan_3_tahun";
    $result_3_tahun = $conn->query($sql_3_tahun);

    if ($result_3_tahun->num_rows > 0) {
        $no = 1;
        while ($row = $result_3_tahun->fetch_assoc()) {
            // Mengatur nilai kapasitas mesin dengan tambahan " CC" atau "-" jika 0
            $kapasitasMesin = ($row['kapasitas_mesin'] == 0) ? '-' : $row['kapasitas_mesin'] . ' CC';

            // Menghapus titik dari harga sewa dan mengonversinya ke integer
            $hargaSewa = 'Rp. ' . number_format((int) str_replace('.', '', $row['harga_sewa']), 0, ',', '.');

            // Menambahkan data ke PDF
            $pdf->Cell(10, 10, $no++, 1);
            $pdf->Cell(140, 10, $row['merk_mobil'], 1);
            $pdf->Cell(30, 10, $hargaSewa, 1);
            $pdf->Cell(30, 10, $row['bahan_bakar'], 1);
            $pdf->Cell(25, 10, $row['transmisi'], 1);
            $pdf->Cell(40, 10, $kapasitasMesin, 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(200, 10, "Tidak ada data kendaraan 3 tahun.", 1, 1, 'C');
    }

    // Menambahkan spasi sebelum kategori berikutnya
    $pdf->Ln(10);

    // Kategori: Kendaraan 4 Tahun
    $pdf->Cell(200, 10, "Sewa 4 Tahun", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(10, 10, 'No', 1);
    $pdf->Cell(140, 10, 'Merk Mobil', 1);
    $pdf->Cell(30, 10, 'Harga Sewa/bulan', 1);
    $pdf->Cell(30, 10, 'Bahan Bakar', 1);
    $pdf->Cell(25, 10, 'Transmisi', 1);
    $pdf->Cell(40, 10, 'Kapasitas Mesin', 1);
    $pdf->Ln();

    // Query untuk data kendaraan 4 tahun
    $sql_4_tahun = "SELECT merk_mobil, harga_sewa, bahan_bakar, transmisi, kapasitas_mesin FROM list_kendaraan_4_tahun";
    $result_4_tahun = $conn->query($sql_4_tahun);

    if ($result_4_tahun->num_rows > 0) {
        $no = 1;
        while ($row = $result_4_tahun->fetch_assoc()) {
            // Mengatur nilai kapasitas mesin dengan tambahan " CC" atau "-" jika 0
            $kapasitasMesin = ($row['kapasitas_mesin'] == 0) ? '-' : $row['kapasitas_mesin'] . ' CC';

            // Menghapus titik dari harga sewa dan mengonversinya ke integer
            $hargaSewa = 'Rp. ' . number_format((int) str_replace('.', '', $row['harga_sewa']), 0, ',', '.');

            // Menambahkan data ke PDF
            $pdf->Cell(10, 10, $no++, 1);
            $pdf->Cell(140, 10, $row['merk_mobil'], 1);
            $pdf->Cell(30, 10, $hargaSewa, 1);
            $pdf->Cell(30, 10, $row['bahan_bakar'], 1);
            $pdf->Cell(25, 10, $row['transmisi'], 1);
            $pdf->Cell(40, 10, $kapasitasMesin, 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(200, 10, "Tidak ada data kendaraan 4 tahun.", 1, 1, 'C');
    }

    // Menampilkan PDF
    $pdf->Output();
    exit();
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

    <title>Admin AkastraRentCar - List Kendaraan</title>
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
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary text-center">List Kendaraan</h4>
            <br>
        </div>
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between flex-wrap">
            <div class="d-flex flex-wrap justify-content-start">
                <a href="input-kendaraan.php" class="btn btn-primary btn-sm mb-2 mr-2">
                    <i class="fas fa-plus"></i> Input Data
                </a>
                <a href="?kategori=4_tahun" class="btn btn-danger btn-sm mb-2 mr-2">4 TAHUN</a>
                <a href="?kategori=3_tahun" class="btn btn-danger btn-sm mb-2 mr-2">3 TAHUN</a>
                <a href="?kategori=bulanan" class="btn btn-danger btn-sm mb-2 mr-2">BULANAN</a>
                <a href="?kategori=harian" class="btn btn-danger btn-sm mb-2 mr-2">HARIAN</a>
<!-- Ubah link export PDF agar dinamis berdasarkan kategori yang aktif -->
<a href="list-kendaraan.php?kategori=<?php echo $kategori; ?>&export_pdf=true" class="btn btn-success btn-sm mb-2">
    <i class="fas fa-file-pdf"></i> Export PDF
</a>


            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <!-- Page Heading -->
                <h5 class="m-0 font-weight-bold text-center"><?= $judul ?></h5>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Merk Mobil</th>
                            <th>Jenis Mobil</th>
                            <th><?php echo ($kategori == 'bulanan' || $kategori == '3_tahun' || $kategori == '4_tahun') ? 'Harga/Bulan' : 'Harga/Hari'; ?></th>
                            <th>Gambar Mobil</th>
                            <th>Bahan Bakar</th>
                            <th>Transmisi</th>
                            <th>Kapasitas Mesin</th>
                            <th>Jumlah Bangku</th>
                            <th>Di Buat Pada</th>
                            <th>Di Update Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['merk_mobil'] . "</td>";
                                echo "<td>" . $row['jenis_mobil'] . "</td>";
                                echo "<td>Rp." . $row['harga_sewa'] . "</td>";
                                echo "<td><a href='#' data-toggle='modal' data-target='#imageModal' onclick=\"setImage('{$row['gambar_mobil']}')\"><img src='" . $row['gambar_mobil'] . "' alt='gambar_mobil' class='car-image'></a></td>";
                                echo "<td>" . $row['bahan_bakar'] . "</td>";
                                echo "<td>" . $row['transmisi'] . "</td>";
                                echo "<td class=text-center>" . ($row['kapasitas_mesin'] == 0 ? '-' : $row['kapasitas_mesin'] . " CC") . "</td>";
                                echo "<td>" . $row['jumlah_bangku'] . " bangku</td>";
                                echo "<td>" . $row['created_at'] . "</td>";
                                echo "<td>" . $row['updated_at'] . "</td>";
                                echo "<td><div class='d-flex justify-content-center gap-1'>
                                    <a href='edit-kendaraan.php?id_kendaraan=" . $row['id_kendaraan'] . "&kategori=" . $kategori . "' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Edit</a>
                                    <a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteModal' 
                                       onclick='setDeleteId(" . $row['id_kendaraan'] . ", \"" . $kategori . "\")'>
                                       <i class='fas fa-trash'></i> Delete</a>
                                </div></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12' class='text-center'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<!-- End of Main Content -->


<!-- Modal for Image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Full Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="fullImage" src="" alt="Full Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

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

<!-- delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus kendaraan ini?
            </div>
            <div class="modal-footer">
                <form action="delete_kendaraan.php" method="POST">
                    <input type="hidden" name="id_kendaraan" id="deleteId">
                    <input type="hidden" name="kategori" id="deleteKategori">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
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
    <script>
        
        function setImage(imageUrl) {
        document.getElementById('fullImage').src = imageUrl;
    }
    </script>
  <script>
function setDeleteId(id, kategori) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteKategori').value = kategori;
}
</script>

</body>

</html>