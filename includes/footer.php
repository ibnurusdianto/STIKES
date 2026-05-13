<?php

?>
    <footer class="bg-brand-navy text-gray-300 py-12 mt-auto border-t-[6px] border-brand-teal">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-xl font-bold text-white mb-4 font-heading">STIKES Mitra Ria Husada Jakarta</h3>
                    <p class="text-sm mb-4 leading-relaxed max-w-md">Menjadi institusi pendidikan kesehatan yang unggul, profesional, dan berdaya saing global dalam menghasilkan tenaga kesehatan yang berkualitas demi kemajuan bangsa.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white mb-4 font-heading">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="pmb.php" class="hover:text-brand-teal transition">Pendaftaran Mahasiswa Baru</a></li>
                        <li><a href="akademik.php" class="hover:text-brand-teal transition">Program Studi</a></li>
                        <li><a href="berita.php" class="hover:text-brand-teal transition">Berita & Informasi</a></li>
                        <li><a href="fasilitas.php" class="hover:text-brand-teal transition">Fasilitas Kampus</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white mb-4 font-heading">Hubungi Kami</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 shrink-0 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Jl. Karya Bhakti Cibubur, Jakarta Timur</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>(021) 1234-5678</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>info@smrhj.ac.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 mt-10 pt-6 text-center text-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; <?= date('Y') ?> STIKES Mitra Ria Husada Jakarta. All rights reserved.</p>
                <a href="admin/login.php" class="text-gray-500 hover:text-brand-teal bg-white/5 px-4 py-2 rounded transition">Admin Login</a>
            </div>
        </div>
    </footer>
</body>
</html>
