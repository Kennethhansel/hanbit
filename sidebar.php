<?php
// Mendapatkan nama file aktif untuk memberikan status class 'active' otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-[#1e293b] text-white flex flex-col p-6 sticky top-0 h-screen shrink-0 shadow-2xl z-20">
    <div class="flex items-center gap-3 mb-10 px-2">
        <div class="w-10 h-10 flex items-center justify-center overflow-hidden">
            <img src="logo warna.png" alt="Logo Hanbit" class="w-full h-full object-contain">
        </div>
        <span class="text-xl font-black uppercase tracking-tighter italic text-[#facc15]">Hanbit Adm</span>
    </div>
    
    <nav class="space-y-1 flex-1">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'dashboard.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-th-large w-5 text-center"></i> Dashboard
        </a>
        <a href="semua_pesanan.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'semua_pesanan.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-clipboard-list w-5 text-center"></i> Semua Pesanan
        </a>
        <a href="manajemen_pelanggan.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'manajemen_pelanggan.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-users w-5 text-center"></i> Manajemen Pelanggan
        </a>
        <a href="riwayat_servis.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'riwayat_servis.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-history w-5 text-center"></i> Riwayat Servis
        </a>
        <a href="katalog_data.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'katalog_data.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-folder-open w-5 text-center"></i> Katalog Data
        </a>
        <a href="pengaturan_sistem.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-200 text-sm font-bold <?= ($current_page == 'pengaturan_sistem.php') ? 'bg-[#facc15] text-black shadow-xl shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-sliders-h w-5 text-center"></i> Pengaturan Sistem
        </a>
    </nav>

    <div class="mt-auto pt-4 border-t border-slate-700/50">
        <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 px-4 py-3 rounded-2xl transition-all font-bold text-sm">
            <i class="fas fa-power-off text-center w-5"></i> Logout
        </a>
    </div>
</aside>