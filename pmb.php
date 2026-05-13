<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Informasi PMB';
$jadwalList = getAll($conn, 'pmb_jadwal', 'id ASC');

// Fetch PMB contact info
$kontakPMB = [
    'nama_pic' => 'Panitia PMB STIKES MRHJ',
    'no_telp' => '(021) 1234-5678',
    'no_whatsapp' => '0812-3456-7890',
    'email' => 'pmb@smrhj.ac.id',
    'alamat_sekretariat' => 'Kampus STIKES Mitra Ria Husada Jakarta, Jl. Karya Bhakti Cibubur, Jakarta Timur',
    'jam_operasional' => 'Senin - Jumat, 08:00 - 16:00 WIB',
    'link_pendaftaran' => '',
    'pesan_tambahan' => ''
];
try {
    $res_pmb = $conn->query("SELECT * FROM kontak_pmb WHERE id=1");
    if ($res_pmb && $res_pmb->num_rows > 0) {
        $kontakPMB = array_merge($kontakPMB, $res_pmb->fetch_assoc());
    }
} catch (Exception $e) {
    // Table may not exist yet
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Pendaftaran Mahasiswa Baru</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">PMB</span>
        </div>
    </div>
</div>

<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto text-center mb-16">
            <span class="text-brand-teal font-bold tracking-widest uppercase text-sm">Tahun Akademik <?= date('Y') ?>/<?= date('Y')+1 ?></span>
            <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-3 mb-6 font-heading">Jadwal Seleksi Penerimaan</h3>
            <p class="text-lg text-gray-600 leading-relaxed">Jadilah bagian dari generasi penerus tenaga kesehatan Indonesia yang kompeten dan berdedikasi tinggi. Temukan jalur masuk yang sesuai dengan Anda.</p>
        </div>

        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transform hover:-translate-y-1 transition-transform duration-500">
            <div class="overflow-x-auto pmb-table-wrapper">
                <table class="w-full text-left">
                    <thead class="bg-brand-navy text-white uppercase text-sm tracking-wider font-heading">
                        <tr>
                            <th class="px-8 py-6">Gelombang / Jalur</th>
                            <th class="px-8 py-6">Periode Pendaftaran</th>
                            <th class="px-8 py-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($jadwalList)): ?>
                            <tr><td colspan="3" class="px-8 py-10 text-center text-gray-500 italic">Jadwal pendaftaran belum tersedia di database.</td></tr>
                        <?php else: foreach($jadwalList as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-8 py-6 font-bold text-brand-navy text-lg"><?= htmlspecialchars($item['nama_gelombang']) ?></td>
                                <td class="px-8 py-6 text-gray-600 font-medium"><?= date('d M Y', strtotime($item['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($item['tanggal_selesai'])) ?></td>
                                <td class="px-8 py-6 text-center">
                                    <?php if($item['status'] == 'Buka'): ?>
                                        <span class="inline-flex items-center px-4 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200 shadow-sm">
                                            <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> DIBUKA
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-4 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200 shadow-sm">
                                            <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> DITUTUP
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-10 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Pendaftaran dapat dilakukan secara online melalui portal akademik mahasiswa baru maupun datang langsung ke sekretariat pendaftaran kampus SMRHJ.</p>
                <button onclick="openPMBModal()" class="inline-flex items-center justify-center px-8 py-4 bg-brand-teal text-white font-bold rounded-xl hover:bg-brand-teal-dark shadow-lg shadow-brand-teal/30 hover:shadow-brand-teal/50 transition-all transform hover:-translate-y-1 text-lg cursor-pointer">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    Hubungi Panitia PMB Sekarang
                </button>
            </div>
        </div>
    </div>
</section>

<!-- PMB Contact Modal -->
<div id="pmb-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-brand-navy/70 backdrop-blur-sm p-4 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300" id="pmb-modal-content">
        <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-brand-navy to-[#113a60] rounded-t-3xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-brand-teal/20 rounded-2xl flex items-center justify-center mb-4 border border-brand-teal/30">
                    <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold font-heading">Hubungi Panitia PMB</h3>
                <p class="text-gray-300 text-sm mt-1"><?= htmlspecialchars($kontakPMB['nama_pic']) ?></p>
            </div>
            <button onclick="closePMBModal()" class="absolute top-6 right-6 text-white/60 hover:text-white text-2xl font-bold z-10">&times;</button>
        </div>

        <div class="p-8 space-y-6">
            <?php if (!empty($kontakPMB['no_telp'])): ?>
            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $kontakPMB['no_telp'])) ?>" class="flex items-center gap-5 p-4 bg-slate-50 rounded-2xl hover:bg-brand-teal/5 hover:border-brand-teal border border-transparent transition-all group">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Telepon</p>
                    <p class="text-lg font-bold text-gray-800"><?= htmlspecialchars($kontakPMB['no_telp']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['no_whatsapp'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $kontakPMB['no_whatsapp']) ?>" target="_blank" class="flex items-center gap-5 p-4 bg-green-50 rounded-2xl hover:bg-green-100 border border-green-100 hover:border-green-300 transition-all group">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-green-500/30">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-green-600 font-bold uppercase tracking-wider">WhatsApp</p>
                    <p class="text-lg font-bold text-gray-800"><?= htmlspecialchars($kontakPMB['no_whatsapp']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($kontakPMB['email']) ?>" class="flex items-center gap-5 p-4 bg-slate-50 rounded-2xl hover:bg-brand-teal/5 border border-transparent hover:border-brand-teal transition-all group">
                <div class="w-12 h-12 bg-brand-navy/10 rounded-xl flex items-center justify-center text-brand-navy group-hover:bg-brand-navy group-hover:text-white transition-colors shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Email</p>
                    <p class="text-lg font-bold text-gray-800"><?= htmlspecialchars($kontakPMB['email']) ?></p>
                </div>
            </a>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['alamat_sekretariat'])): ?>
            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div>
                        <p class="text-xs text-amber-700 font-bold uppercase tracking-wider mb-1">Sekretariat Pendaftaran</p>
                        <p class="text-sm text-gray-700 leading-relaxed"><?= htmlspecialchars($kontakPMB['alamat_sekretariat']) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['jam_operasional'])): ?>
            <div class="text-center pt-2">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Jam Layanan</p>
                <p class="text-sm font-semibold text-gray-600"><?= htmlspecialchars($kontakPMB['jam_operasional']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['pesan_tambahan'])): ?>
            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 text-sm text-blue-800">
                <?= nl2br(htmlspecialchars($kontakPMB['pesan_tambahan'])) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($kontakPMB['link_pendaftaran'])): ?>
            <a href="<?= htmlspecialchars($kontakPMB['link_pendaftaran']) ?>" target="_blank" class="block w-full py-4 bg-brand-teal text-white text-center font-bold rounded-xl hover:bg-brand-teal-dark shadow-lg shadow-brand-teal/30 transition-all transform hover:-translate-y-1 text-lg">
                Daftar Online Sekarang &rarr;
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function openPMBModal() {
    const modal = document.getElementById('pmb-modal');
    const content = document.getElementById('pmb-modal-content');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closePMBModal() {
    const modal = document.getElementById('pmb-modal');
    const content = document.getElementById('pmb-modal-content');
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

// Close on backdrop click
document.getElementById('pmb-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closePMBModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePMBModal();
});
</script>

<?php require_once 'includes/footer.php'; ?>
