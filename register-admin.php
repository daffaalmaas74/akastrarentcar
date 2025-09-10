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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username']; // Menambahkan input username
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT username_admin FROM admin_account WHERE username_admin = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username sudah terdaftar
        echo "<script>alert('Username is already taken. Please choose another one.'); window.history.back();</script>";
        exit;
    }


// Cek apakah email sudah terdaftar
$stmt = $conn->prepare("SELECT email_admin FROM admin_account WHERE email_admin = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Email sudah terdaftar
    echo "<script>alert('Email is already registered. Please use a different one.'); window.history.back();</script>";
    exit;
}



    // Validasi nama lengkap hanya boleh huruf dan spasi
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        echo "<script>alert('Please fill out this field with a valid name (only letters and spaces are allowed).'); window.history.back();</script>";
        exit;
    }

    // Validasi konfirmasi password
    if ($password !== $confirm_password) {
        echo "<script>alert('Password dan konfirmasi password tidak cocok!'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare dan bind SQL query
    $stmt = $conn->prepare("INSERT INTO admin_account (nama_admin, email_admin, username_admin, password_admin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $username, $hashed_password);

    // Eksekusi query dan cek apakah berhasil
    if ($stmt->execute()) {
        // Redirect ke halaman login dengan pesan sukses
        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login-admin.php';</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - Register Admin</title>
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


    <!-- Registration Form Start -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4" style="color: white;">Create Account</h3>
                        <form action="register-admin.php" method="POST">
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
                                <label for="password" class="form-label text-light">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
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

    

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="js/main.js"></script>

    <!-- JavaScript Validation -->
    <script>
        // Validasi untuk Nama Lengkap
var nameInput = document.getElementById("name");
var nameError = document.getElementById("name-error");
var registerButton = document.querySelector("button[type='submit']");

nameInput.addEventListener("input", function() {
    var name = nameInput.value;
    var nameRegex = /^[a-zA-Z\s]+$/; // Hanya huruf dan spasi yang diterima

    if (!nameRegex.test(name)) {
        nameError.style.display = "block"; // Tampilkan pesan kesalahan saat input tidak valid
        registerButton.disabled = true; // Menonaktifkan tombol Register
    } else {
        nameError.style.display = "none"; // Sembunyikan pesan kesalahan saat input valid
        // Pastikan tombol Register diaktifkan kembali jika valid
        var isValidUsername = usernameError.style.display === "none";
        var isValidPassword = passwordError.style.display === "none" && document.getElementById("password").value === document.getElementById("confirm-password").value;
        
        // Mengaktifkan tombol Register jika semua input valid
        if (isValidUsername && isValidPassword) {
            registerButton.disabled = false;
        }
    }
});

        // Validasi Password dan Konfirmasi Password hanya saat tombol Register ditekan
        document.querySelector('form').addEventListener('submit', function(event) {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
            var passwordError = document.getElementById("password-error");

            // Cek apakah password dan konfirmasi password cocok
            if (password !== confirmPassword) {
                passwordError.style.display = "block"; // Tampilkan pesan error
                event.preventDefault(); // Mencegah form untuk submit jika password tidak cocok
            } else {
                passwordError.style.display = "none"; // Sembunyikan pesan error jika cocok
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
var registerButton = document.querySelector("button[type='submit']");

usernameInput.addEventListener("input", function() {
    var username = usernameInput.value;

    // Mengirim request untuk memeriksa apakah username sudah ada
    $.post('check_username_admin.php', { username: username }, function(response) {
        if (response == "exists") {
            usernameError.style.display = "block"; // Tampilkan pesan error jika username sudah ada
            registerButton.disabled = true; // Menonaktifkan tombol register
        } else {
            usernameError.style.display = "none"; // Sembunyikan pesan error jika username valid
            registerButton.disabled = false; // Mengaktifkan tombol register jika username belum terdaftar
        }
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



// Validasi untuk Email
var emailInput = document.getElementById("email");
var emailError = document.getElementById("email-error");
var registerButton = document.querySelector("button[type='submit']");

emailInput.addEventListener("input", function() {
    var email = emailInput.value;

    // Mengirim request untuk memeriksa apakah email sudah ada
    $.post('check_email_admin.php', { email: email }, function(response) {
        if (response == "exists") {
            emailError.style.display = "block"; // Tampilkan pesan error jika email sudah ada
            registerButton.disabled = true; // Menonaktifkan tombol register
        } else {
            emailError.style.display = "none"; // Sembunyikan pesan error jika email valid
            registerButton.disabled = false; // Mengaktifkan tombol register jika email belum terdaftar
        }
    });
});



    </script>
</body>

</html>
