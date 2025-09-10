<?php
session_start();

// Menghapus semua session
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: ../login-admin.php");
exit();
?>
