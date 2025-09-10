<?php
session_start();
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

$showSuccessModal = false;
$showErrorModal = false;

// Mengirim ulang OTP jika terdapat parameter 'resend'
if (isset($_GET['resend']) && isset($_SESSION['register_data'])) {
    $otp_code = rand(100000, 999999); // Generate OTP baru
    $_SESSION['otp_code'] = $otp_code; // Simpan OTP baru di session
    $_SESSION['otp_sent_time'] = time(); // Simpan waktu pengiriman OTP

    $userkey = '3cf89ed83802';
    $passkey = 'dd95620e24ad1cebe0354837';
    $my_brand = 'AkastraRentCar';
    $phone = $_SESSION['register_data']['phone'];
    $url = 'https://console.zenziva.net/waofficial/api/sendWAOfficial/';

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
        'userkey' => $userkey,
        'passkey' => $passkey,
        'to' => $phone,
        'brand' => $my_brand,
        'otp' => $otp_code
    ));

    $results = json_decode(curl_exec($curlHandle), true);
    curl_close($curlHandle);

    if ($results['status'] == 1) {
        echo "<script>alert('OTP has been resent. Please check your phone.'); window.location.href='verify_otp.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to resend OTP. Please try again later.'); window.history.back();</script>";
        exit;
    }
}

// Cek apakah OTP yang dimasukkan benar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['otp_code'])) {
    $otp_input = $_POST['otp'];

    if ($otp_input == $_SESSION['otp_code']) {
        // OTP valid, simpan data pengguna ke database
        $user_data = $_SESSION['register_data'];
        $stmt = $conn->prepare("INSERT INTO user_account (nama_user, email_user, no_telp_user, username_user, password_user, created_at) 
                                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("sssss", $user_data['name'], $user_data['email'], $user_data['phone'], $user_data['username'], $user_data['password']);

        if ($stmt->execute()) {
            $showSuccessModal = true;
        } else {
            echo "Terjadi kesalahan: " . $stmt->error;
        }

        // Hapus data session setelah sukses
        unset($_SESSION['register_data']);
        unset($_SESSION['otp_code']);
        unset($_SESSION['otp_sent_time']);
    } else {
        $showErrorModal = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - Registration</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/logo.jpg" rel="icon">

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
    <link href="css/registration.css" rel="stylesheet">

</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


<!-- Verifikasi Form Start -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4" style="color: white;">Verify Your Account</h3>
                    <form action="verify_otp.php" method="POST">
                        <div class="mb-4">
                            <label for="otp" class="form-label text-light">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP sent to your phone" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3" style="background-color: #D81324; border-color: #D81324;">Verify OTP</button>
                        </div>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-light">Didn't receive the OTP? <a href="verify_otp.php?resend=1" class="text-warning">Resend OTP</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Verifikasi Form End -->

<!-- Modal Pop-Up Berhasil -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Registrasi Berhasil</h5>
        <button type="button" class="btn-close" id="closeModalBtnSuccess"></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Pop-Up OTP Tidak Sesuai -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">OTP Tidak sesuai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="js/main.js"></script>
    <script>
    // Tampilkan modal berdasarkan kondisi PHP
    <?php if ($showSuccessModal) { ?>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    <?php } elseif ($showErrorModal) { ?>
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    <?php } ?>

    // Event Listener untuk tombol Tutup di modal berhasil
    document.getElementById('closeModalBtnSuccess').addEventListener('click', function () {
        window.location.href = 'login.php';
    });

    // Jika ingin redirect otomatis setelah beberapa detik tanpa perlu klik tombol
    // setTimeout(function () {
    //     window.location.href = 'login.php';
    // }, 3000); // 3000 ms = 3 detik
</script>
   

</body>

</html>
