<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Kontak Kami';

// Fetch contact info from database
$kontak = [
    'alamat' => 'Jl. Raya Kampus Kesehatan No. 123, Jakarta', 
    'email' => 'info@stikesmrhj.ac.id', 
    'telp' => '(021) 12345678', 
    'wa' => '0812-3456-7890', 
    'jam_operasional' => 'Senin - Jumat, 08:00 - 16:00 WIB'
];
try {
    $res = $conn->query("SELECT * FROM kontak WHERE id=1");
    if ($res && $res->num_rows > 0) {
        $kontak = array_merge($kontak, $res->fetch_assoc());
    }
} catch (Exception $e) {}

// Handle contact form submission
$formMsg = '';
$formType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pesan'])) {
    $nama = trim($_POST['nama_pengirim'] ?? '');
    $email = trim($_POST['email_pengirim'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($pesan)) {
        $formMsg = 'Harap isi Nama, Email, dan Pesan Anda.';
        $formType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formMsg = 'Format email tidak valid. Silakan periksa kembali.';
        $formType = 'error';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO pesan_kontak (nama_pengirim, email_pengirim, subjek, pesan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);
            if ($stmt->execute()) {
                $formMsg = 'Pesan Anda berhasil dikirim! Tim kami akan segera merespons melalui email Anda.';
                $formType = 'success';
            } else {
                $formMsg = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
                $formType = 'error';
            }
            $stmt->close();
        } catch (Exception $e) {
            $formMsg = 'Sistem pesan belum siap. Silakan hubungi kami langsung melalui email atau telepon.';
            $formType = 'error';
        }
    }
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
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Hubungi Kami</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Kontak</span>
        </div>
    </div>
</div>

<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">

            <div class="bg-white rounded-3xl p-10 md:p-12 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-teal/5 rounded-bl-full -mr-4 -mt-4"></div>

                <h3 class="text-3xl font-bold text-brand-navy mb-10 font-heading relative z-10">Informasi Kampus</h3>

                <div class="space-y-8 relative z-10">
                    <div class="flex items-start group">
                        <div class="w-14 h-14 bg-brand-teal/10 rounded-2xl flex items-center justify-center text-brand-teal shrink-0 mr-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg mb-2">Alamat Utama</h4>
                            <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($kontak['alamat'])) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-14 h-14 bg-brand-navy/10 rounded-2xl flex items-center justify-center text-brand-navy shrink-0 mr-6 group-hover:bg-brand-navy group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg mb-2">Email Layanan</h4>
                            <a href="mailto:<?= htmlspecialchars($kontak['email']) ?>" class="text-brand-teal font-bold text-lg hover:underline"><?= htmlspecialchars($kontak['email']) ?></a>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-14 h-14 bg-brand-teal/10 rounded-2xl flex items-center justify-center text-brand-teal shrink-0 mr-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg mb-2">Telepon & WhatsApp</h4>
                            <p class="text-gray-600 mb-1 text-lg">Telp: <span class="font-semibold"><?= htmlspecialchars($kontak['telp']) ?></span></p>
                            <p class="text-gray-600 text-lg">WA PMB: <span class="font-semibold"><?= htmlspecialchars($kontak['wa']) ?></span></p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-14 h-14 bg-brand-navy/10 rounded-2xl flex items-center justify-center text-brand-navy shrink-0 mr-6 group-hover:bg-brand-navy group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg mb-2">Jam Layanan Operasional</h4>
                            <p class="text-gray-600 font-medium"><?= htmlspecialchars($kontak['jam_operasional']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-brand-navy rounded-3xl p-10 md:p-12 shadow-2xl text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <h3 class="text-3xl font-bold mb-3 font-heading relative z-10">Tinggalkan Pesan</h3>
                <p class="text-gray-400 text-sm mb-8 relative z-10">Kami akan merespons pesan Anda melalui email dalam 1-2 hari kerja.</p>

                <?php if ($formMsg): ?>
                <div class="mb-6 p-4 rounded-xl relative z-10 <?= $formType === 'success' ? 'bg-green-500/20 border border-green-400/30 text-green-200' : 'bg-red-500/20 border border-red-400/30 text-red-200' ?>">
                    <div class="flex items-center gap-3">
                        <?php if ($formType === 'success'): ?>
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                        <?php endif; ?>
                        <p class="text-sm font-medium"><?= htmlspecialchars($formMsg) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <form action="kontak.php" method="POST" class="space-y-5 relative z-10">
                    <input type="hidden" name="kirim_pesan" value="1">
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-300">Nama Lengkap Anda <span class="text-red-400">*</span></label>
                        <input type="text" name="nama_pengirim" required class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-brand-teal outline-none text-white placeholder-gray-500 transition-all shadow-inner" placeholder="Cth: Budi Santoso">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-300">Alamat Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email_pengirim" required class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-brand-teal outline-none text-white placeholder-gray-500 transition-all shadow-inner" placeholder="budi@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-300">Subjek / Perihal</label>
                        <input type="text" name="subjek" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-brand-teal outline-none text-white placeholder-gray-500 transition-all shadow-inner" placeholder="cth: Pertanyaan tentang pendaftaran PMB">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-gray-300">Isi Pesan <span class="text-red-400">*</span></label>
                        <textarea name="pesan" rows="5" required class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl focus:bg-white/10 focus:border-brand-teal outline-none text-white placeholder-gray-500 transition-all shadow-inner resize-none" placeholder="Tuliskan pertanyaan Anda mengenai pendaftaran, dll..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-brand-teal hover:bg-brand-teal-dark font-bold rounded-xl shadow-lg shadow-brand-teal/30 hover:shadow-brand-teal/50 transition-all transform hover:-translate-y-1 mt-2 text-lg tracking-wide cursor-pointer">
                        Kirim Pesan Sekarang
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
