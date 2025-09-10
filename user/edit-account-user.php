<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
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
// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna yang sedang login
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user_account WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Cek apakah form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT username_user FROM user_account WHERE username_user = ? AND id_user != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username is already taken. Please choose another one.'); window.history.back();</script>";
        exit;
    }

    // Cek apakah nomor telepon sudah terdaftar
    $stmt = $conn->prepare("SELECT no_telp_user FROM user_account WHERE no_telp_user = ? AND id_user != ?");
    $stmt->bind_param("si", $phone, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Phone number is already registered. Please use a different one.'); window.history.back();</script>";
        exit;
    }

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT email_user FROM user_account WHERE email_user = ? AND id_user != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email is already registered. Please use a different one.'); window.history.back();</script>";
        exit;
    }

    // Validasi nama lengkap hanya boleh huruf dan spasi
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        echo "<script>alert('Please fill out this field with a valid name (only letters and spaces are allowed).'); window.history.back();</script>";
        exit;
    }

    // Jika password diisi, lakukan validasi password
    if (!empty($password)) {
        // Validasi password dan konfirmasi password
        if ($password !== $confirm_password) {
            echo "<script>alert('Password dan konfirmasi password tidak cocok!'); window.history.back();</script>";
            exit;
        }

        if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
            echo "<script>alert('Password must contain letters, numbers, and special characters with a minimum of 8 characters.'); window.history.back();</script>";
            exit;
        }

        // Hash password baru
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Jika password kosong, gunakan password lama
        $password = $user['password_user'];
    }

    // Prepare dan bind SQL query untuk update
    $stmt = $conn->prepare("UPDATE user_account SET nama_user = ?, email_user = ?, no_telp_user = ?, username_user = ?, password_user = ? WHERE id_user = ?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $username, $password, $user_id);

    if ($stmt->execute()) {
    // Perbarui session username agar langsung berubah tanpa perlu logout
    $_SESSION['username'] = $username;
    $edit_success = true;
    
} else {
    echo "Terjadi kesalahan: " . $stmt->error;
}


    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AkastraRentCar - Edit Account</title>
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
    <link href="../css/edit-account-user.css" rel="stylesheet">

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
    <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="../img/logo2.jpg" alt="CarServ Logo" class="logo-navbar">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="home-user.php" class="nav-item nav-link">Home</a>
            <a href="about-user.php" class="nav-item nav-link">About</a>
            <a href="service-user.php" class="nav-item nav-link">Services</a>
            <a href="contact-user.php" class="nav-item nav-link">Contact</a>
            <a href="reservation-user.php" class="nav-item nav-link">Reservation</a>
            <!-- Profile Dropdown -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../img/profile.png" alt="User Logo" class="user-logo"> 
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
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

<!-- Edit Account Form Start -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4" style="color: white;">Edit Account</h3>
                    <form action="edit-account-user.php" method="POST">
                        <div class="mb-4">
                            <label for="username" class="form-label text-light">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username_user'] ?>" required  oninput="this.value = this.value.replace(/\s+/g, '')" 
        pattern="^\S+$">
                            <div id="username-error" class="alert-error">Username is already taken. Please choose another one.</div>
                        </div>
                        <div class="mb-4">
                            <label for="name" class="form-label text-light">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= $user['nama_user'] ?>" required>
                            <div id="name-error" class="alert-error">Please fill out this field with a valid name (only letters and spaces are allowed).</div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label text-light">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email_user'] ?>" required>
                            <div id="email-error" class="alert-error">This email is already registered. Please use a different one.</div>
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="form-label text-light">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= $user['no_telp_user'] ?>" maxlength="12" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            <div id="phone-error" class="alert-error">This phone number is already registered. Please use a different one.</div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label text-light">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password (leave empty to keep current)">
                            <small class="form-text text-light">Password must contain at least 8 characters, including uppercase letters, lowercase letters, numbers, and special characters.</small>
                        </div>
                        <div class="mb-4">
                            <label for="confirm-password" class="form-label text-light">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="Confirm your password">
                            <div id="password-error" class="alert-error">Password and confirm password do not match!</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3" style="background-color: #D81324; border-color: #D81324;">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Account Form End -->

<!-- Modal Pop-Up -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Update Berhasil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeModalBtn" data-bs-dismiss="modal">Tutup</button>
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../js/main.js"></script>

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

        // Validasi hanya dilakukan jika password tidak kosong
        if (password !== "" || confirmPassword !== "") {
            var passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (!passwordRegex.test(password)) {
                passwordError.style.display = "block";
                passwordError.textContent = "Password must contain letters, numbers, and special characters with a minimum of 8 characters.";
                event.preventDefault();
            } else if (password !== confirmPassword) {
                passwordError.style.display = "block";
                passwordError.textContent = "Password and confirm password do not match!";
                event.preventDefault();
            } else {
                passwordError.style.display = "none";
            }
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

<script>
// Menampilkan modal pop-up jika edit berhasil
<?php if (isset($edit_success) && $edit_success): ?>
  var myModal = new bootstrap.Modal(document.getElementById('editModal'), {
    keyboard: false
  });
  myModal.show();
<?php endif; ?>

// Arahkan ke halaman home-user.php setelah modal ditutup
document.getElementById('closeModalBtn').addEventListener('click', function() {
    window.location.href = 'account-info.php';
});
</script>

</body>

</html>
