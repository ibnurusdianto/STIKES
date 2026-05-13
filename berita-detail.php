<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$id = intval($_GET['id'] ?? 0);

$berita = null;

if (!empty($slug)) {
    $stmt = $conn->prepare("SELECT * FROM berita WHERE slug = ? AND status = 'Publish' LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $berita = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$berita && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM berita WHERE id = ? AND status = 'Publish' LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $berita = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$berita) {
    $pageTitle = 'Berita Tidak Ditemukan';
    require_once 'includes/header.php';
    require_once 'includes/navbar.php';
    ?>
    <div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
        <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">404</h2>
        </div>
    </div>
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4 text-center">
            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-brand-navy mb-4 font-heading">Berita Tidak Ditemukan</h3>
            <p class="text-gray-500 mb-8">Artikel yang Anda cari mungkin sudah dihapus atau belum dipublikasikan.</p>
            <a href="berita.php" class="inline-flex items-center px-6 py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-brand-teal-dark transition-colors shadow-lg">
                &larr; Kembali ke Daftar Berita
            </a>
        </div>
    </section>
    <?php
    require_once 'includes/footer.php';
    exit;
}

$pageTitle = $berita['judul'];

// Get related news (same category, exclude current)
$related = [];
$stmt = $conn->prepare("SELECT * FROM berita WHERE status='Publish' AND kategori=? AND id!=? ORDER BY created_at DESC LIMIT 3");
$stmt->bind_param("si", $berita['kategori'], $berita['id']);
$stmt->execute();
$related = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <div class="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm text-brand-teal font-bold mb-6 border border-white/10">
            <?= htmlspecialchars($berita['kategori']) ?>
        </div>
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight max-w-4xl mx-auto leading-tight">
            <?= htmlspecialchars($berita['judul']) ?>
        </h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium gap-6">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="opacity-50">/</span>
            <a href="berita.php" class="hover:text-brand-teal transition-colors">Berita</a>
            <span class="opacity-50">/</span>
            <span class="text-white">Detail</span>
        </div>
    </div>
</div>

<section class="py-16 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-4xl mx-auto">

            <!-- Meta Info Bar -->
            <div class="flex flex-wrap items-center gap-6 mb-10 pb-8 border-b border-gray-100">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="font-medium"><?= date('d F Y', strtotime($berita['created_at'])) ?></span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-5 h-5 text-brand-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="font-medium"><?= htmlspecialchars($berita['kategori']) ?></span>
                </div>
            </div>

            <!-- Thumbnail Image -->
            <?php if (!empty($berita['gambar_thumbnail'])): ?>
                <div class="mb-12 rounded-3xl overflow-hidden shadow-2xl shadow-gray-200/50 border border-gray-100">
                    <img src="uploads/berita/<?= htmlspecialchars($berita['gambar_thumbnail']) ?>" 
                         alt="<?= htmlspecialchars($berita['judul']) ?>" 
                         class="w-full max-h-[500px] object-cover">
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <article class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-16">
                <div class="text-lg leading-[1.9] space-y-6">
                    <?= nl2br(htmlspecialchars($berita['konten'])) ?>
                </div>
            </article>

            <!-- Share & Back Navigation -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-8 border-t border-gray-100">
                <a href="berita.php" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-navy text-white font-bold rounded-xl hover:bg-[#113a60] transition-colors shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Daftar Berita
                </a>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400 font-medium">Bagikan:</span>
                    <a href="https://wa.me/?text=<?= urlencode($berita['judul'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors shadow-md" title="Share via WhatsApp">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href); this.textContent='✓ Tersalin!'; setTimeout(()=>this.textContent='🔗', 2000)" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors" title="Copy Link">
                        🔗
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Related News -->
<?php if (!empty($related)): ?>
<section class="py-20 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center mb-12">
            <span class="text-brand-teal font-bold tracking-widest uppercase text-sm">Artikel Terkait</span>
            <h3 class="text-3xl font-bold text-brand-navy mt-2 font-heading">Berita Lainnya</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <?php foreach($related as $item): ?>
                <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col group transform hover:-translate-y-1">
                    <div class="h-48 bg-gray-100 relative overflow-hidden">
                        <?php if(!empty($item['gambar_thumbnail'])): ?>
                            <img src="uploads/berita/<?= htmlspecialchars($item['gambar_thumbnail']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="text-xs text-brand-teal font-bold mb-2">
                            <?= date('d M Y', strtotime($item['created_at'])) ?>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2 group-hover:text-brand-teal transition-colors font-heading">
                            <?= htmlspecialchars($item['judul']) ?>
                        </h4>
                        <a href="berita-detail.php?slug=<?= urlencode($item['slug']) ?>&id=<?= $item['id'] ?>" class="mt-auto text-brand-navy font-bold text-sm hover:text-brand-teal transition-colors flex items-center gap-1 group/link">
                            Baca Selengkapnya <span class="group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
