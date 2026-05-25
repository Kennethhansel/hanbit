<?php
if (!isset($koneksi)) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "db_hanbit";

    $koneksi = mysqli_connect($host, $user, $pass, $db);

    if (!$koneksi) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
}
?>