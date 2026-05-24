<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fungsi proteksi halaman admin
function proteksi_halaman() {
    if (!isset($_SESSION['login_admin'])) {
        header("Location: login.php");
        exit;
    }
}
?>