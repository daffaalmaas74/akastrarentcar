<?php
session_start();

// Cek jika admin sudah login, jika tidak arahkan ke login.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login-admin.php");
    exit();
}

// Jika admin sudah login, lanjutkan ke halaman ini
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

// Query
$query = "
    SELECT 
        r.id_reservation, 
        u.nama_user, 
        u.no_telp_user, 
        r.merk_mobil, 
        REPLACE(r.durasi, '_', ' ') AS durasi,  -- Mengganti underscore dengan spasi
        IFNULL(r.company_name, '-') AS company_name, 
        CASE 
            WHEN r.status = 'disetujui' THEN 'Disetujui'
            WHEN r.status = 'dibatalkan' THEN 'Dibatalkan'
        END AS status,
        d.ktp_user, d.sim_a_user, d.kartu_keluarga_user, d.cover_rekening_tabungan_user, d.domisili_tempat_tinggal_user, d.surat_keterangan_kerja_user, d.kartu_kredit_user, -- Dokumen personal
        d.ktp_direktur, d.domisili_perusahaan, d.akta_perusahaan, d.siup_perusahaan, d.npwp_perusahaan, d.tdp_perusahaan -- Dokumen perusahaan
    FROM 
        reservasi r
    JOIN 
        user_account u ON r.id_user = u.id_user
    LEFT JOIN 
        dokumen_user d ON r.id_user = d.id_user
    WHERE 
        r.status_sewa IS NULL  -- Menampilkan hanya yang status_sewa null
        AND r.status != 'null'   -- Mengecualikan kolom status yang berisi teks 'null'
        AND r.status != ''       -- Memastikan kolom status tidak kosong
    ORDER BY 
        r.id_reservation DESC
";

