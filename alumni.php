<?php
require_once 'admin/config/database.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle = 'Alumni';

// Handle alumni form submission
$formMsg = '';
$formType = '';

// Check for redirect-back success
if (isset($_SESSION['alumni_success'])) {
    $formMsg = $_SESSION['alumni_success'];
    $formType = 'success';
    unset($_SESSION['alumni_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alumni_form'])) {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $prodi_id = intval($_POST['prodi_id'] ?? 0);
    $tahun_masuk = intval($_POST['tahun_masuk'] ?? 0);
    $tahun_lulus = intval($_POST['tahun_lulus'] ?? 0);
    $no_ijazah = trim($_POST['no_ijazah'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat_sekarang'] ?? '');
    $pekerjaan = trim($_POST['pekerjaan_sekarang'] ?? '');
    $instansi = trim($_POST['instansi_kerja'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    // Validation
    if (empty($nama) || empty($nim) || $prodi_id < 1 || $tahun_masuk < 1990 || $tahun_lulus < 1990 || empty($email) || empty($no_hp) || empty($jenis_kelamin)) {
        $formMsg = 'Harap lengkapi semua field yang wajib diisi (bertanda *).';
        $formType = 'error';
    } elseif ($tahun_lulus < $tahun_masuk) {
        $formMsg = 'Tahun lulus tidak boleh lebih kecil dari tahun masuk.';
        $formType = 'error';
    } else {
        try {
            // Check duplicate NIM
            $chk = $conn->prepare("SELECT id FROM alumni_form WHERE nim = ?");
            $chk->bind_param("s", $nim);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $formMsg = 'NIM ini sudah terdaftar dalam sistem alumni kami. Jika Anda merasa ini adalah kesalahan, silakan hubungi admin.';
                $formType = 'error';
            } else {
                $stmt = $conn->prepare("INSERT INTO alumni_form (nama_lengkap, nim, prodi_id, tahun_masuk, tahun_lulus, no_ijazah, tempat_lahir, tanggal_lahir, jenis_kelamin, email, no_hp, alamat_sekarang, pekerjaan_sekarang, instansi_kerja, jabatan, pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiissssssssssss", $nama, $nim, $prodi_id, $tahun_masuk, $tahun_lulus, $no_ijazah, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $email, $no_hp, $alamat, $pekerjaan, $instansi, $jabatan, $pesan);
                if ($stmt->execute()) {
                    $_SESSION['alumni_success'] = 'Data alumni Anda berhasil dikirim! Tim kami akan memverifikasi data Anda dalam 1-3 hari kerja.';
                    header("Location: alumni.php#form-alumni");
                    exit;
                } else {
                    $formMsg = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi admin.';
                    $formType = 'error';
                }
                $stmt->close();
            }
            $chk->close();
        } catch (Exception $e) {
            $formMsg = 'Sistem pendataan alumni belum siap. Silakan hubungi admin untuk menjalankan migrasi database.';
            $formType = 'error';
        }
    }
}

$alumniQuery = "SELECT a.*, p.nama_prodi as prodi FROM alumni_testimoni a LEFT JOIN program_studi p ON a.prodi_id = p.id WHERE a.status='Publish' ORDER BY a.urutan ASC, a.id DESC";
$res = $conn->query($alumniQuery);
$alumniList = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

// Also include verified alumni from registration form who left a message
try {
    $verifiedQuery = "SELECT af.nama_lengkap, af.pesan, af.pekerjaan_sekarang, af.tahun_lulus, af.prodi_id, p.nama_prodi as prodi FROM alumni_form af LEFT JOIN program_studi p ON af.prodi_id = p.id WHERE af.status='Verified' AND af.pesan IS NOT NULL AND af.pesan != '' ORDER BY af.created_at DESC";
    $resV = $conn->query($verifiedQuery);
    if ($resV) {
        while ($v = $resV->fetch_assoc()) {
            $alumniList[] = [
                'nama_alumni' => $v['nama_lengkap'],
                'pesan_testimoni' => $v['pesan'],
                'pekerjaan_sekarang' => $v['pekerjaan_sekarang'] ?? '',
                'tahun_lulus' => $v['tahun_lulus'],
                'prodi' => $v['prodi'] ?? 'SMRHJ',
                'foto' => '',
                '_from_form' => true
            ];
        }
    }
} catch (Exception $e) {}

// Get prodi list for the form
$prodi_list = [];
$res_prodi = $conn->query("SELECT id, nama_prodi, jenjang FROM program_studi ORDER BY jenjang, nama_prodi");
if ($res_prodi) $prodi_list = $res_prodi->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="bg-brand-navy relative pt-24 pb-20 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full border-8 border-brand-teal"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full border-8 border-white"></div>
    </div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 font-heading tracking-tight">Kisah Sukses Alumni</h2>
        <div class="flex items-center justify-center text-sm text-gray-300 font-medium">
            <a href="home.php" class="hover:text-brand-teal transition-colors">Beranda</a>
            <span class="mx-3 opacity-50">/</span>
            <span class="text-white">Alumni</span>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto text-center mb-20">
            <span class="text-brand-teal font-bold tracking-widest uppercase text-sm">Jejak Langkah</span>
            <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-3 mb-6 font-heading">Suara Mereka yang Telah Berhasil</h3>
            <p class="text-lg text-gray-600">Simak testimoni dan cerita sukses para lulusan STIKES Mitra Ria Husada Jakarta yang kini telah berkiprah nyata di dunia kesehatan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php if(empty($alumniList)): ?>
                <div class="col-span-full text-center py-20 bg-slate-50 rounded-3xl text-gray-500 border border-dashed border-gray-200">Data testimoni alumni belum diinput di CMS.</div>
            <?php else: foreach($alumniList as $item): ?>
                <div class="bg-slate-50 rounded-3xl p-10 border border-gray-100 relative group hover:bg-brand-navy hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col">

                    <svg class="w-16 h-16 text-brand-teal/20 absolute top-8 right-8 group-hover:text-brand-teal/30 transition-colors duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>

                    <p class="text-gray-600 mb-10 relative z-10 italic leading-relaxed group-hover:text-gray-300 transition-colors duration-500 flex-grow text-lg">"<?= nl2br(htmlspecialchars($item['pesan_testimoni'])) ?>"</p>

                    <div class="flex items-center mt-auto border-t border-gray-200 group-hover:border-white/10 pt-6 transition-colors duration-500">
                        <div class="w-16 h-16 bg-gray-200 rounded-full overflow-hidden mr-5 border-2 border-white shadow-md group-hover:border-brand-teal transition-colors">
                            <?php if(!empty($item['foto'])): ?>
                                <img src="uploads/alumni/<?= htmlspecialchars($item['foto']) ?>" alt="Foto <?= htmlspecialchars($item['nama_alumni']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-white">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 group-hover:text-white transition-colors duration-500 font-heading"><?= htmlspecialchars($item['nama_alumni']) ?></h4>
                            <p class="text-sm text-brand-teal font-bold mb-1"><?= htmlspecialchars($item['pekerjaan_sekarang']) ?></p>
                            <p class="text-xs text-gray-500 group-hover:text-gray-400 transition-colors duration-500 uppercase tracking-wider font-medium">Lulusan <?= htmlspecialchars($item['prodi'] ?? 'SMRHJ') ?> (<?= htmlspecialchars($item['tahun_lulus']) ?>)</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<!-- Alumni Registration Form Section -->
<section class="py-24 bg-slate-50 border-t border-gray-100" id="form-alumni">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-brand-teal font-bold tracking-widest uppercase text-sm">Formulir Pendaftaran</span>
                <h3 class="text-3xl md:text-4xl font-bold text-brand-navy mt-3 mb-4 font-heading">Pendataan Alumni</h3>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Jika Anda merupakan alumni STIKES Mitra Ria Husada Jakarta, silakan isi formulir berikut untuk pendataan. Data Anda akan diverifikasi oleh tim kami.</p>
            </div>

            <?php if ($formMsg): ?>
            <div class="mb-8 p-5 rounded-2xl border <?= $formType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?> flex items-start gap-3">
                <?php if ($formType === 'success'): ?>
                    <svg class="w-6 h-6 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <?php else: ?>
                    <svg class="w-6 h-6 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                <?php endif; ?>
                <p class="font-medium"><?= htmlspecialchars($formMsg) ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="alumni.php#form-alumni" class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 bg-gradient-to-r from-brand-navy to-[#113a60] text-white">
                    <h4 class="text-xl font-bold font-heading flex items-center gap-3">
                        <svg class="w-6 h-6 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Data Identitas Alumni
                    </h4>
                    <p class="text-gray-300 text-sm mt-1">Field bertanda <span class="text-red-400">*</span> wajib diisi. NIM dan No. Ijazah digunakan untuk memverifikasi bahwa Anda benar alumni SMRHJ.</p>
                </div>

                <div class="p-8 space-y-6">
                    <input type="hidden" name="alumni_form" value="1">

                    <!-- Identity Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal outline-none transition-all" placeholder="Nama sesuai ijazah">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIM (Nomor Induk Mahasiswa) <span class="text-red-500">*</span></label>
                            <input type="text" name="nim" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal outline-none transition-all" placeholder="cth: 2019010001">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Program Studi <span class="text-red-500">*</span></label>
                            <select name="prodi_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none bg-white">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach($prodi_list as $pl): ?>
                                    <option value="<?= $pl['id'] ?>"><?= htmlspecialchars($pl['jenjang'] . ' - ' . $pl['nama_prodi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">No. Ijazah</label>
                            <input type="text" name="no_ijazah" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="Nomor ijazah (untuk verifikasi)">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Masuk <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_masuk" min="1990" max="<?= date('Y') ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none" placeholder="cth: 2019">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun Lulus <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun_lulus" min="1990" max="<?= date('Y') ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none" placeholder="cth: 2023">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none bg-white">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="cth: Jakarta">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all">
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="text-lg font-bold text-brand-navy mb-4 font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Kontak & Pekerjaan Saat Ini
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Aktif <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="email@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="08xx-xxxx-xxxx">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Tinggal Sekarang</label>
                        <textarea name="alamat_sekarang" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all resize-none" placeholder="Alamat domisili saat ini..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pekerjaan Saat Ini</label>
                            <input type="text" name="pekerjaan_sekarang" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="cth: Perawat">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Instansi / Tempat Kerja</label>
                            <input type="text" name="instansi_kerja" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="cth: RSUD Jakarta">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan</label>
                            <input type="text" name="jabatan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all" placeholder="cth: Kepala Perawat">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pesan / Kesan untuk Kampus</label>
                        <textarea name="pesan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-teal outline-none transition-all resize-none" placeholder="Tuliskan kesan dan pesan Anda selama berkuliah di STIKES MRHJ (opsional)..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="px-8 py-4 bg-brand-teal hover:bg-brand-teal-dark text-white font-bold rounded-xl shadow-lg shadow-brand-teal/30 hover:shadow-brand-teal/50 transition-all transform hover:-translate-y-1 text-lg">
                            Kirim Data Alumni
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
