<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Beranda';

$slides = [];
$res_slides = $conn->query("SELECT * FROM konten_home WHERE bagian='hero_slider' AND status='Publish' ORDER BY urutan ASC");
if ($res_slides) $slides = $res_slides->fetch_all(MYSQLI_ASSOC);
$berita = getBeritaTerbaru($conn, 3);

$sekilas = [];
$res_sekilas = $conn->query("SELECT * FROM konten_home WHERE bagian='sekilas_tentang' LIMIT 1");
if ($res_sekilas && $res_sekilas->num_rows > 0) $sekilas = $res_sekilas->fetch_assoc();

$prodi_home = [];
$res_prodi = $conn->query("SELECT * FROM program_studi WHERE status='Publish' ORDER BY id ASC LIMIT 4");

$kerjasama = [];
try {
    $res_kerja = $conn->query("SELECT * FROM kerjasama WHERE status='Publish' ORDER BY urutan ASC");
    if ($res_kerja) $kerjasama = $res_kerja->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // Table may not exist yet
}
if ($res_prodi) $prodi_home = $res_prodi->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Hero Carousel Section -->
<section id="hero-carousel" class="relative bg-brand-navy h-[85vh] min-h-[550px] flex items-center justify-center overflow-hidden">

    <?php if(!empty($slides)): ?>
        <!-- Slide Backgrounds -->
        <?php foreach($slides as $i => $slide): ?>
            <div class="hero-slide-bg absolute inset-0 z-0 transition-opacity duration-1000 ease-in-out <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?>" data-slide="<?= $i ?>">
                <?php if(!empty($slide['gambar_background'])): ?>
                    <img src="uploads/home/<?= htmlspecialchars($slide['gambar_background']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($slide['judul']) ?>">
                    <div class="absolute inset-0 bg-brand-navy/60"></div>
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-brand-navy to-[#1a4b77]"></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Slide Content -->
        <?php foreach($slides as $i => $slide): ?>
            <div class="hero-slide-content absolute inset-0 z-10 flex flex-col items-center justify-center px-4 text-center transition-all duration-700 ease-in-out <?= $i === 0 ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 translate-y-8 pointer-events-none' ?>" data-slide="<?= $i ?>">
                <div class="max-w-5xl mx-auto w-full">
                    <h2 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight drop-shadow-[0_4px_4px_rgba(0,0,0,0.5)] font-heading">
                        <?= htmlspecialchars($slide['judul']) ?>
                    </h2>
                    <p class="text-xl md:text-2xl text-gray-100 mb-6 max-w-3xl mx-auto font-light drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                        <?= htmlspecialchars($slide['subjudul']) ?>
                    </p>
                    <?php if(!empty($slide['deskripsi'])): ?>
                        <p class="text-base md:text-lg text-gray-200 mb-10 max-w-2xl mx-auto leading-relaxed drop-shadow-md">
                            <?= htmlspecialchars($slide['deskripsi']) ?>
                        </p>
                    <?php else: ?>
                        <div class="mb-10"></div>
                    <?php endif; ?>
                    <?php if(!empty($slide['link_tombol'])): ?>
                        <a href="<?= htmlspecialchars($slide['link_tombol']) ?>" class="inline-block px-10 py-4 bg-brand-teal hover:bg-teal-500 text-white rounded-full font-bold text-lg transition-all transform hover:-translate-y-1 shadow-[0_10px_20px_rgba(0,191,165,0.3)] hover:shadow-[0_15px_30px_rgba(0,191,165,0.4)]">
                            <?= !empty($slide['teks_tombol']) ? htmlspecialchars($slide['teks_tombol']) : 'Selengkapnya' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(count($slides) > 1): ?>
            <!-- Navigation Controls Wrapper -->
            <div id="hero-controls" class="transition-opacity duration-500 opacity-100">
                <!-- Navigation Arrows -->
                <button id="hero-prev" class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="hero-next" class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/10 hover:bg-white/25 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all border border-white/20 group">
                    <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <!-- Dot Indicators -->
                <!-- <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-3">
                    <?php foreach($slides as $i => $slide): ?>
                        <button class="hero-dot w-3 h-3 rounded-full transition-all duration-300 <?= $i === 0 ? 'bg-brand-teal w-8' : 'bg-white/40 hover:bg-white/70' ?>" data-slide="<?= $i ?>"></button>
                    <?php endforeach; ?>
                </div> -->
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Fallback: No Slides -->
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-gradient-to-br from-brand-navy to-[#1a4b77]"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg font-heading">
                Selamat Datang di <span class="text-brand-teal">SMRHJ</span>
            </h2>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto font-light">
                Sekolah Tinggi Ilmu Kesehatan Mitra Ria Husada Jakarta
            </p>
        </div>
    <?php endif; ?>
</section>

<?php if(!empty($slides) && count($slides) > 1): ?>
<script>
(function() {
    const totalSlides = <?= count($slides) ?>;
    let currentSlide = 0;
    let autoplayTimer = null;

    const bgs = document.querySelectorAll('.hero-slide-bg');
    const contents = document.querySelectorAll('.hero-slide-content');
    const dots = document.querySelectorAll('.hero-dot');

    function goToSlide(index) {
        // Wrap around
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        // Hide current
        bgs[currentSlide].classList.remove('opacity-100');
        bgs[currentSlide].classList.add('opacity-0');
        contents[currentSlide].classList.remove('opacity-100', 'translate-y-0');
        contents[currentSlide].classList.add('opacity-0', 'translate-y-8');
        if (dots.length) {
            dots[currentSlide].classList.remove('bg-brand-teal', 'w-8');
            dots[currentSlide].classList.add('bg-white/40');
        }

        // Show new
        bgs[index].classList.remove('opacity-0');
        bgs[index].classList.add('opacity-100');
        contents[index].classList.remove('opacity-0', 'translate-y-8');
        contents[index].classList.add('opacity-100', 'translate-y-0');
        if (dots.length) {
            dots[index].classList.remove('bg-white/40');
            dots[index].classList.add('bg-brand-teal', 'w-8');
        }

        currentSlide = index;
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(() => goToSlide(currentSlide + 1), 6000);
    }

    function stopAutoplay() {
        if (autoplayTimer) clearInterval(autoplayTimer);
    }

    // Arrow buttons
    document.getElementById('hero-prev')?.addEventListener('click', () => {
        goToSlide(currentSlide - 1);
        startAutoplay();
    });
    document.getElementById('hero-next')?.addEventListener('click', () => {
        goToSlide(currentSlide + 1);
        startAutoplay();
    });

    // Dot buttons
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.dataset.slide));
            startAutoplay();
        });
    });

    // Start autoplay
    startAutoplay();

    // Pause on hover & Handle Idle Timer for Controls
    const carousel = document.getElementById('hero-carousel');
    const heroControls = document.getElementById('hero-controls');
    let idleTimer = null;

    function resetIdleTimer() {
        if (!heroControls) return;
        
        // Show controls
        heroControls.classList.remove('opacity-0', 'pointer-events-none');
        heroControls.classList.add('opacity-100');
        
        // Clear existing timer
        if (idleTimer) clearTimeout(idleTimer);
        
        // Set new timer to hide controls after 3 seconds
        idleTimer = setTimeout(() => {
            heroControls.classList.remove('opacity-100');
            heroControls.classList.add('opacity-0', 'pointer-events-none');
        }, 3000);
    }

    // Attach events for hover pause and idle detection
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    
    carousel.addEventListener('mousemove', resetIdleTimer);
    carousel.addEventListener('touchstart', resetIdleTimer);
    carousel.addEventListener('click', resetIdleTimer);
    
    // Initialize idle timer
    resetIdleTimer();
})();
</script>
<?php endif; ?>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mb-8 font-heading"><?= htmlspecialchars($sekilas['judul'] ?? 'Sekilas Tentang Kami') ?></h3>
            <p class="text-lg text-gray-600 leading-relaxed mb-10">
                <?php if(!empty($sekilas['subjudul'])): ?>
                    <?= nl2br(htmlspecialchars($sekilas['subjudul'])) ?>
                <?php else: ?>
                    Informasi tentang kami belum dikonfigurasi. Silakan atur melalui panel admin.
                <?php endif; ?>
            </p>
            <a href="tentang-kami.php" class="inline-flex items-center justify-center px-6 py-3 border-2 border-brand-teal text-brand-teal font-semibold rounded-lg hover:bg-brand-teal hover:text-white transition-colors">
                Baca Selengkapnya
            </a>
        </div>
    </div>
