<?php
// Mendapatkan nama file aktif untuk memberikan status class 'active' otomatis
$current_page = basename($_SERVER['PHP_SELF']);

// 🌟 LOGIKA INTEGRASI: Cek apakah halaman aktif adalah menu induk Semua Pesanan ATAU halaman detail update pesanan
$is_pesanan_aktif = ($current_page == 'semua_pesanan.php' || $current_page == 'detail_update_pesanan.php');
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800;900&display=swap');

    #hanbit-sidebar {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Tambahan efek transisi halus untuk sub-menu baru */
    .animate-slide-down {
        animation: slideDown 0.25s ease-out forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<aside id="hanbit-sidebar" class="w-64 bg-[#1e293b] text-white flex flex-col p-5 sticky top-0 h-screen shrink-0 shadow-2xl z-20">
    <a href="dashboard.php" class="flex items-center gap-3 mb-8 px-2 pt-2 hover:opacity-90 transition group select-none">
        <div class="w-9 h-9 flex items-center justify-center overflow-hidden">
            <img src="images/logo warna.png" alt="Logo Hanbit" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
        </div>
        <span class="text-xl font-black tracking-tight text-white">Hanbit <span class="text-[#facc15]">Adm</span></span>
    </a>

    <nav class="space-y-1 flex-1 overflow-y-auto pr-1">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'dashboard.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-th-large w-4 text-center text-sm"></i> Dashboard
        </a>

        <a href="semua_pesanan.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= $is_pesanan_aktif ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-clipboard-list w-4 text-center text-sm"></i> Semua Pesanan
        </a>
        <?php if ($current_page == 'detail_update_pesanan.php' && isset($_GET['invoice'])): ?>
            <div class="pl-7 pt-0.5 pb-2 space-y-1 animate-slide-down">
                <a href="#" class="flex items-start gap-2 text-yellow-400 font-extrabold py-1.5 border-l-2 border-yellow-400 pl-3 bg-white/5 rounded-r-xl transition">
                    <i class="fas fa-file-invoice-dollar text-[11px] mt-0.5"></i>
                    <div>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-200">Update Invoice</span>
                        <span class="block text-[9px] font-mono tracking-tight text-yellow-400 break-all mt-0.5">
                            #<?= htmlspecialchars($_GET['invoice']); ?>
                        </span>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <a href="manajemen_pelanggan.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'manajemen_pelanggan.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-users w-4 text-center text-sm"></i> Manajemen Pelanggan
        </a>

        <a href="riwayat_servis.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'riwayat_servis.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-history w-4 text-center text-sm"></i> Riwayat Servis
        </a>

        <a href="kelola_paket.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'kelola_paket.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-box w-4 text-center text-sm"></i> Kelola Paket Perawatan
        </a>

        <a href="master_laptop.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'master_laptop.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-laptop w-4 text-center text-sm"></i> Master Merek & Seri
        </a>

        <a href="katalog_produk.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'katalog_produk.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-microchip w-4 text-center text-sm"></i> Katalog Part & Aksesori
        </a>

        <a href="portofolio_produk.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'portofolio_produk.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-folder-open w-4 text-center text-sm"></i> Kelola Portofolio Kerja
        </a>

        <a href="pengaturan_sistem.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-bold <?= ($current_page == 'pengaturan_sistem.php') ? 'bg-[#facc15] text-black shadow-lg shadow-yellow-400/10' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-sliders-h w-4 text-center text-sm"></i> Pengaturan Sistem
        </a>
    </nav>

    <div class="mt-auto pt-2 border-t border-slate-700/50">
        <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 px-4 py-2.5 rounded-xl transition-all text-xs font-bold">
            <i class="fas fa-power-off text-center w-4 text-sm"></i> Logout
        </a>
    </div>
</aside>