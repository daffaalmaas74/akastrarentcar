<?php
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

// Cek apakah form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['otp'])) {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT username_user FROM user_account WHERE username_user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username is already taken. Please choose another one.'); window.history.back();</script>";
        exit;
    }

    // Validasi lainnya (seperti di atas) ...

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate OTP
    $otp_code = rand(100000, 999999);  // Generate OTP code

    // Kirim OTP ke nomor telepon via Zenziva
    $userkey = '3cf89ed83802';
    $passkey = 'dd95620e24ad1cebe0354837';
    $my_brand = 'AkastraRentCar';  // Nama brand
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
        // OTP successfully sent, save OTP to session and redirect to verify_otp.php
        session_start();
        $_SESSION['otp_code'] = $otp_code;  // Save OTP to session
        $_SESSION['register_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'username' => $username,
            'password' => $hashed_password
        ];
        header("Location: verify_otp.php");  // Redirect to OTP verification page
        exit;
    } else {
        echo "<script>alert('Failed to send OTP. Please try again later.'); window.history.back();</script>";
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

    <style>
        .alert-error {
            color: white;
            background-color: #D81324;
            padding: 10px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="img/logo2.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.php" class="nav-item nav-link">Home</a>
            <a href="about.html" class="nav-item nav-link">About</a>
            <a href="service.php" class="nav-item nav-link">Services</a>
            <a href="contact.html" class="nav-item nav-link">Contact</a>
            <a href="login.php" class="nav-item nav-link">Login</a>
            
            <!-- Book Now button for both small and large screens -->
            <a href="login.php" class="btn btn-primary py-4 px-lg-5 d-lg-none">Reservation<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </div>
    <!-- Book Now button for large screens (still visible) -->
    <a href="login.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Reservation<i class="fa fa-arrow-right ms-3"></i></a>
</nav>
<!-- Navbar End -->

    <!-- Registration Form Start -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4" style="color: white;">Create Account</h3>
                        <form action="register.php" method="POST">
                            <div class="mb-4">
                                <label for="username" class="form-label text-light">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                                <div id="username-error" class="alert-error">Username is already taken. Please choose another one.</div>
                            </div>
                            <div class="mb-4">
                                <label for="name" class="form-label text-light">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                                <div id="name-error" class="alert-error">Please fill out this field with a valid name (only letters and spaces are allowed).</div>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label text-light">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                <div id="email-error" class="alert-error">This email is already registered. Please use a different one.</div>

                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label text-light">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" maxlength="12" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                     <!-- Add an alert message for phone number -->
                                <div id="phone-error" class="alert-error">This phone number is already registered. Please use a different one.</div>

                                </div>
                            <div class="mb-4">
                                <label for="password" class="form-label text-light">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <small class="form-text text-light">Password must contain at least 8 characters, including uppercase letters, lowercase letters, numbers, and special characters.</small>
                            </div>
                            <div class="mb-4">
                                <label for="confirm-password" class="form-label text-light">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
                                <div id="password-error" class="alert-error">Password and confirm password do not match!</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-3" style="background-color: #D81324; border-color: #D81324;">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Registration Form End -->

    

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
                            <a href="about.html#FAQ-section">FAQ</a>
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

    <!-- JavaScript Validation -->
    <script>
    // Validasi untuk Nama Lengkap (hanya huruf dan spasi)
    var nameInput = document.getElementById("name");
    var nameError = document.getElementById("name-error");
    var registerButton = document.querySelector("button[type='submit']");

    nameInput.addEventListener("input", function() {
        var name = nameInput.value;

        // Regex untuk hanya menerima huruf dan spasi
        var nameRegex = /^[a-zA-Z\s]*$/;

        // Jika ada karakter yang tidak valid, hapus karakter tersebut
        if (!nameRegex.test(name)) {
            nameInput.value = name.replace(/[^a-zA-Z\s]/g, ''); // Menghapus karakter yang tidak valid
        }

        
        

        // Mengaktifkan tombol Register jika semua input valid
        validateRegisterButton();
    });

    // Fungsi untuk memvalidasi tombol Register berdasarkan validitas semua input
    function validateRegisterButton() {
        var nameValid = nameError.style.display === "none";
        var usernameValid = usernameError.style.display === "none";
        var phoneValid = phoneError.style.display === "none";
        var passwordValid = passwordError.style.display === "none" && document.getElementById("password").value === document.getElementById("confirm-password").value;

        // Jika semua input valid, tombol Register diaktifkan
        if (nameValid && usernameValid && phoneValid && passwordValid) {
            registerButton.disabled = false;
        } else {
            registerButton.disabled = true;
        }
    }

    // Validasi password dan konfirmasi password hanya saat tombol Register ditekan
    document.querySelector('form').addEventListener('submit', function(event) {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm-password").value;
        var passwordError = document.getElementById("password-error");

        // Validasi password: harus berisi karakter, huruf, angka, dan minimal 8 karakter
        var passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!passwordRegex.test(password)) {
            passwordError.style.display = "block"; // Tampilkan pesan error
            passwordError.textContent = "Password must contain letters, numbers, and special characters with a minimum of 8 characters."; // Pesan error
            event.preventDefault(); // Mencegah form untuk submit jika password tidak cocok
        } else if (password !== confirmPassword) {
            passwordError.style.display = "block"; // Tampilkan pesan error jika password dan konfirmasi tidak cocok
            passwordError.textContent = "Password and confirm password do not match!";
            event.preventDefault(); // Mencegah form untuk submit jika password tidak cocok
        } else {
            passwordError.style.display = "none"; // Sembunyikan pesan error jika password valid
        }
    });

    // Menyembunyikan pesan kesalahan password saat mulai mengetik pada password atau konfirmasi password
    document.getElementById("password").addEventListener("input", function() {
        var passwordError = document.getElementById("password-error");
        passwordError.style.display = "none"; // Sembunyikan pesan error saat mengetik
    });

    document.getElementById("confirm-password").addEventListener("input", function() {
        var passwordError = document.getElementById("password-error");
        passwordError.style.display = "none"; // Sembunyikan pesan error saat mengetik
    });

    // Validasi untuk Username
    var usernameInput = document.getElementById("username");
    var usernameError = document.getElementById("username-error");

    usernameInput.addEventListener("input", function() {
        var username = usernameInput.value;

        // Mengirim request untuk memeriksa apakah username sudah ada
        $.post('check_username.php', { username: username }, function(response) {
            if (response == "exists") {
                usernameError.style.display = "block"; // Tampilkan pesan error jika username sudah ada
            } else {
                usernameError.style.display = "none"; // Sembunyikan pesan error jika username valid
            }
            // Update status tombol Register
            validateRegisterButton();
        });
    });

    // Menambahkan event listener untuk mencegah spasi di input password dan konfirmasi password
    document.getElementById("password").addEventListener("input", function() {
        var password = this.value;
        this.value = password.replace(/\s/g, ''); // Menghapus spasi dari input
    });

    document.getElementById("confirm-password").addEventListener("input", function() {
        var confirmPassword = this.value;
        this.value = confirmPassword.replace(/\s/g, ''); // Menghapus spasi dari input
    });

    // Tambahkan validasi untuk nomor telepon di JavaScript
    var phoneInput = document.getElementById("phone");
    var phoneError = document.getElementById("phone-error");

    phoneInput.addEventListener("input", function() {
        var phone = phoneInput.value;

        // Mengirim request untuk memeriksa apakah nomor telepon sudah ada
        $.post('check_phone.php', { phone: phone }, function(response) {
            if (response == "exists") {
                phoneError.style.display = "block"; // Tampilkan pesan error jika nomor telepon sudah ada
            } else {
                phoneError.style.display = "none"; // Sembunyikan pesan error jika nomor telepon valid
            }
            // Update status tombol Register
            validateRegisterButton();
        });
    });

    // Validasi untuk Email
    var emailInput = document.getElementById("email");
    var emailError = document.getElementById("email-error");

    emailInput.addEventListener("input", function() {
        var email = emailInput.value;

        // Mengirim request untuk memeriksa apakah email sudah ada
        $.post('check_email.php', { email: email }, function(response) {
            if (response == "exists") {
                emailError.style.display = "block"; // Tampilkan pesan error jika email sudah ada
            } else {
                emailError.style.display = "none"; // Sembunyikan pesan error jika email valid
            }
            // Update status tombol Register
            validateRegisterButton();
        });
    });
</script>

</body>

</html>
