<?php
session_start();

// Cek jika admin sudah login, langsung arahkan ke halaman admin
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
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

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form login
    $email_or_username = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mencari admin berdasarkan email atau username
    $query = "SELECT * FROM admin_account WHERE email_admin = ? OR username_admin = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika admin ditemukan
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $admin['password_admin'])) {
            // Jika password benar, set session untuk admin
            $_SESSION['admin_id'] = $admin['id_admin']; // Simpan id_admin di session
            $_SESSION['admin_username'] = $admin['username_admin']; // Simpan username di session
            // Set session nama_admin setelah login berhasil
            $_SESSION['nama_admin'] = $admin['nama_admin']; // Mengambil nama admin dari database dan menyimpannya ke session

            // Redirect ke halaman admin
            header("Location: admin/dashboard.php");
            exit();
        } else {
            // Jika password salah
            $error_message = "Invalid email/username or password.";
        }
    } else {
        // Jika email atau username tidak ditemukan
        $error_message = "Invalid email/username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - Login Admin</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700&family=Ubuntu:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/login.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->



<!-- HTML form -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4" style="color: white;">Login Admin</h3> 
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="login-admin.php">
                        <div class="mb-4">
                            <label for="email" class="form-label text-light">Email Address / Username</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email or username" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label text-light">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3" style="background-color: #D81324; border-color: #D81324;">Login</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>





    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
