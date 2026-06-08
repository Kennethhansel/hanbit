<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Hapus dan hancurkan semua data session login admin
$_SESSION = [];
session_unset();
session_destroy();

// 2. REVISI: Karena file logout.php dan login.php berada di dalam folder yang sama (admin/)
// Kita langsung arahkan ke login.php tanpa perlu mundur pakai '../'
header("Location: login.php");
exit;
?>