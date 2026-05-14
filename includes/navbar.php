<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>

    <div class="bg-brand-navy text-white text-xs py-2 hidden sm:block">
        <div class="container mx-auto px-4 md:px-6 flex justify-between items-center">
            <div class="flex gap-4">
                <a href="mailto:info@smrhj.ac.id" class="hover:text-brand-teal transition">✉ info@smrhj.ac.id</a>
                <a href="tel:+62211234567" class="hover:text-brand-teal transition">📞 (021) 1234-5678</a>
            </div>
            <div class="flex gap-4 font-medium">
                <a href="https://stikesmrh.siakadcloud.com/gate/login" target="_blank" class="hover:text-brand-teal transition">SIAKAD</a>
                <a href="https://repo.mrhj.ac.id/" target="_blank" class="hover:text-brand-teal transition">Repository</a>
                <a href="https://e-journal.mrhj.ac.id/Jkk" target="_blank" class="hover:text-brand-teal transition">E-Journal</a>
            </div>
        </div>
    </div>

    <nav class="bg-brand-navy sticky top-0 z-50 shadow-lg border-t border-white/10">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex justify-between items-center h-20">

                <a href="home.php" class="flex flex-col group">
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white group-hover:text-brand-teal transition-colors font-heading">
                        STIKES Mitra Ria Husada Jakarta
                    </h1>
                    <span class="text-xs md:text-sm text-gray-300 uppercase tracking-widest mt-0.5">Sekolah Tinggi Ilmu Kesehatan</span>
                </a>

                <ul class="hidden lg:flex space-x-1 items-center">
                    <li><a href="home.php" class="px-4 py-2 <?= $current_page == 'home.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Beranda</a></li>
                    <li><a href="akademik.php" class="px-4 py-2 <?= $current_page == 'akademik.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Akademik</a></li>
                    <li><a href="berita.php" class="px-4 py-2 <?= $current_page == 'berita.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Berita</a></li>
                    <li><a href="tentang-kami.php" class="px-4 py-2 <?= $current_page == 'tentang-kami.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Tentang Kami</a></li>
                    <li><a href="pmb.php" class="px-4 py-2 <?= $current_page == 'pmb.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Informasi PMB</a></li>
                    <li><a href="fasilitas.php" class="px-4 py-2 <?= $current_page == 'fasilitas.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Fasilitas</a></li>
                    <li><a href="alumni.php" class="px-4 py-2 <?= $current_page == 'alumni.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Alumni</a></li>
                    <li><a href="kontak.php" class="px-4 py-2 <?= $current_page == 'kontak.php' ? 'text-brand-teal' : 'text-gray-200 hover:text-white hover:bg-white/10' ?> font-medium rounded-md transition-colors">Kontak</a></li>
                </ul>

                <button id="mobile-menu-btn" class="lg:hidden text-gray-200 hover:text-white p-2 focus:outline-none" aria-label="Menu navigasi" aria-expanded="false">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-brand-navy border-t border-white/10 px-4 pt-2 pb-4 space-y-1">
            <a href="home.php" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-white/10">Beranda</a>
            <a href="akademik.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Akademik</a>
            <a href="berita.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Berita</a>
            <a href="tentang-kami.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Tentang Kami</a>
            <a href="pmb.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Informasi PMB</a>
            <a href="fasilitas.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Fasilitas</a>
            <a href="alumni.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Alumni</a>
            <a href="kontak.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10">Kontak</a>

            <div class="border-t border-white/10 mt-3 pt-3">
                <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Portal Akademik</p>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-brand-teal hover:bg-white/10">🎓 SIAKAD</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-brand-teal hover:bg-white/10">📚 Repository</a>
                <a href="#" class="block px-3 py-2 rounded-md text-sm font-medium text-brand-teal hover:bg-white/10">📖 E-Journal</a>
            </div>
            <div class="border-t border-white/10 mt-3 pt-3 space-y-1">
                <a href="mailto:info@smrhj.ac.id" class="block px-3 py-1.5 text-xs text-gray-400 hover:text-brand-teal">✉ info@smrhj.ac.id</a>
                <a href="tel:+62211234567" class="block px-3 py-1.5 text-xs text-gray-400 hover:text-brand-teal">📞 (021) 1234-5678</a>
            </div>
        </div>
    </nav>
    <script>
        (function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (!btn || !menu) return;
            
            btn.addEventListener('click', function() {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                } else {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
                
                // Toggle hamburger / X icon
                const svg = btn.querySelector('svg');
                if (svg) {
                    const path = svg.querySelector('path');
                    if (isHidden) {
                        path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
                    } else {
                        path.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                    }
                }
            });
            
            // Close menu when a link is clicked
            menu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                    const path = btn.querySelector('svg path');
                    if (path) path.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                });
            });
        })();
    </script>
