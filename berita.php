<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Berita & Informasi';
$kategori = $_GET['kategori'] ?? '';

$query = "SELECT * FROM berita WHERE status = 'Publish'";
$params = [];
$types = "";

if ($kategori) {
    $query .= " AND kategori = ?";
    $params[] = $kategori;
    $types .= "s";
}
$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$beritaList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Berita & Pengumuman</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Berita</span>
        </div>
    </div>
</div>

<section class="py-24 bg-slate-50 min-h-[50vh]">
    <div class="container mx-auto px-4 md:px-6">

        <div class="flex flex-wrap gap-4 mb-16 justify-center">
            <a href="berita.php" class="px-6 py-2.5 <?= empty($kategori) ? 'bg-brand-navy text-white shadow-md' : 'bg-white text-gray-600 hover:text-brand-navy hover:bg-gray-50 border border-gray-200' ?> rounded-full text-sm font-bold transition-all">Semua Berita</a>

            <a href="berita.php?kategori=Akademik" class="px-6 py-2.5 <?= $kategori == 'Akademik' ? 'bg-brand-teal text-white shadow-md' : 'bg-white text-gray-600 hover:text-brand-teal hover:bg-gray-50 border border-gray-200' ?> rounded-full text-sm font-bold transition-all">Akademik</a>

            <a href="berita.php?kategori=Prestasi" class="px-6 py-2.5 <?= $kategori == 'Prestasi' ? 'bg-brand-teal text-white shadow-md' : 'bg-white text-gray-600 hover:text-brand-teal hover:bg-gray-50 border border-gray-200' ?> rounded-full text-sm font-bold transition-all">Prestasi</a>

            <a href="berita.php?kategori=Pengumuman" class="px-6 py-2.5 <?= $kategori == 'Pengumuman' ? 'bg-brand-teal text-white shadow-md' : 'bg-white text-gray-600 hover:text-brand-teal hover:bg-gray-50 border border-gray-200' ?> rounded-full text-sm font-bold transition-all">Pengumuman</a>

            <a href="berita.php?kategori=Kemahasiswaan" class="px-6 py-2.5 <?= $kategori == 'Kemahasiswaan' ? 'bg-brand-teal text-white shadow-md' : 'bg-white text-gray-600 hover:text-brand-teal hover:bg-gray-50 border border-gray-200' ?> rounded-full text-sm font-bold transition-all">Kemahasiswaan</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($beritaList)): ?>
                <div class="col-span-full flex flex-col items-center justify-center text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-brand-navy mb-2">Tidak ada berita ditemukan</h3>
                    <p class="text-gray-500">Kategori berita ini belum memiliki artikel yang dipublikasi.</p>
                </div>
            <?php else: foreach($beritaList as $item): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col group transform hover:-translate-y-1">
                    <div class="h-56 bg-gray-100 relative overflow-hidden">
                        <?php if(!empty($item['gambar_thumbnail'])): ?>
                            <img src="uploads/berita/<?= htmlspecialchars($item['gambar_thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200">No Image</div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur text-brand-navy text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                            <?= htmlspecialchars($item['kategori']) ?>
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="text-xs text-brand-teal font-bold mb-3 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3 leading-tight font-heading group-hover:text-brand-teal transition-colors line-clamp-2">
                            <?= htmlspecialchars($item['judul']) ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3 leading-relaxed">
                            <?= htmlspecialchars(strip_tags($item['konten'])) ?>
                        </p>
                        <div class="mt-auto pt-4 border-t border-gray-50">
                            <a href="berita-detail.php?slug=<?= urlencode($item['slug'] ?? '') ?>&id=<?= $item['id'] ?>" class="inline-flex items-center font-bold text-brand-navy hover:text-brand-teal transition-colors group/link">
                                Baca Selengkapnya <span class="ml-1 transform group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