</section>

<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 text-center">
        <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mb-4 font-heading">Program Studi</h3>
        <p class="text-gray-600 max-w-2xl mx-auto mb-16">Pilih program studi yang sesuai dengan minat Anda untuk berkarir di dunia kesehatan profesional.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?= min(count($prodi_home), 4) ?> gap-8 max-w-6xl mx-auto">
            <?php foreach($prodi_home as $p): ?>
            <a href="akademik.php#<?= $p['slug'] ?>" class="block group bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-brand-teal/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-brand-navy mb-2 group-hover:text-brand-teal transition-colors"><?= htmlspecialchars($p['jenjang'] . ' ' . $p['nama_prodi']) ?></h4>
                <p class="text-gray-500 text-sm">Akreditasi <?= htmlspecialchars($p['akreditasi']) ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="flex flex-col md:flex-row justify-between items-center md:items-end mb-16 gap-6">
            <div class="text-center md:text-left">
                <span class="text-brand-teal font-bold tracking-wider uppercase text-sm">Informasi Terkini</span>
                <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-2 font-heading">Berita & Pengumuman</h3>
            </div>
            <a href="berita.php" class="self-center md:self-auto inline-flex items-center justify-center px-6 py-3 bg-brand-navy text-white font-semibold rounded-lg hover:bg-[#113a60] transition-colors whitespace-nowrap">
                Lihat Semua Berita
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if(empty($berita)): ?>
                <div class="col-span-3 text-center text-gray-500 py-10 border-2 border-dashed border-gray-200 rounded-2xl">Belum ada berita dipublikasi.</div>
            <?php else: foreach($berita as $item): ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2 flex flex-col group border border-gray-100">
                    <div class="h-56 overflow-hidden relative bg-gray-100">
                        <?php if($item['gambar_thumbnail']): ?>
                            <img src="uploads/berita/<?= htmlspecialchars($item['gambar_thumbnail']) ?>" alt="<?= htmlspecialchars($item['judul']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200">No Image</div>
                        <?php endif; ?>
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur text-brand-navy text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                            <?= htmlspecialchars($item['kategori']) ?>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="text-xs text-gray-500 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                        </div>
                        <h4 class="text-xl font-bold text-brand-navy mb-4 line-clamp-2 group-hover:text-brand-teal transition-colors font-heading leading-tight">
                            <?= htmlspecialchars($item['judul']) ?>
                        </h4>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3 leading-relaxed">
                            <?= htmlspecialchars(strip_tags($item['konten'])) ?>
                        </p>
                        <a href="berita-detail.php?slug=<?= urlencode($item['slug'] ?? '') ?>&id=<?= $item['id'] ?>" class="mt-auto text-brand-teal font-bold text-sm hover:underline flex items-center gap-1 group/link">
                            Baca Selengkapnya <span class="group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<?php if(!empty($kerjasama)): ?>
