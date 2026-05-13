<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Akademik';

$prodiList = [];
$res = $conn->query("SELECT * FROM program_studi WHERE status='Publish' ORDER BY id ASC");
if ($res) $prodiList = $res->fetch_all(MYSQLI_ASSOC);

// Get contact info for the modal
$kontak = [
    'email' => 'info@stikesmrhj.ac.id',
    'telp' => '(021) 12345678',
    'wa' => '0812-3456-7890',
    'jam_operasional' => 'Senin - Jumat, 08:00 - 16:00 WIB'
];
$res_kontak = $conn->query("SELECT * FROM kontak WHERE id=1");
if ($res_kontak && $res_kontak->num_rows > 0) {
    $kontak = array_merge($kontak, $res_kontak->fetch_assoc());
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Program Akademik</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Akademik</span>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto text-center mb-20">
            <span class="text-brand-teal font-bold tracking-widest uppercase text-sm">Fakultas & Program Studi</span>
            <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-3 mb-6 font-heading">Pendidikan Berkualitas untuk Masa Depan Kesehatan</h3>
            <p class="text-lg text-gray-600 leading-relaxed">STIKES Mitra Ria Husada Jakarta menyelenggarakan program pendidikan kesehatan berstandar nasional yang dirancang untuk menghasilkan lulusan yang kompeten dan siap kerja secara global.</p>
        </div>

        <?php if(empty($prodiList)): ?>
            <div class="text-center text-gray-500 py-16 border-2 border-dashed border-gray-200 rounded-2xl max-w-xl mx-auto">
                Belum ada program studi yang dipublikasikan.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl mx-auto">
                <?php foreach($prodiList as $index => $prodi): 
                    $accentColor = $index % 2 === 0 ? 'brand-teal' : 'brand-navy';
                ?>
                <div id="<?= htmlspecialchars($prodi['slug']) ?>" class="border border-gray-100 rounded-3xl p-10 shadow-lg hover:shadow-2xl transition-all duration-300 bg-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-<?= $accentColor ?>/20 to-transparent rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-125"></div>

                    <h4 class="text-2xl font-bold text-brand-navy mb-3 relative z-10 font-heading"><?= htmlspecialchars($prodi['jenjang'] . ' ' . $prodi['nama_prodi']) ?></h4>
                    <div class="inline-flex items-center px-4 py-1.5 bg-<?= $accentColor ?>/10 text-<?= $accentColor === 'brand-teal' ? 'brand-teal-dark' : 'brand-navy' ?> text-sm font-bold rounded-full mb-8 relative z-10 shadow-sm border border-<?= $accentColor ?>/20">
                        <span class="w-2 h-2 rounded-full bg-<?= $accentColor ?> mr-2"></span> Akreditasi <?= htmlspecialchars($prodi['akreditasi']) ?>
                    </div>

                    <?php if(!empty($prodi['deskripsi'])): ?>
                        <p class="text-gray-600 mb-8 relative z-10 leading-relaxed"><?= nl2br(htmlspecialchars($prodi['deskripsi'])) ?></p>
                    <?php endif; ?>

                    <?php if(!empty($prodi['visi'])): ?>
                        <div class="mb-6 relative z-10">
                            <h5 class="text-sm font-bold text-brand-navy uppercase tracking-wider mb-2">Visi</h5>
                            <p class="text-gray-600 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($prodi['visi'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($prodi['misi'])): ?>
                        <div class="mb-8 relative z-10">
                            <h5 class="text-sm font-bold text-brand-navy uppercase tracking-wider mb-2">Misi</h5>
                            <div class="space-y-2 text-sm text-gray-700">
                                <?php foreach(explode("\n", $prodi['misi']) as $misiItem): 
                                    $misiItem = trim($misiItem);
                                    if(empty($misiItem)) continue;
                                ?>
                                <div class="flex items-start">
                                    <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0 shadow-sm border border-gray-100">
                                        <svg class="w-3.5 h-3.5 text-<?= $accentColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <?= htmlspecialchars($misiItem) ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button onclick="openProdiModal('<?= htmlspecialchars(addslashes($prodi['jenjang'] . ' ' . $prodi['nama_prodi'])) ?>')" class="inline-flex items-center text-<?= $accentColor ?> font-bold hover:text-brand-navy transition-colors relative z-10 group/link cursor-pointer">
                        Hubungi Prodi <span class="ml-2 transform group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Hubungi Prodi Modal -->
<div id="prodi-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-brand-navy/70 backdrop-blur-sm p-4 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300" id="prodi-modal-content">
        <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-brand-navy to-[#113a60] rounded-t-3xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-brand-teal/20 rounded-2xl flex items-center justify-center mb-4 border border-brand-teal/30">
                    <svg class="w-7 h-7 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-xl font-bold font-heading" id="prodi-modal-title">Hubungi Program Studi</h3>
                <p class="text-gray-300 text-sm mt-1">Informasi kontak untuk pertanyaan seputar program studi.</p>
            </div>
            <button onclick="closeProdiModal()" class="absolute top-6 right-6 text-white/60 hover:text-white text-2xl font-bold z-10">&times;</button>
        </div>

        <div class="p-8 space-y-5">
            <?php if (!empty($kontak['telp'])): ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $kontak['telp']) ?>" class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl hover:bg-brand-teal/5 hover:border-brand-teal border border-transparent transition-all group">
                <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Telepon Kampus</p>
                    <p class="text-base font-bold text-gray-800"><?= htmlspecialchars($kontak['telp']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontak['wa'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $kontak['wa']) ?>" target="_blank" class="flex items-center gap-4 p-4 bg-green-50 rounded-2xl hover:bg-green-100 border border-green-100 hover:border-green-300 transition-all group">
                <div class="w-11 h-11 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-green-500/30">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-green-600 font-bold uppercase tracking-wider">WhatsApp</p>
                    <p class="text-base font-bold text-gray-800"><?= htmlspecialchars($kontak['wa']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontak['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($kontak['email']) ?>" class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl hover:bg-brand-teal/5 border border-transparent hover:border-brand-teal transition-all group">
                <div class="w-11 h-11 bg-brand-navy/10 rounded-xl flex items-center justify-center text-brand-navy group-hover:bg-brand-navy group-hover:text-white transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Email</p>
                    <p class="text-base font-bold text-gray-800"><?= htmlspecialchars($kontak['email']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontak['jam_operasional'])): ?>
            <div class="text-center pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Jam Layanan</p>
                <p class="text-sm font-semibold text-gray-600"><?= htmlspecialchars($kontak['jam_operasional']) ?></p>
            </div>
            <?php endif; ?>

            <a href="kontak.php" class="block w-full py-3.5 bg-brand-navy text-white text-center font-bold rounded-xl hover:bg-[#113a60] transition-colors text-sm">
                Lihat Halaman Kontak Lengkap &rarr;
            </a>
        </div>
    </div>
</div>

<script>
function openProdiModal(prodiName) {
    const modal = document.getElementById('prodi-modal');
    const content = document.getElementById('prodi-modal-content');
    document.getElementById('prodi-modal-title').textContent = 'Hubungi ' + prodiName;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeProdiModal() {
    const modal = document.getElementById('prodi-modal');
    const content = document.getElementById('prodi-modal-content');
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

document.getElementById('prodi-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeProdiModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProdiModal();
});
</script>

<?php require_once 'includes/footer.php'; ?>
