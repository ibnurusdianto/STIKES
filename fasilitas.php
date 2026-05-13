<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Fasilitas Kampus';
$fasilitasList = getAll($conn, 'fasilitas');

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Fasilitas Kampus</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Fasilitas</span>
        </div>
    </div>
</div>

<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto text-center mb-16">
            <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mb-4 font-heading">Lingkungan Belajar Ideal</h3>
            <p class="text-gray-600 text-lg">STIKES MRHJ menyediakan fasilitas modern dan lengkap untuk mendukung proses kegiatan belajar mengajar secara optimal.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php if(empty($fasilitasList)): ?>
                <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-gray-100 text-gray-500">Data katalog fasilitas belum ditambahkan di Admin.</div>
            <?php else: foreach($fasilitasList as $item): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 border border-gray-100 group transform hover:-translate-y-2 flex flex-col">
                    <div class="h-64 bg-gray-100 relative overflow-hidden">
                        <?php if($item['gambar']): ?>
                            <img src="uploads/fasilitas/<?= htmlspecialchars($item['gambar']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200">Gambar Belum Tersedia</div>
                        <?php endif; ?>

                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur text-brand-navy text-xs font-bold px-4 py-2 rounded-full shadow-sm uppercase tracking-wider">
                            <?= htmlspecialchars($item['kategori'] ?? 'Fasilitas Kampus') ?>
                        </div>
                    </div>

                    <div class="p-8 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3 font-heading group-hover:text-brand-teal transition-colors"><?= htmlspecialchars($item['nama_fasilitas']) ?></h3>
                        <p class="text-gray-600 text-sm mb-6 leading-relaxed flex-grow"><?= nl2br(htmlspecialchars($item['deskripsi_singkat'])) ?></p>

                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center">
                            <span class="text-xs text-gray-500 font-medium mr-3">Kondisi Saat Ini:</span>
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full <?= $item['status'] == 'Publish' ? 'bg-green-100 text-green-700' : 'bg-brand-navy/10 text-brand-navy' ?>">
                                Tersedia
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