<section class="py-24 bg-gradient-to-b from-white to-slate-50 border-t border-gray-100">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center mb-16">
            <span class="inline-flex items-center px-4 py-1.5 bg-brand-teal/10 text-brand-teal rounded-full text-sm font-bold tracking-wider uppercase border border-brand-teal/20">Mitra & Kolaborasi</span>
            <h3 class="text-3xl md:text-5xl font-bold text-brand-navy mt-5 font-heading">Kerjasama Universitas</h3>
            <p class="text-gray-500 mt-4 max-w-2xl mx-auto text-lg leading-relaxed">STIKES Mitra Ria Husada Jakarta menjalin kerjasama strategis dengan berbagai universitas dan institusi terkemuka.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <?php foreach($kerjasama as $mitra): ?>
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-md hover:shadow-2xl transition-all duration-500 group transform hover:-translate-y-2 flex flex-col">
                <!-- Logo & Header -->
                <div class="flex flex-col items-center text-center mb-6">
                    <?php if(!empty($mitra['logo'])): ?>
                        <div class="w-56 h-56 rounded-2xl bg-slate-50 border border-gray-100 flex items-center justify-center p-3 shrink-0 group-hover:border-brand-teal/30 transition-colors overflow-hidden mb-4">
                            <img 
                                src="uploads/kerjasama/<?= htmlspecialchars($mitra['logo']) ?>" 
                                alt="<?= htmlspecialchars($mitra['nama_institusi']) ?>" 
                                class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                    <?php else: ?>
                        <div class="w-56 h-56 rounded-2xl bg-brand-navy/5 flex items-center justify-center shrink-0 group-hover:bg-brand-teal/10 transition-colors mb-4">
                            <svg class="w-16 h-16 text-brand-navy/30 group-hover:text-brand-teal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="text-xl font-bold text-brand-navy group-hover:text-brand-teal transition-colors font-heading leading-tight">
                            <?= htmlspecialchars($mitra['nama_institusi']) ?>
                        </h4>
                        <?php if(!empty($mitra['jenis_kerjasama'])): ?>
                            <span class="inline-flex items-center px-3 py-1 bg-brand-teal/10 text-brand-teal text-sm font-bold rounded-full mt-2">
                                <?= htmlspecialchars($mitra['jenis_kerjasama']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <?php if(!empty($mitra['deskripsi'])): ?>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6 flex-grow"><?= nl2br(htmlspecialchars(mb_strimwidth($mitra['deskripsi'], 0, 200, '...'))) ?></p>
                <?php else: ?>
                    <div class="flex-grow"></div>
                <?php endif; ?>

                <!-- Website Link -->
                <?php if(!empty($mitra['website'])): ?>
                    <div class="pt-4 border-t border-gray-100 mt-auto">
                        <a href="<?= htmlspecialchars($mitra['website']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-bold text-brand-navy hover:text-brand-teal transition-colors group/link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            Kunjungi Website <span class="group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
