<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Tentang Kami';
$profil_raw = getAll($conn, 'tentang_kami', 'urutan ASC');

// Organize by category
$sections = [];
$pimpinan = [];

if (!empty($profil_raw)) {
    foreach ($profil_raw as $p) {
        if (!isset($p['status']) || $p['status'] !== 'Publish') continue;
        
        if ($p['kategori'] == 'Struktur') {
            $pimpinan[] = [
                'nama' => $p['judul'],
                'jabatan' => strip_tags($p['konten']),
                'gambar' => $p['gambar'] ?? '',
            ];
        } else {
            $kat = $p['kategori'] ?? 'Umum';
            if (!isset($sections[$kat])) $sections[$kat] = [];
            $sections[$kat][] = $p;
        }
    }
}

// Define icons and colors for each category
$categoryMeta = [
    'Deskripsi Utama' => ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'brand-teal'],
    'Sejarah' => ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber-500'],
    'Visi' => ['icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'blue-500'],
    'Misi' => ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'emerald-500'],
    'Tujuan' => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple-500'],
    'Nilai/Keunggulan' => ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'color' => 'rose-500'],
];

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Hero Banner -->
<div class="bg-brand-navy relative pt-24 pb-24 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full border border-white/20"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <span class="inline-flex items-center px-4 py-1.5 bg-brand-teal/20 text-brand-teal rounded-full text-sm font-bold tracking-wider uppercase border border-brand-teal/30 mb-6">Profil Kampus</span>
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4 font-heading tracking-tight">Tentang SMRHJ</h2>
        <p class="text-gray-400 max-w-2xl mx-auto text-lg">Mengenal lebih dekat Sekolah Tinggi Ilmu Kesehatan Mitra Ria Husada Jakarta</p>
        <div class="flex items-center justify-center text-sm text-gray-400 font-medium mt-6">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Tentang Kami</span>
        </div>
    </div>
</div>

<?php if (empty($sections) && empty($pimpinan)): ?>
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-lg mx-auto py-16 bg-slate-50 rounded-3xl border border-dashed border-gray-200">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-gray-500 text-lg">Informasi tentang kami belum dikonfigurasi oleh administrator.</p>
        </div>
    </div>
</section>
<?php else: ?>

<!-- Content Sections -->
<?php 
$sectionIndex = 0;
foreach ($sections as $kategori => $items): 
    $meta = $categoryMeta[$kategori] ?? ['icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'brand-teal'];
    $isAlt = $sectionIndex % 2 !== 0;
?>
<section class="py-20 <?= $isAlt ? 'bg-slate-50' : 'bg-white' ?>">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-5xl mx-auto">
            <?php foreach ($items as $idx => $p): ?>
            <div class="<?= $idx > 0 ? 'mt-12 pt-12 border-t border-gray-100' : '' ?>">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-<?= $meta['color'] ?>/10 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-<?= $meta['color'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $meta['icon'] ?>"></path></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-<?= $meta['color'] ?> uppercase tracking-widest"><?= htmlspecialchars($kategori) ?></span>
                        <h3 class="text-2xl md:text-3xl font-bold text-brand-navy font-heading leading-tight"><?= htmlspecialchars($p['judul']) ?></h3>
                    </div>
                </div>

                <?php if (!empty($p['gambar'])): ?>
                <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
                    <img src="uploads/tentang/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['judul']) ?>" class="w-full max-h-[400px] object-cover">
                </div>
                <?php endif; ?>

                <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed [&>ul]:list-disc [&>ul]:pl-6 [&>ol]:list-decimal [&>ol]:pl-6 [&>p]:mb-4">
                    <?= nl2br($p['konten']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php 
    $sectionIndex++;
endforeach; 
?>

<!-- Struktur Kepemimpinan -->
<section class="py-24 bg-gradient-to-b from-white to-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-4 py-1.5 bg-brand-navy/10 text-brand-navy rounded-full text-sm font-bold tracking-wider uppercase border border-brand-navy/20">Manajemen Kampus</span>
                <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-5 font-heading">Struktur Kepemimpinan</h3>
                <p class="text-gray-500 mt-3 max-w-xl mx-auto">Para pemimpin yang mendedikasikan diri untuk memajukan pendidikan kesehatan di STIKES MRHJ.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if(empty($pimpinan)): ?>
                    <div class="col-span-full text-center text-gray-500 py-12 bg-white rounded-3xl border border-dashed border-gray-200">Data pimpinan belum ditambahkan oleh administrator.</div>
                <?php else: foreach($pimpinan as $idx => $p): ?>
                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-md hover:shadow-2xl transition-all duration-500 text-center group transform hover:-translate-y-2 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-navy to-brand-teal"></div>
                        <?php if(!empty($p['gambar'])): ?>
                            <div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-6 border-4 border-white shadow-xl group-hover:shadow-2xl group-hover:scale-105 transition-all duration-500 ring-4 ring-brand-teal/20">
                                <img src="uploads/tentang/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" class="w-full h-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-brand-navy/10 to-brand-teal/10 text-brand-navy rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-lg group-hover:scale-105 transition-all duration-500">
                                <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        <?php endif; ?>
                        <h4 class="text-xl font-bold text-gray-800 mb-1 font-heading"><?= htmlspecialchars($p['nama']) ?></h4>
                        <div class="w-10 h-0.5 bg-brand-teal mx-auto my-3"></div>
                        <p class="text-brand-teal font-bold text-sm uppercase tracking-wider"><?= htmlspecialchars($p['jabatan']) ?></p>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
