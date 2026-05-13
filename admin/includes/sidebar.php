<?php

$currentPage = basename($_SERVER['PHP_SELF']);
?>

    <aside class="bg-brand-navy w-64 fixed top-0 bottom-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-xl flex flex-col" id="sidebar">

        <div class="h-16 flex items-center justify-between px-6 bg-brand-navy-light border-b border-white/10">
            <span class="text-lg font-bold text-white tracking-tight flex items-center">
                Admin Panel
            </span>
            <button id="sidebar-close" class="lg:hidden text-gray-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-3 no-scrollbar space-y-1">
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Main Menu</p>
            <a href="dashboard.php" class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Overview
            </a>

            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Manajemen Halaman</p>

            <a href="kelola-home.php" class="nav-item <?= $currentPage == 'kelola-home.php' ? 'active' : '' ?>">
                Kelola Home
            </a>
            <a href="kelola-akademik.php" class="nav-item <?= $currentPage == 'kelola-akademik.php' ? 'active' : '' ?>">
                Program Studi
            </a>
            <a href="kelola-berita.php" class="nav-item <?= $currentPage == 'kelola-berita.php' ? 'active' : '' ?>">
                Kelola Berita
            </a>
            <a href="kelola-tentang-kami.php" class="nav-item <?= $currentPage == 'kelola-tentang-kami.php' ? 'active' : '' ?>">
                Tentang Kami
            </a>
            <a href="kelola-pmb.php" class="nav-item <?= $currentPage == 'kelola-pmb.php' ? 'active' : '' ?>">
                Kelola PMB
            </a>
            <a href="kelola-fasilitas.php" class="nav-item <?= $currentPage == 'kelola-fasilitas.php' ? 'active' : '' ?>">
                Fasilitas
            </a>
            <a href="kelola-alumni.php" class="nav-item <?= $currentPage == 'kelola-alumni.php' ? 'active' : '' ?>">
                Alumni Testimoni
            </a>
            <a href="kelola-kerjasama.php" class="nav-item <?= $currentPage == 'kelola-kerjasama.php' ? 'active' : '' ?>">
                Kerjasama & Mitra
            </a>
            <a href="kelola-kontak.php" class="nav-item <?= $currentPage == 'kelola-kontak.php' ? 'active' : '' ?>">
                Kontak & Info
            </a>

            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Data Masuk</p>
            <a href="kelola-alumni-form.php" class="nav-item <?= $currentPage == 'kelola-alumni-form.php' ? 'active' : '' ?>">
                Pendataan Alumni
            </a>
            <a href="kelola-pesan.php" class="nav-item <?= $currentPage == 'kelola-pesan.php' ? 'active' : '' ?>">
                Pesan Masuk
            </a>

            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-6 mb-2">Pengaturan</p>
            <a href="kelola-admin.php" class="nav-item <?= $currentPage == 'kelola-admin.php' ? 'active' : '' ?>">
                Admin Terdaftar
            </a>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-brand-navy/60 backdrop-blur-sm z-30 hidden lg:hidden transition-opacity duration-300"></div>