$result = $conn->query($query);
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

    <title>Admin AkastraRentCar - List Reservasi</title>
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
                        <li class="nav-item active">
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
            <h4 class="m-0 font-weight-bold text-primary text-center">list-reservasi</h4>
            <br>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
    <tr>
        <th>No</th>
        <th>Nama Lengkap</th>
        <th>Nomor Telepon</th>
        <th>Merk Mobil</th>
        <th>Durasi</th>
        <th>Company Name</th>
        <th>Dokumen Personal</th> <!-- Kolom baru -->
        <th>Dokumen Perusahaan</th> <!-- Kolom baru -->
        <th>Status</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
    <?php
    if ($result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            // Logika untuk Dokumen Personal
            $dokumen_personal_lengkap = 
                !empty($row['ktp_user']) &&
                !empty($row['sim_a_user']) &&
                !empty($row['kartu_keluarga_user']) &&
                !empty($row['cover_rekening_tabungan_user']) &&
                !empty($row['domisili_tempat_tinggal_user']) &&
                !empty($row['surat_keterangan_kerja_user']) &&
                !empty($row['kartu_kredit_user']);
            
            // Logika untuk Dokumen Perusahaan
            $dokumen_perusahaan_lengkap = 
                !empty($row['ktp_direktur']) &&
                !empty($row['domisili_perusahaan']) &&
                !empty($row['akta_perusahaan']) &&
                !empty($row['siup_perusahaan']) &&
                !empty($row['npwp_perusahaan']) &&
                !empty($row['tdp_perusahaan']);
            
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['nama_user'] . "</td>";
            echo "<td>" . $row['no_telp_user'] . "</td>";
            echo "<td>" . $row['merk_mobil'] . "</td>";
            echo "<td>" . $row['durasi'] . "</td>";
            echo "<td class='text-center'>" . ($row['company_name'] !== null && strtolower($row['company_name']) != '-' ? $row['company_name'] : '-') . "</td>";
            
            // Dokumen Personal
            echo "<td class='text-center'>";
            if ($dokumen_personal_lengkap) {
                echo "<i class='fas fa-check text-success'> Lengkap</i>";
            } else {
                echo "<i class='fas fa-times text-danger'> Tidak Lengkap</i>";
            }
            echo "</td>";
            
            // Dokumen Perusahaan
            echo "<td class='text-center'>";
            if (is_null($row['company_name']) || strtolower($row['company_name']) == '-') {
                echo "-"; // Jika company_name null
            } elseif ($dokumen_perusahaan_lengkap) {
                echo "<i class='fas fa-check text-success'> Lengkap</i>";
            } else {
                echo "<i class='fas fa-times text-danger'> Tidak Lengkap</i>";
            }
            echo "</td>";
            
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>
        <div class='d-flex justify-content-center gap-1'>";
        
if ($row['status'] == 'Disetujui') {
    // Menampilkan tombol "Tambahkan ke List Penyewa" hanya jika status adalah "Disetujui"
    echo "<a href='#' class='btn btn-success btn-sm' data-toggle='modal' data-target='#addToTenantModal' data-id='" . $row['id_reservation'] . "'><i class='fas fa-plus'></i> Tambahkan ke List Penyewa</a>";
}

echo "<a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteModal' data-id='" . $row['id_reservation'] . "'><i class='fas fa-trash'></i> Delete</a>
        </div>
      </td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10' class='text-center'>Tidak ada data yang tersedia.</td></tr>";
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
                Apakah Anda yakin ingin menghapus reservasi ini?
            </div>
            <div class="modal-footer">
                <form action="delete-reservation.php" method="POST">
                    <input type="hidden" name="id_reservation" id="deleteId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tambahkan ke List Penyewa -->
<div class="modal fade" id="addToTenantModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambahkan ke List Penyewa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addToTenantForm">
                    <div class="form-group">
                        <label for="total_harga">Total Harga</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" class="form-control" id="total_harga" name="total_harga" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_rental_mulai">Tanggal Rental Mulai</label>
                        <input type="date" class="form-control" id="tanggal_rental_mulai" name="tanggal_rental_mulai" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_rental_berakhir">Tanggal Rental Berakhir</label>
                        <input type="date" class="form-control" id="tanggal_rental_berakhir" name="tanggal_rental_berakhir" required>
                    </div>
                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran</label>
                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*, .pdf" required>
                    </div>
                    <div class="form-group">
                        <label for="bukti_serah_terima">Bukti Serah Terima</label>
                        <input type="file" class="form-control" id="bukti_serah_terima" name="bukti_serah_terima" accept="image/*, .pdf" required>
                    </div>
                    <input type="hidden" id="reservation_id" name="reservation_id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
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
                Apakah Anda yakin ingin menghapus akun user ini?
            </div>
            <div class="modal-footer">
                <form action="delete-user.php" method="POST">
    <input type="hidden" name="id_user" id="deleteId">
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
    // Script untuk mengisi ID pada modal delete
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang diklik untuk membuka modal
        var id_reservation = button.data('id'); // Mengambil ID dari tombol
        var modal = $(this);
        modal.find('#deleteId').val(id_reservation); // Menambahkan ID ke input hidden
    });
</script>

<script>
    $(document).ready(function () {
    $('#addToTenantModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button yang men-trigger modal
        var reservationId = button.data('id'); // Ambil id_reservation dari button

        // Set ID Reservasi ke dalam form
        var modal = $(this);
        modal.find('#reservation_id').val(reservationId);
    });

    $('#addToTenantForm').submit(function (e) {
    e.preventDefault(); // Mencegah form untuk submit secara default

    var formData = new FormData(this); // Ambil data form

    $.ajax({
        url: 'proses_tambahkan_sewa.php', // File PHP untuk memproses data
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            var result = JSON.parse(response);

            if (result.status === "success") {
                alert(result.message);
                $('#addToTenantModal').modal('hide');
                location.reload(); // Reload halaman untuk memperbarui data
            } else {
                alert(result.message); // Tampilkan pesan error
            }
        },
        error: function () {
            alert('Terjadi kesalahan pada server, coba lagi nanti!');
        }
    });
});

});

</script>

<script>
    // Menangani format input angka dengan pemisah ribuan
document.getElementById('total_harga').addEventListener('input', function (e) {
    let input = e.target.value;

    // Menghapus semua karakter selain angka
    input = input.replace(/[^0-9]/g, '');

    // Menambahkan pemisah ribuan (titik)
    if (input) {
        input = input.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Menyimpan hasil ke input field
    e.target.value = input;
});

</script>
</body>

</html>