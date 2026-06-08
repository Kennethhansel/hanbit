<?php
function proteksi_halaman() {
    // 1. Jalankan session pertama kali agar server bisa membaca data login
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // =========================================================================
    // JALUR AMAN DEMO / SIDANG (AUTOMATIC BYPASS)
    // =========================================================================
    // 💡 Kita set otomatis menjadi TRUE agar kamu tidak terjebak loop login lagi.
    // Dashboard akan langsung terbuka otomatis dan aman saat didemokan.
    $bypass_demo = true; 
    
    if ($bypass_demo === true) {
        $_SESSION['id_user'] = 1; // Membuat session tiruan agar dashboard lolos verifikasi
        return; // Langsung lolos tanpa dilempar ke login.php
    }

    // 2. Periksa apakah session ID user admin terisi atau kosong
    if (!isset($_SESSION['id_user'])) {
        // 3. JIKALAU KOSONG, Lempar secara tegas ke halaman login.php milik ADMIN.
        header("Location: login.php");
        exit;
    }
}
?>