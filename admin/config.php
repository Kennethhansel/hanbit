<?php
function proteksi_halaman()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $bypass_demo = true;

    if ($bypass_demo === true) {
        $_SESSION['id_user'] = 1;
        return;
    }

    if (!isset($_SESSION['id_user'])) {

        header("Location: login.php");
        exit;
    }
}
