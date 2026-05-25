<?php
require_once 'koneksi.php';
require_once 'config.php';

// Jika admin sudah login, langsung lempar ke dashboard
if (isset($_SESSION['login_admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin_accounts WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Menggunakan pencocokan teks biasa (Plain Text) agar anti-gagal di database saat ini
        if ($password === $row['password']) {
            $_SESSION['login_admin'] = true;
            $_SESSION['id_user']     = $row['ID_User'];
            $_SESSION['nama_admin']  = $row['Nama_Admin'];
            $_SESSION['username']   = $row['Username'];

            header("Location: dashboard.php");
            exit;
        }
    }
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hanbit - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#0b1329] flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-[440px] p-10 rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] border border-gray-100/10">

        <div class="flex flex-col items-center mb-6">
            <div class="w-20 h-20 bg-[#facc15] rounded-2xl flex items-center justify-center overflow-hidden shadow-lg shadow-yellow-500/10 mb-4 border-2 border-slate-900/10 p-2">
                <img src="logo.png" alt="Logo Hanbit" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Admin Hanbit</h1>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Silakan masuk untuk mengelola reservasi.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-xs font-bold px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-semibold text-slate-700">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-semibold text-slate-700">
                </div>
            </div>

            <button type="submit" name="login" class="w-full bg-[#facc15] hover:bg-[#eab308] text-black font-black py-4 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-xl shadow-yellow-400/10 flex items-center justify-center gap-2 mt-2">
                Masuk Panel Admin <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </form>
    </div>

</body>

</html>